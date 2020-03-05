<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use SpaceCode\Maia\Contracts\Post as PostContract;
use SpaceCode\Maia\Exceptions\PostAlreadyExists;
use SpaceCode\Maia\Exceptions\PostDoesNotExist;
use SpaceCode\Maia\Guard;

class Post extends Model implements PostContract
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
        $attributes['author_id'] = Auth::id();
        parent::__construct($attributes);
        $this->setTable('posts');
    }

    /**
     * @param array $attributes
     * @return Builder|Model
     * @throws PostAlreadyExists
     */
    public static function create(array $attributes = [])
    {
        if (static::where('slug', $attributes['slug'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw PostAlreadyExists::create($attributes['slug'], $attributes['guard_name']);
        }
        return static::query()->create($attributes);
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
     * @param string $slug
     * @param string|null $guardName
     * @return PostContract
     * @throws PostDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName = null): PostContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $post = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $post) {
            throw PostDoesNotExist::sluged($slug);
        }
        return $post;
    }

    /**
     * @param string $title
     * @param string|null $guardName
     * @return PostContract
     * @throws PostDoesNotExist
     */
    public static function findByTitle(string $title, $guardName = null): PostContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $post = static::where('title', $title)->where('guard_name', $guardName)->first();
        if (! $post) {
            throw PostDoesNotExist::named($title);
        }
        return $post;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return PostContract
     * @throws PostDoesNotExist
     */
    public static function findById(int $id, $guardName = null): PostContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $post = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $post) {
            throw PostDoesNotExist::withId($id);
        }
        return $post;
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PostContract
     */
    public static function findOrCreate(string $slug, $guardName = null): PostContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $post = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $post) {
            return static::query()->create(['slug' => $slug, 'guard_name' => $guardName]);
        }
        return $post;
    }
}
