<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use SpaceCode\Maia\Contracts\PostTag as PostTagContract;
use SpaceCode\Maia\Exceptions\PostAlreadyExists;
use SpaceCode\Maia\Exceptions\PostTagAlreadyExists;
use SpaceCode\Maia\Exceptions\PostTagDoesNotExist;
use SpaceCode\Maia\Guard;

class PostTag extends Model implements PostTagContract
{
    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    /**
     * Post Tag constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->setTable('post_tags');
    }

    /**
     * @param array $attributes
     * @return Builder|Model
     * @throws PostTagAlreadyExists
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
     * @return PostTagContract
     * @throws PostTagDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName = null): PostTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postTag = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $postTag) {
            throw PostTagDoesNotExist::sluged($slug);
        }
        return $postTag;
    }

    /**
     * @param string $title
     * @param string|null $guardName
     * @return PostTagContract
     * @throws PostTagDoesNotExist
     */
    public static function findByTitle(string $title, $guardName = null): PostTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postTag = static::where('title', $title)->where('guard_name', $guardName)->first();
        if (! $postTag) {
            throw PostTagDoesNotExist::named($title);
        }
        return $postTag;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return PostTagContract
     * @throws PostTagDoesNotExist
     */
    public static function findById(int $id, $guardName = null): PostTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postTag = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $postTag) {
            throw PostTagDoesNotExist::withId($id);
        }
        return $postTag;
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PostTagContract
     */
    public static function findOrCreate(string $slug, $guardName = null): PostTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $postTag = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $postTag) {
            return static::query()->create(['slug' => $slug, 'guard_name' => $guardName]);
        }
        return $postTag;
    }
}
