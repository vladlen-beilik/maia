<?php

namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use SpaceCode\Maia\Contracts\Portfolio as PortfolioContract;
use SpaceCode\Maia\Exceptions\PortfolioAlreadyExists;
use SpaceCode\Maia\Exceptions\PortfolioDoesNotExist;
use SpaceCode\Maia\Guard;

class Portfolio extends Model implements PortfolioContract
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
     * Portfolio constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['author_id'] = Auth::id();
        parent::__construct($attributes);
        $this->setTable('portfolio');
    }

    /**
     * @param array $attributes
     * @return Builder|Model
     * @throws PortfolioAlreadyExists
     */
    public static function create(array $attributes = [])
    {
        if (static::where('slug', $attributes['slug'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw PortfolioAlreadyExists::create($attributes['slug'], $attributes['guard_name']);
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
        return $this->belongsToMany(PortfolioCategory::class, 'relation_portfolio_category', 'portfolio_id', 'category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(PortfolioTag::class, 'relation_portfolio_tag', 'portfolio_id', 'tag_id');
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PortfolioContract
     * @throws PortfolioDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName = null): PortfolioContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolio = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $portfolio) {
            throw PortfolioDoesNotExist::sluged($slug);
        }
        return $portfolio;
    }

    /**
     * @param string $title
     * @param string|null $guardName
     * @return PortfolioContract
     * @throws PortfolioDoesNotExist
     */
    public static function findByTitle(string $title, $guardName = null): PortfolioContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolio = static::where('title', $title)->where('guard_name', $guardName)->first();
        if (! $portfolio) {
            throw PortfolioDoesNotExist::named($title);
        }
        return $portfolio;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return PortfolioContract
     * @throws PortfolioDoesNotExist
     */
    public static function findById(int $id, $guardName = null): PortfolioContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolio = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $portfolio) {
            throw PortfolioDoesNotExist::withId($id);
        }
        return $portfolio;
    }
}
