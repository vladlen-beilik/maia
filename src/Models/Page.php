<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use SpaceCode\Maia\Contracts\Page as PageContract;
use SpaceCode\Maia\Exceptions\PageAlreadyExists;
use SpaceCode\Maia\Exceptions\PageDoesNotExist;
use SpaceCode\Maia\Guard;

class Page extends Model implements PageContract
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
     * Page constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['author_id'] = Auth::id();
        parent::__construct($attributes);
        $this->setTable('pages');
    }

    /**
     * @param array $attributes
     * @return Builder|Model
     * @throws PageAlreadyExists
     */
    public static function create(array $attributes = [])
    {
        if (static::where('slug', $attributes['slug'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw PageAlreadyExists::create($attributes['slug'], $attributes['guard_name']);
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
     * @param string $slug
     * @param string|null $guardName
     * @return PageContract
     * @throws PageDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName = null): PageContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $page = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $page) {
            throw PageDoesNotExist::sluged($slug);
        }
        return $page;
    }

    /**
     * @param string $title
     * @param string|null $guardName
     * @return PageContract
     * @throws PageDoesNotExist
     */
    public static function findByTitle(string $title, $guardName = null): PageContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $page = static::where('title', $title)->where('guard_name', $guardName)->first();
        if (! $page) {
            throw PageDoesNotExist::named($title);
        }
        return $page;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return PageContract
     * @throws PageDoesNotExist
     */
    public static function findById(int $id, $guardName = null): PageContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $page = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $page) {
            throw PageDoesNotExist::withId($id);
        }
        return $page;
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PageContract
     */
    public static function findOrCreate(string $slug, $guardName = null): PageContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $page = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $page) {
            return static::query()->create(['slug' => $slug, 'guard_name' => $guardName]);
        }
        return $page;
    }
}
