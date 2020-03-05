<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use SpaceCode\Maia\Contracts\PortfolioTag as PortfolioTagContract;
use SpaceCode\Maia\Exceptions\PortfolioTagAlreadyExists;
use SpaceCode\Maia\Exceptions\PortfolioTagDoesNotExist;
use SpaceCode\Maia\Guard;

class PortfolioTag extends Model implements PortfolioTagContract
{
    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    /**
     * Portfolio Tag constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->setTable('portfolio_tags');
    }

    /**
     * @param array $attributes
     * @return Builder|Model
     * @throws PortfolioTagAlreadyExists
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
     * @return PortfolioTagContract
     * @throws PortfolioTagDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName = null): PortfolioTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioTag = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $portfolioTag) {
            throw PortfolioTagDoesNotExist::sluged($slug);
        }
        return $portfolioTag;
    }

    /**
     * @param string $title
     * @param string|null $guardName
     * @return PortfolioTagContract
     * @throws PortfolioTagDoesNotExist
     */
    public static function findByTitle(string $title, $guardName = null): PortfolioTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioTag = static::where('title', $title)->where('guard_name', $guardName)->first();
        if (! $portfolioTag) {
            throw PortfolioTagDoesNotExist::named($title);
        }
        return $portfolioTag;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return PortfolioTagContract
     * @throws PortfolioTagDoesNotExist
     */
    public static function findById(int $id, $guardName = null): PortfolioTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioTag = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $portfolioTag) {
            throw PortfolioTagDoesNotExist::withId($id);
        }
        return $portfolioTag;
    }

    /**
     * @param string $slug
     * @param string|null $guardName
     * @return PortfolioTagContract
     */
    public static function findOrCreate(string $slug, $guardName = null): PortfolioTagContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $portfolioTag = static::where('slug', $slug)->where('guard_name', $guardName)->first();
        if (! $portfolioTag) {
            return static::query()->create(['slug' => $slug, 'guard_name' => $guardName]);
        }
        return $portfolioTag;
    }
}
