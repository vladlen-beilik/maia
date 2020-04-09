<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;

class ShopsMeta extends Model {

    protected $primaryKey = 'key';
    protected $table = 'shops_meta';
    public $incrementing = false;
    public $timestamps = false;
    public $fillable = ['key', 'value'];

    public static function getValue($id, $key)
    {
        $meta = static::where(['shop_id' => $id, 'key' => $key])->get()->first();
        return isset($meta) ? $meta->value : null;
    }
}
