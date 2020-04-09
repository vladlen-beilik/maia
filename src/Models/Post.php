<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\User;

class Post extends Model
{
    use SoftDeletes;

    const STATUS_PUBLISHED = 'published';
    const STATUS_PENDING = 'pending';

    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $statuses = [self::STATUS_PUBLISHED, self::STATUS_PENDING];
    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    /**
     * Post constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['author_id'] = $attributes['author_id'] ?? Auth::id();
        $attributes['template'] = $attributes['template'] ?? 'default';
        $attributes['status'] = $attributes['status'] ?? 'pending';
        $attributes['document_state'] = $attributes['document_state'] ?? 'dynamic';
        $attributes['comments'] = $attributes['comments'] ?? 0;
        $attributes['view'] = $attributes['view'] ?? 0;
        $attributes['view_unique'] = $attributes['view_unique'] ?? 0;
        parent::__construct($attributes);
        $this->setTable('posts');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($model) {
            $storage = Storage::disk(config('maia.filemanager.disk'));
            if(!is_null($model->image) && $storage->exists($model->image)) {
                $storage->delete($model->image);
            }
            DB::table('relationships')->where(['type' => 'post_tag', 'item_id' => $model->id])->delete();
            DB::table('relationships')->where(['type' => 'post_category', 'item_id' => $model->id])->delete();

            DB::table('comments_relationships')->where(['type' => 'post', 'item_id' => $model->id])->delete();
            $model->comments->delete();

            return true;
        });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsToMany
     */
    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'relationships', 'item_id', 'term_id')->where('relationships.type', 'post_category');
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(PostTag::class, 'relationships', 'item_id', 'term_id')->where('relationships.type', 'post_tag');
    }


    /**
     * @return BelongsToMany
     */
    public function commentsList() : BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'comments_relationships', 'item_id', 'comment_id')->where('comments_relationships.type', 'post');
    }

    /**
     * @param bool $arg
     * @return mixed|string
     */
    public function getUrl($arg = false)
    {
        $url = seo('seo_posts_prefix') . '/' . $this->slug;
        return $arg ? url($url) : $url;
    }

    /**
     * @return mixed|string
     */
    public function thumbnail()
    {
        if(!is_null($this->image)) {
            return Storage::disk(config('maia.filemanager.disk'))->url($this->image);
        }
        return '#';
    }

    /**
     * @param $string
     * @param $limit
     * @param $end
     * @return mixed|string
     */
    public function limit($string, $limit, $end)
    {
        return Str::limit((string)$string, $limit, $end);
    }
}
