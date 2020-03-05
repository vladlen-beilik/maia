<?php

namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use SpaceCode\Maia\Tools\SeoTool;
use Illuminate\Support\Collection as BaseCollection;

class Seo extends Model
{
    protected $primaryKey = 'key';
    protected $table = 'seo';
    public $incrementing = false;
    public $timestamps = false;
    public $fillable = ['key', 'value'];

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getValueAttribute()
    {
        $value = $this->attributes['value'];
        $casts = SeoTool::getCasts();
        $castType = $casts[$this->key] ?? null;

        if (!$castType) return $value;

        switch ($castType) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return $this->fromFloat($value);
            case 'decimal':
                return $this->asDecimal($value, explode(':', $casts[$this->key], 2)[1]);
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDate($value);
            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value);
            case 'timestamp':
                return $this->asTimestamp($value);
        }
    }

    public static function getValueForKey($key)
    {
        $seo = static::where('key', $key)->get()->first();
        return isset($seo) ? $seo->value : null;
    }
}
