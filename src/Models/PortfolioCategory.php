<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use SpaceCode\Maia\Exceptions\PortfolioCategoryConflict;

class PortfolioCategory extends Model
{
    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {

            $already = self::all()->map(function ($portfolioCategory) {
                return $portfolioCategory->getUrl();
            });
            if($already->count() > 0 && !is_null($model->parent_id) && in_array($model->getUrl(), $already->toArray())) {
                throw PortfolioCategoryConflict::url($model->getUrl(), $model->guard_name);
            }

            return true;
        });

        static::updating(function($model) {

            $already = self::all()->map(function ($portfolioCategory) {
                return $portfolioCategory->getUrl();
            });
            if($already->count() > 0 && !is_null($model->parent_id) && in_array($model->getUrl(), $already->toArray())) {
                throw PortfolioCategoryConflict::url($model->getUrl(), $model->guard_name);
            }

            return true;
        });
    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['template'] = $attributes['template'] ?? 'default';
        $attributes['order'] = $attributes['order'] ?? 1;
        $attributes['document_state'] = $attributes['document_state'] ?? 'dynamic';
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
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * @param bool $arg
     * @return mixed|string
     */
    public function getUrl($arg = false)
    {
        $url = $this->slug;
        $parent = $this;
        while ($parent = $parent->parent) {
            $url = $parent->slug . '/' . $url;
        }
        $link = seo('seo_portfolio_categories_prefix') . '/' . $url;
        return $arg ? url($link) : $link;
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
