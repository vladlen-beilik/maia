<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SpaceCode\Maia\Exceptions\PageConflict;
use App\User;

class Page extends Model
{
    use SoftDeletes;

    const STATUS_PUBLISHED = 'published';
    const STATUS_PENDING = 'pending';

    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $statuses = [self::STATUS_PUBLISHED, self::STATUS_PENDING];
    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::saving(function($model) {

            $prefixes = Seo::where('key', 'LIKE', '%_prefix')->pluck('value');
            $reserved = $prefixes->merge(['profile', 'admin', 'nova-api', 'maia-api', 'nova-vendor'])->toArray();

            if(is_null($model->parent_id) && in_array($model->getUrl(), $reserved)) {
                throw PageConflict::reserved($model->getUrl());
            }

            if($prefixes->count() > 0 && !is_null($model->parent_id) && in_array($model->parent->slug, $prefixes->toArray())) {
                throw PageConflict::ban($model->getUrl());
            }

            $already = self::all()->map(function ($page) {
                return $page->getUrl();
            });
            if($already->count() > 0 && !is_null($model->parent_id) && in_array($model->getUrl(), $already->toArray())) {
                throw PageConflict::url($model->getUrl(), $model->guard_name);
            }
            return true;
        });

        static::deleting(function($model) {
            Page::where('parent_id', $model->id)->update(['parent_id' => null]);
            DB::table('comments_relationships')->where(['type' => 'page', 'item_id' => $model->id])->delete();
            $model->comments->delete();
            return true;
        });
    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['author_id'] = $attributes['author_id'] ?? Auth::id();
        $attributes['status'] = $attributes['status'] ?? 'pending';
        $attributes['document_state'] = $attributes['document_state'] ?? 'dynamic';
        $attributes['template'] = $attributes['template'] ?? 'default';
        parent::__construct($attributes);
        $this->setTable('pages');
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
     * @return HasManyThrough
     */
    public function comments() : HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, 'comments_relationships', 'item_id', 'comment_id')->where('comments_relationships.type', 'page');
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
        return $arg ? url($url) : $url;
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

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
