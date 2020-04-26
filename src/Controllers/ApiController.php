<?php

namespace SpaceCode\Maia\Controllers;

use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SpaceCode\Maia\Maia;
use SpaceCode\Maia\Models\Comment;
use Validator;

class ApiController extends Controller
{
    /**
     * @param Request $request
     * @param Maia $maia
     * @return JsonResponse
     */
    public function getComments(Request $request, Maia $maia)
    {
        $resource = $request->input('viaResource')::where(['id' => $request->input('viaResourceId'), 'status' => 'published', 'deleted_at' => null])->first();
        if($resource) {
            $list = $resource->commentsList;
            if($list->count()) {
                $list = $list->where('created_at', '>=', Carbon::now()->subDays(setting('comments_autoClose'))->toDateTimeString());
                if(setting('comments_display') === 'newer') {
                    $list = $list->sortByDesc('created_at')->values()->all();
                }
            }
            if($list->count()) {
                $commentList = [];
                $avatar = 'https://secure.gravatar.com/avatar';
                foreach ($list as $comment) {
                    if($comment->status === 'published' && $comment->deleted_at === null && $comment->parent_id === null) {
                        $author = !is_null($comment->author_id) ? User::where('id', $comment->author_id)->first() : null;
                        if($author && !is_null($author->avatar)) {
                            $avatar = !is_null($author->avatar) ? $maia->image($author->avatar) : $avatar . md5($author->email) .  '?size=512';
                        }
                        $commentList[] = (object)[
                            'id' => $comment->id,
                            'user' => (object)[
                                'name' => $author ? $author->getName() : 'guest',
                                'avatar' => $avatar
                            ],
                            'children' => $this->getChildren($comment->id, $maia),
                            'body' => $comment->body,
                            'created_at' => $comment->created_at->diffForHumans()
                        ];
                    }
                }
            }
        }
        $comments = (object)[
            'auth' => setting('comments_userLoggedIn') === 1 ? true : false,
            'nested' => is_null(setting('comments_nested')) ? 0 : intval(setting('comments_nested')),
            'list' => $commentList ?? []
        ];
        return response()->json(['comments' => $comments], 200);
    }

    /**
     * @param Request $request
     * @param Maia $maia
     * @return JsonResponse
     * @throws Exception
     */
    public function postComments(Request $request, Maia $maia)
    {
        $val = Validator::make(
            $request->input('comment'),
            [
                'text' => 'required|min:3'
            ],
            [
                'text.required' => 'Comment field is required',
                'text.min' => 'Comment field must be at least 3 characters'
            ]
        );
        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()], 401);
        } else {

            // Create Comment
            $authorId = !Auth::check() ? null : Auth::id();
            if(setting('comments_userLoggedIn')) {
                $authorId = Auth::id();
            }
            $status = setting('comments_confirmed') === 1 ? 'pending' : 'published';
            $success = $status === 'published' ? 'Your comment has been published.' : 'Your comment has been submitted for review.';
            $comment_id = Comment::insertGetId([
                'author_id' => $authorId,
                'guard_name' => 'web',
                'parent_id' => $request->input('comment')['id'] === 0 ? null : $request->input('comment')['id'],
                'body' => strip_tags($request->input('comment')['text'], ['<code>', '<br>', '<em>', '<strong>', '<b>']),
                'status' => $status,
                'created_at' => new DateTime(),
                'updated_at' => new DateTime()
            ]);
            DB::table('comments_relationships')->insert(['comment_id' => $comment_id, 'item_id' => $request->input('viaResourceId'), 'type' => strtolower(class_basename($request->input('viaResource')))]);

            // Get Comments
            $resource = $request->input('viaResource')::where(['id' => $request->input('viaResourceId'), 'status' => 'published', 'deleted_at' => null])->first();
            if($resource) {
                $list = $resource->commentsList;
                if($list->count()) {
                    $list = $list->where('created_at', '>=', Carbon::now()->subDays(setting('comments_autoClose'))->toDateTimeString());
                    if(setting('comments_display') === 'newer') {
                        $list = $list->sortByDesc('created_at')->values()->all();
                    }
                }
                if($list->count()) {
                    $commentList = [];
                    $avatar = 'https://secure.gravatar.com/avatar';
                    foreach ($list as $comment) {
                        if($comment->status === 'published' && $comment->deleted_at === null && $comment->parent_id === null) {
                            $author = !is_null($comment->author_id) ? User::where('id', $comment->author_id)->first() : null;
                            if($author && !is_null($author->avatar)) {
                                $avatar = !is_null($author->avatar) ? $maia->image($author->avatar) : $avatar . md5($author->email) .  '?size=512';
                            }
                            $commentList[] = (object)[
                                'id' => $comment->id,
                                'user' => (object)[
                                    'name' => $author ? $author->getName() : 'guest',
                                    'avatar' => $avatar
                                ],
                                'children' => $this->getChildren($comment->id, $maia),
                                'body' => $comment->body,
                                'created_at' => $comment->created_at->diffForHumans()
                            ];
                        }
                    }
                }
            }
            $comments = (object)[
                'auth' => setting('comments_userLoggedIn') === 1 ? true : false,
                'nested' => is_null(setting('comments_nested')) ? 0 : intval(setting('comments_nested')),
                'list' => $commentList ?? []
            ];

            return response()->json(['success' => $success, 'comments' => $comments], 200);
        }
    }

    /**
     * @param $id
     * @param $maia
     * @return array|null
     */
    public function getChildren($id, $maia) {
        if(!is_null($id)) {
            $children = Comment::where(['id' => $id, 'status' => 'published', 'deleted_at' => null])->first()->children;
            if($children->count()) {
                if(setting('comments_display') === 'newer') {
                    $children = $children->sortByDesc('created_at')->values()->all();
                }
            }
            if($children->count()) {
                $commentList = [];
                $avatar = 'https://secure.gravatar.com/avatar';
                foreach ($children as $comment) {
                    if($comment->status === 'published' && $comment->deleted_at === null) {
                        $author = !is_null($comment->author_id) ? User::where('id', $comment->author_id)->first() : null;
                        if($author && !is_null($author->avatar)) {
                            $avatar = !is_null($author->avatar) ? $maia->image($author->avatar) : $avatar . md5($author->email) .  '?size=512';
                        }
                        $commentList[] = (object)[
                            'id' => $comment->id,
                            'user' => (object)[
                                'name' => $author ? $author->getName() : 'guest',
                                'avatar' => $avatar
                            ],
                            'children' => $this->getChildren($comment->id, $maia),
                            'body' => $comment->body,
                            'created_at' => $comment->created_at->diffForHumans()
                        ];
                    }
                }
                return $commentList;
            }
        }
        return [];
    }
}