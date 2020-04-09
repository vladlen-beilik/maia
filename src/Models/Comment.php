<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\User;

class Comment extends Model
{
    use SoftDeletes;

    const STATUS_PUBLISHED = 'published';
    const STATUS_PENDING = 'pending';
    const STATUS_SPAM = 'spam';

    public static $statuses = [self::STATUS_PUBLISHED, self::STATUS_PENDING, self::STATUS_SPAM];

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::deleting(function($model) {
            DB::table('comments_relationships')->where(['comment_id' => $model->id])->delete();
            return true;
        });
    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable('comments');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return BelongsToMany
     */
    public function post(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'comments_relationships', 'comment_id', 'item_id')->where('comments_relationships.type', 'post');
    }

    /**
     * @return BelongsToMany
     */
    public function portfolio(): BelongsToMany
    {
        return $this->belongsToMany(Portfolio::class, 'comments_relationships', 'comment_id', 'item_id')->where('comments_relationships.type', 'portfolio');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
