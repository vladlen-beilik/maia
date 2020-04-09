<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\User;

class Shop extends Model
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
        $attributes['author_id'] = $attributes['author_id'] ?? Auth::id();
        $attributes['template'] = $attributes['template'] ?? 'default';
        $attributes['status'] = $attributes['status'] ?? 'pending';
        $attributes['document_state'] = $attributes['document_state'] ?? 'dynamic';
        $attributes['view'] = $attributes['view'] ?? 0;
        $attributes['view_unique'] = $attributes['view_unique'] ?? 0;
        parent::__construct($attributes);
        $this->setTable('shops');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function($model) {
            $location = ShopsMeta::getValue($model->id, 'location');
            // Location
            if(is_null($location)) {
                ShopsMeta::insert(['shop_id' => $model->id, 'key' => 'location', 'value' => json_encode((object)['country' => $model->country, 'city' => $model->city])]);
            } else {
                ShopsMeta::where(['shop_id' => $model->id, 'key' => 'location'])->update(['value' => json_encode((object)['country' => $model->country, 'city' => $model->city])]);
            }
            unset($model->city);
            unset($model->country);

            // scheduleTime
            $scheduleTime = ShopsMeta::getValue($model->id, 'scheduleTime');
            $modelScheduleTime = (object)[
                'monday' => (object)['value' => is_null($model->scheduleTimeMondayValue) ? 0 : intval($model->scheduleTimeMondayValue), 'from' => $model->scheduleTimeMondayFrom, 'to' => $model->scheduleTimeMondayTo],
                'tuesday' => (object)['value' => is_null($model->scheduleTimeTuesdayValue) ? 0 : intval($model->scheduleTimeTuesdayValue), 'from' => $model->scheduleTimeTuesdayFrom, 'to' => $model->scheduleTimeTuesdayTo],
                'wednesday' => (object)['value' => is_null($model->scheduleTimeWednesdayValue) ? 0 : intval($model->scheduleTimeWednesdayValue), 'from' => $model->scheduleTimeWednesdayFrom, 'to' => $model->scheduleTimeWednesdayTo],
                'thursday' => (object)['value' => is_null($model->scheduleTimeThursdayValue) ? 0 : intval($model->scheduleTimeThursdayValue), 'from' => $model->scheduleTimeThursdayFrom, 'to' => $model->scheduleTimeThursdayTo],
                'friday' => (object)['value' => is_null($model->scheduleTimeFridayValue) ? 0 : intval($model->scheduleTimeFridayValue), 'from' => $model->scheduleTimeFridayFrom, 'to' => $model->scheduleTimeFridayTo],
                'saturday' => (object)['value' => is_null($model->scheduleTimeSaturdayValue) ? 0 : intval($model->scheduleTimeSaturdayValue), 'from' => $model->scheduleTimeSaturdayFrom, 'to' => $model->scheduleTimeSaturdayTo],
                'sunday' => (object)['value' => is_null($model->scheduleTimeSundayValue) ? 0 : intval($model->scheduleTimeSundayValue), 'from' => $model->scheduleTimeSundayFrom, 'to' => $model->scheduleTimeSundayTo]
            ];

            if(is_null($scheduleTime)) {
                ShopsMeta::insert(['shop_id' => $model->id, 'key' => 'scheduleTime', 'value' => json_encode($modelScheduleTime)]);
            } else {
                ShopsMeta::where(['shop_id' => $model->id, 'key' => 'scheduleTime'])->update(['value' => json_encode($modelScheduleTime)]);
            }
            unset($model->scheduleTimeMondayValue);
            unset($model->scheduleTimeMondayFrom);
            unset($model->scheduleTimeMondayTo);
            unset($model->scheduleTimeTuesdayValue);
            unset($model->scheduleTimeTuesdayFrom);
            unset($model->scheduleTimeTuesdayTo);
            unset($model->scheduleTimeWednesdayValue);
            unset($model->scheduleTimeWednesdayFrom);
            unset($model->scheduleTimeWednesdayTo);
            unset($model->scheduleTimeThursdayValue);
            unset($model->scheduleTimeThursdayFrom);
            unset($model->scheduleTimeThursdayTo);
            unset($model->scheduleTimeFridayValue);
            unset($model->scheduleTimeFridayFrom);
            unset($model->scheduleTimeFridayTo);
            unset($model->scheduleTimeSaturdayValue);
            unset($model->scheduleTimeSaturdayFrom);
            unset($model->scheduleTimeSaturdayTo);
            unset($model->scheduleTimeSundayValue);
            unset($model->scheduleTimeSundayFrom);
            unset($model->scheduleTimeSundayTo);

            // Communication
            $communication = ShopsMeta::getValue($model->id, 'scheduleTime');
            if(is_null($communication)) {
                ShopsMeta::insert(['shop_id' => $model->id, 'key' => 'communication', 'value' => $model->communication]);
            } else {
                ShopsMeta::where(['shop_id' => $model->id, 'key' => 'communication'])->update(['value' => $model->communication]);
            }
            unset($model->communication);
            return true;
        });

        static::retrieved(function ($model) {
            $scheduleTime = ShopsMeta::getValue($model->id, 'scheduleTime');
            $model->scheduleTimeMondayValue = jsonProp($scheduleTime, 'monday->value');
            $model->scheduleTimeMondayFrom = jsonProp($scheduleTime, 'monday->from');
            $model->scheduleTimeMondayTo = jsonProp($scheduleTime, 'monday->to');
            $model->scheduleTimeTuesdayValue = jsonProp($scheduleTime, 'tuesday->value');
            $model->scheduleTimeTuesdayFrom = jsonProp($scheduleTime, 'tuesday->from');
            $model->scheduleTimeTuesdayTo = jsonProp($scheduleTime, 'tuesday->to');
            $model->scheduleTimeWednesdayValue = jsonProp($scheduleTime, 'wednesday->value');
            $model->scheduleTimeWednesdayFrom = jsonProp($scheduleTime, 'wednesday->from');
            $model->scheduleTimeWednesdayTo = jsonProp($scheduleTime, 'wednesday->to');
            $model->scheduleTimeThursdayValue = jsonProp($scheduleTime, 'thursday->value');
            $model->scheduleTimeThursdayFrom = jsonProp($scheduleTime, 'thursday->from');
            $model->scheduleTimeThursdayTo = jsonProp($scheduleTime, 'thursday->to');
            $model->scheduleTimeFridayValue = jsonProp($scheduleTime, 'friday->value');
            $model->scheduleTimeFridayFrom = jsonProp($scheduleTime, 'friday->from');
            $model->scheduleTimeFridayTo = jsonProp($scheduleTime, 'friday->to');
            $model->scheduleTimeSaturdayValue = jsonProp($scheduleTime, 'saturday->value');
            $model->scheduleTimeSaturdayFrom = jsonProp($scheduleTime, 'saturday->from');
            $model->scheduleTimeSaturdayTo = jsonProp($scheduleTime, 'saturday->to');
            $model->scheduleTimeSundayValue = jsonProp($scheduleTime, 'sunday->value');
            $model->scheduleTimeSundayFrom = jsonProp($scheduleTime, 'sunday->from');
            $model->scheduleTimeSundayTo = jsonProp($scheduleTime, 'sunday->to');
            if(is_null($model->communication)) {
                $communication = ShopsMeta::getValue($model->id, 'communication');
                $model->communication = $communication;
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @param bool $arg
     * @return mixed|string
     */
    public function getUrl($arg = false)
    {
        $url = seo('seo_shops_prefix') . '/' . $this->slug;
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

    /**
     * @param $type
     * @return |null
     */
    public function getLocation($type)
    {
        $shopMeta = DB::table('shops_meta')->where(['shop_id' => $this->id, 'key' => 'location'])->first();
        if($shopMeta && !is_null(jsonProp($shopMeta->value, $type))) {
            return json_decode($shopMeta->value)->{$type};
        }
        return null;
    }

    /**
     * @param $type
     * @return |null
     */
    public function getMeta($type)
    {
        $meta = DB::table('shops_meta')->where(['shop_id' => $this->id, 'key' => $type])->first();
        if($meta && !is_null(jsonProp($meta->value, $type))) {
            return json_decode($meta->value)->{$type};
        }
        return null;
    }
}
