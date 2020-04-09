<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SpaceCode\Maia\Exceptions\ProductConflict;
use Carbon\Carbon;

class Product extends Model
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
        $attributes['template'] = $attributes['template'] ?? 'default';
        $attributes['status'] = $attributes['status'] ?? 'pending';
        $attributes['document_state'] = $attributes['document_state'] ?? 'dynamic';
        $attributes['comments'] = $attributes['comments'] ?? 0;
        $attributes['view'] = $attributes['view'] ?? 0;
        $attributes['view_unique'] = $attributes['view_unique'] ?? 0;
        $attributes['amount'] = $attributes['amount'] ?? 0;
        $attributes['wholesale_from'] = $attributes['wholesale_from'] ?? 2;
        parent::__construct($attributes);
        $this->setTable('products');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function($model) {
            if($model->regular_price === '0.00' || $model->regular_price === '0') {
                throw ProductConflict::price();
            }
            $model->amount = intval($model->amount);
            if($model->wholesale_from) {
                $model->wholesale_from = intval($model->wholesale_from);
            }
            if(intval($model->wholesalePrice) === 0) {
                $model->wholesale_price = null;
                $model->wholesale_from = null;
                $model->discount_wholesale_price = null;
                $model->discount_wholesale_date_from = null;
                $model->discount_wholesale_date_to = null;
            }
            if(intval($model->wholesaleToggle) === 0) {
                $model->discount_wholesale_date_from = null;
                $model->discount_wholesale_date_to = null;
                $model->discount_wholesale_price = null;
            }
            if(intval($model->wholesalePrice) === 1) {
                if($model->wholesale_price === '0.00' || $model->wholesale_price === '0') {
                    throw ProductConflict::wholesale_price();
                }
                if(intval($model->wholesaleToggle) === 1) {
                    if($model->discount_wholesale_price <= $model->wholesale_price) {
                        throw ProductConflict::discountWholesale_price();
                    }
                    if($model->discount_wholesale_date_from <= Carbon::now()) {
                        throw ProductConflict::wholesale_dateFromWithNow();
                    }
                    if($model->discount_wholesale_date_to <= Carbon::now()) {
                        throw ProductConflict::wholesale_dateToWithNow();
                    }
                    if($model->discount_wholesale_date_from <= Carbon::now()->addMinutes(5)) {
                        throw ProductConflict::wholesale_dateFromAddMinutes();
                    }
                    if($model->discount_wholesale_date_from->addHour() >= $model->discount_wholesale_date_to) {
                        throw ProductConflict::wholesale_dateFromWithTo();
                    }
                }
            }
            if(intval($model->saleToggle) === 0) {
                $model->discount_date_from = null;
                $model->discount_date_to = null;
                $model->discount_price = null;
            }
            if(intval($model->saleToggle) === 1) {
                if($model->discount_price <= $model->regular_price) {
                    throw ProductConflict::discountPrice();
                }
                if($model->discount_date_from <= Carbon::now()) {
                    throw ProductConflict::dateFromWithNow();
                }
                if($model->discount_date_to <= Carbon::now()) {
                    throw ProductConflict::dateToWithNow();
                }
                if($model->discount_date_from <= Carbon::now()->addMinutes(5)) {
                    throw ProductConflict::dateFromAddMinutes();
                }
                if($model->discount_date_from->addHour() >= $model->discount_date_to) {
                    throw ProductConflict::dateFromWithTo();
                }
            }
            unset($model->saleToggle);
            unset($model->wholesalePrice);
            unset($model->wholesaleToggle);
            return true;
        });

        static::retrieved(function ($model) {
            $model->wholesalePrice = 0;
            $model->wholesaleToggle = 0;
            $model->saleToggle = 0;
            if(!is_null($model->wholesale_price) && !is_null($model->wholesale_from) && $model->wholesale_from >= 2) {
                $model->wholesalePrice = 1;
                if(!is_null($model->discount_wholesale_date_from) && !is_null($model->discount_wholesale_date_to) && !is_null($model->discount_wholesale_price)) {
                    $model->wholesaleToggle = 1;
                }
            }
            if(!is_null($model->discount_date_from) && !is_null($model->discount_date_to) && !is_null($model->discount_price)) {
                $model->saleToggle = 1;
            }
        });

        static::deleting(function($model) {
            $storage = Storage::disk(config('maia.filemanager.disk'));
            if(!is_null($model->image) && $storage->exists($model->image)) {
                $storage->delete($model->image);
            }
            DB::table('relationships')->where(['type' => 'product_tag', 'item_id' => $model->id])->delete();
            DB::table('relationships')->where(['type' => 'product_category', 'item_id' => $model->id])->delete();

            DB::table('comments_relationships')->where(['type' => 'product', 'item_id' => $model->id])->delete();
            $model->comments->delete();

            return true;
        });
    }

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id')->where('author_id', Auth::id());
    }

    /**
     * @return BelongsToMany
     */
    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'relationships', 'item_id', 'term_id')->where('relationships.type', 'product_category');
    }

    /**
     * @return BelongsToMany
     */
    public function tags() : BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'relationships', 'item_id', 'term_id')->where('relationships.type', 'product_tag');
    }


    /**
     * @return BelongsToMany
     */
    public function commentsList() : BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'comments_relationships', 'item_id', 'comment_id')->where('comments_relationships.type', 'product');
    }

    /**
     * @param bool $arg
     * @return mixed|string
     */
    public function getUrl($arg = false)
    {
        $url = seo('seo_products_prefix') . '/' . $this->slug;
        return $arg ? url($url) : $url;
    }

    /**
     * @return mixed|string
     */
    public function thumbnail()
    {
        if(!is_null($this->image)) {
            return Storage::disk(config('maia.filemanager.disk'))->url($this->image);
        }
        return '#';
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
