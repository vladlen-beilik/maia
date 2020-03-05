<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SpaceCode\Maia\Contracts\PostCategory as PostCategoryContract;
use SpaceCode\Maia\Exceptions\PostAlreadyExists;
use SpaceCode\Maia\Exceptions\PostCategoryAlreadyExists;
use SpaceCode\Maia\Exceptions\PostCategoryDoesNotExist;
use SpaceCode\Maia\Guard;

class PostCategory extends Model implements PostCategoryContract
{
    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    /**
     * Post Category constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->setTable('post_categories');
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @param array $attributes
     * @return Builder|Model
     * @throws PostCategoryAlreadyExists
     */
    public static function create(array $attributes = [])
    {
        if (static::where('slug', $attributes['slug'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw PostAlreadyExists::create($attributes['slug'], $attributes['guard_name']);
        }
        return static::query()->create($attributes);
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PostCategoryContract
     * @throws PostCategoryDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName = null): PostCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postCategory = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $postCategory) {
            throw PostCategoryDoesNotExist::sluged($slug);
        }
        return $postCategory;
    }

    /**
     * @param string $title
     * @param string|null $guardName
     * @return PostCategoryContract
     * @throws PostCategoryDoesNotExist
     */
    public static function findByTitle(string $title, $guardName = null): PostCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postCategory = static::where('title', $title)->where('guard_name', $guardName)->first();
        if (! $postCategory) {
            throw PostCategoryDoesNotExist::named($title);
        }
        return $postCategory;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return PostCategoryContract
     * @throws PostCategoryDoesNotExist
     */
    public static function findById(int $id, $guardName = null): PostCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postCategory = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $postCategory) {
            throw PostCategoryDoesNotExist::withId($id);
        }
        return $postCategory;
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PostCategoryContract
     */
    public static function findOrCreate(string $slug, $guardName = null): PostCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postCategory = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $postCategory) {
            return static::query()->create(['slug' => $slug, 'guard_name' => $guardName]);
        }
        return $postCategory;
    }
}
