<?php

namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    /**
     * @var string
     */
    protected $table = 'users_meta';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = ['user_id', 'key', 'value'];

    /**
     * @param $userId
     * @param $key
     * @param $value
     */
    public function setValue($userId, $key, $value)
    {
        $meta = static::where(['user_id' => $userId, 'key' => $key])->first();
        if(isset($meta)) {
            static::where(['user_id' => $userId, 'key' => $key])->update(['value' => $value]);
        } else {
            static::insert(['user_id' => $userId, 'key' => $key, 'value' => $value]);
        }
    }

    /**
     * @param $userId
     * @param $key
     * @return mixed|null
     */
    public function getValue($userId, $key)
    {
        $meta = static::where(['user_id' => $userId, 'key' => $key])->first();
        return isset($meta) ? $meta->value : null;
    }
}
