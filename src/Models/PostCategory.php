<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SpaceCode\Maia\Exceptions\PostCategoryConflict;

class PostCategory extends Model
{
    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {

            $already = self::all()->map(function ($postCategory) {
                return $postCategory->getUrl();
            });
            if($already->count() > 0 && !is_null($model->parent_id) && in_array($model->getUrl(), $already->toArray())) {
                throw PostCategoryConflict::url($model->getUrl(), $model->guard_name);
            }

            return true;
        });

        static::updating(function($model) {

            $already = self::all()->map(function ($postCategory) {
                return $postCategory->getUrl();
            });
            if($already->count() > 0 && !is_null($model->parent_id) && in_array($model->getUrl(), $already->toArray())) {
                throw PostCategoryConflict::url($model->getUrl(), $model->guard_name);
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
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * @return mixed|string
     */
    public function getUrl()
    {
        $url = $this->slug;
        $parent = $this;
        while ($parent = $parent->parent) {
            $url = $parent->slug . '/' . $url;
        }
        return seo('seo_post_categories_prefix') . '/' . $url;
    }
}
