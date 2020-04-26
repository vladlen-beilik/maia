<?php

namespace SpaceCode\Maia\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;
use SpaceCode\Maia\Fields\Hidden;
use SpaceCode\Maia\Fields\Tabs;
use SpaceCode\Maia\Fields\TabsOnEdit;

class Comment extends Resource
{
    use TabsOnEdit;

    /**
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\Comment::class;

    /**
     * @var string
     */
    public static $id = 'id';

    /**
     * @var array
     */
    public static $statuses = [
        'pending' => 'warning',
        'published' => 'success',
        'spam' => 'info',
        'deleted' => 'danger'
    ];

    /**
     * @return array|string|null
     */
    public static function label()
    {
        return _trans('maia::resources.comments');
    }

    /**
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return _trans('maia::resources.comment');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });
        if($this->getCommentType() === 'post') {
            $res = BelongsToMany::make(_trans('maia::resources.resource'), 'post', Post::class)->nullable();
        } else if ($this->getCommentType() === 'portfolio') {
            $res = BelongsToMany::make(_trans('maia::resources.resource'), 'portfolio', Portfolio::class)->nullable();
        } else {
            $res = Hidden::make(_trans('maia::resources.resource'), 'resource');
        }
        return [
            (new Tabs($this->singularLabel(), [
                _trans('maia::resources.general') => [
                    ID::make()->asBigInt()->sortable(),

                    Select::make(_trans('maia::resources.guard_name'), 'guard_name')
                        ->options($guardOptions->toArray())
                        ->rules('required', Rule::in($guardOptions))
                        ->hideFromIndex(),

                    Text::make(_trans('maia::resources.author'), 'author_id', function () {
                        return '<p>' . $this->author_id === 0 ? '—' : $this->user->getName() . '</p>';
                    })->exceptOnForms()->asHtml(),

                    Badge::make(_trans('maia::resources.status'), 'status', function () {
                        if (!is_null($this->deleted_at))
                            return 'deleted';
                        return $this->status;
                    })->map(static::$statuses)
                        ->sortable(),

                    Select::make(_trans('maia::resources.status'), 'status')
                        ->options(collect(static::$model::$statuses)->mapWithKeys(function ($key) {
                            return [$key => ucfirst($key)];
                        }))->onlyOnForms()
                        ->rules('required')
                        ->displayUsingLabels()
                ],
                _trans('maia::resources.parent') => [
                    BelongsTo::make(_trans('maia::resources.parent'), 'parent', self::class)
                        ->onlyOnDetail()
                        ->nullable()
                        ->searchable()
                ],
                _trans('maia::resources.resource') => [
                    $res
                ],
                _trans('maia::resources.content') => [
                    Textarea::make(_trans('maia::resources.body'), 'body')
                        ->rules('required', 'min:3')
                        ->showOnIndex(true),

                    DateTime::make(_trans('maia::resources.created_at'), 'created_at')
                        ->exceptOnForms()
                        ->hideFromIndex(),

                    Text::make(_trans('maia::resources.created_at'), 'created_at')
                        ->onlyOnIndex()
                        ->sortable()
                        ->displayUsing(function($date) {
                            return $date->diffForHumans();
                        }),

                    DateTime::make(_trans('maia::resources.updated_at'), 'updated_at')
                        ->exceptOnForms()
                        ->hideFromIndex(),

                    Text::make(_trans('maia::resources.updated_at'), 'updated_at')
                        ->onlyOnIndex()
                        ->sortable()
                        ->displayUsing(function($date) {
                            return $date->diffForHumans();
                        }),
                ]
            ]))->withToolbar()
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * @return $this
     */
    public function getCommentType()
    {
        $relation = DB::table('comments_relationships')->where('comment_id', $this->id)->first();
        return !$relation ? null : $relation->type;
    }
}
