<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SpaceCode\Maia\Contracts\PortfolioCategory as PortfolioCategoryContract;
use SpaceCode\Maia\Exceptions\PortfolioAlreadyExists;
use SpaceCode\Maia\Exceptions\PortfolioCategoryAlreadyExists;
use SpaceCode\Maia\Exceptions\PortfolioCategoryDoesNotExist;
use SpaceCode\Maia\Guard;

class PortfolioCategory extends Model implements PortfolioCategoryContract
{
    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    /**
     * Portfolio Category constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->setTable('portfolio_categories');
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
     * @throws PortfolioCategoryAlreadyExists
     */
    public static function create(array $attributes = [])
    {
        if (static::where('slug', $attributes['slug'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw PortfolioAlreadyExists::create($attributes['slug'], $attributes['guard_name']);
        }
        return static::query()->create($attributes);
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PortfolioCategoryContract
     * @throws PortfolioCategoryDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName = null): PortfolioCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioCategory = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $portfolioCategory) {
            throw PortfolioCategoryDoesNotExist::sluged($slug);
        }
        return $portfolioCategory;
    }

    /**
     * @param string $title
     * @param string|null $guardName
     * @return PortfolioCategoryContract
     * @throws PortfolioCategoryDoesNotExist
     */
    public static function findByTitle(string $title, $guardName = null): PortfolioCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioCategory = static::where('title', $title)->where('guard_name', $guardName)->first();
        if (! $portfolioCategory) {
            throw PortfolioCategoryDoesNotExist::named($title);
        }
        return $portfolioCategory;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return PortfolioCategoryContract
     * @throws PortfolioCategoryDoesNotExist
     */
    public static function findById(int $id, $guardName = null): PortfolioCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioCategory = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $portfolioCategory) {
            throw PortfolioCategoryDoesNotExist::withId($id);
        }
        return $portfolioCategory;
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PortfolioCategoryContract
     */
    public static function findOrCreate(string $slug, $guardName = null): PortfolioCategoryContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioCategory = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $portfolioCategory) {
            return static::query()->create(['slug' => $slug, 'guard_name' => $guardName]);
        }
        return $portfolioCategory;
    }
}
