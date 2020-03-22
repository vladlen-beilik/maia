<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        parent::__construct($attributes);
        $this->setTable('posts');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\\User', 'author_id');
    }

    /**
     * @return BelongsToMany
     */
    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'relation_post_category', 'post_id', 'category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(PostTag::class, 'relation_post_tag', 'post_id', 'tag_id');
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
