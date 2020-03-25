<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SpaceCode\Maia\Collections\CommentCollection;

class Comment extends Model
{
    use SoftDeletes;

    const STATUS_PUBLISHED = 'published';
    const STATUS_PENDING = 'pending';

    public static $statuses = [self::STATUS_PUBLISHED, self::STATUS_PENDING];

    protected $guarded = ['id'];

//    public static function boot()
//    {
//        parent::boot();
//
//        static::saving(function($model) {
//            $prefixes = Seo::where('key', 'LIKE', '%_prefix')->pluck('value');
//            if($prefixes->count() > 0 && !is_null($model->parent_id) && in_array($model->parent->slug, $prefixes->toArray())) {
//                throw PageConflict::ban($model->getUrl());
//            }
//
//            $already = self::all()->map(function ($page) {
//                return $page->getUrl();
//            });
//            if($already->count() > 0 && !is_null($model->parent_id) && in_array($model->getUrl(), $already->toArray())) {
//                throw PageConflict::url($model->getUrl(), $model->guard_name);
//            }
//            return true;
//        });
//    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
//        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
//        $attributes['status'] = $attributes['status'] ?? 'pending';
        parent::__construct($attributes);
        $this->setTable('comments');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\\User', 'author_id');
    }

//    /**
//     * @return BelongsTo
//     */
//    public function parent(): BelongsTo
//    {
//        return $this->belongsTo(self::class, 'parent_id');
//    }

//    /**
//     * @return HasMany
//     */
//    public function children(): HasMany
//    {
//        return $this->hasMany(self::class, 'parent_id', 'id');
//    }

//    /**
//     * @param array $models
//     * @return \Illuminate\Database\Eloquent\Collection|CommentCollection
//     */
//    public function newCollection(array $models = [])
//    {
//        return new CommentCollection($models);
//    }
}
