<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostTag extends Model
{
    const STATE_STATIC = 'static';
    const STATE_DYNAMIC = 'dynamic';

    public static $states = [self::STATE_STATIC, self::STATE_DYNAMIC];

    protected $guarded = ['id'];

    /**
     * Post Tag constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        $attributes['template'] = $attributes['template'] ?? 'default';
        $attributes['document_state'] = $attributes['document_state'] ?? 'dynamic';
        parent::__construct($attributes);
        $this->setTable('post_tags');
    }

    /**
     * @param bool $arg
     * @return mixed|string
     */
    public function getUrl($arg = false)
    {
        $url = seo('seo_post_tags_prefix') . '/' . $this->slug;
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
}
