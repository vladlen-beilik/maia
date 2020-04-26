<?php

use App\User;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Facades\Auth;
use SpaceCode\Maia\Models;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

if (!function_exists('settings')) {
    function settings($keys = null)
    {
        $query = Models\Settings::query();
        if (isset($keys)) $query->whereIn('key', $keys);
        return $query->get()->pluck('value', 'key')->toArray();
    }
}

if (!function_exists('setting')) {
    function setting($key)
    {
        $setting = Models\Settings::find($key);
        if(isset($setting)) {
            if($setting->value === '0' || $setting->value === '1') {
                return intval($setting->value);
            } else {
                return $setting->value;
            }
        }
        return null;
    }
}

if (!function_exists('seo')) {
    function seo($key)
    {
        $seo = Models\Seo::find($key);
        if(isset($seo)) {
            if($seo->value === '0' || $seo->value === '1') {
                return intval($seo->value);
            } else {
                return $seo->value;
            }
        }
        return null;
    }
}

if (! function_exists('getModelForGuard')) {
    /**
     * @param string $guard
     *
     * @return string|null
     */
    function getModelForGuard(string $guard)
    {
        return collect(config('auth.guards'))
            ->map(function ($guard) {
                if (! isset($guard['provider'])) {
                    return;
                }
                return config("auth.providers.{$guard['provider']}.model");
            })->get($guard);
    }
}

if (! function_exists('timezoneList')) {
    function timezoneList() {
        $timezones = [];
        foreach([DateTimeZone::AFRICA, DateTimeZone::AMERICA, DateTimeZone::ANTARCTICA, DateTimeZone::ASIA, DateTimeZone::ATLANTIC, DateTimeZone::AUSTRALIA, DateTimeZone::EUROPE, DateTimeZone::INDIAN, DateTimeZone::PACIFIC] as $region ) {
            $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
        }
        $timezone_offsets = [];
        foreach( $timezones as $timezone ) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }
        asort($timezone_offsets);
        $timezone_list = [];
        foreach($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );
            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
            $pretty_group = explode('/', $timezone)[0];
            $pretty_label = str_replace('_', ' ', $timezone) . ' ' . $pretty_offset;
            $timezone_list['UTC'] = ['label' => 'UTC', 'group' => _trans('maia::resources.default')];
            $timezone_list[$timezone] = ['label' => $pretty_label, 'group' => $pretty_group];
        }
        if (!Cache::has('timezoneList')) {
            Cache::forever('timezoneList', $timezone_list);
        }
        return Cache::get('timezoneList');
    }
}

//if (! function_exists('langList')) {
//    function langList() {
//        $langList = [
//            'en' => 'English',
//            'es' => 'Espanol',
//            'pt' => 'Português',
//            'fr' => 'Le français',
//            'en' => 'English',
//            'ru' => 'Русский',
//        ];
//        return $langList;
//    }
//}

if (! function_exists('changeEnv')) {
    function changeEnv($change_key, $change_value) {
        if(isset($change_key) && isset($change_value)) {
            if($change_key === 'APP_DEBUG') {
                $change_value = $change_value === '0' ? 'false' : 'true';
            }
            $env = file_get_contents(base_path() . '/.env');
            $new_env = [];
            $putContent = false;
            foreach(array_filter(array_map('trim', explode(PHP_EOL, $env))) as $key => $value) {
                $entry = explode("=", $value, 2);
                if(count($entry) > 1 && $entry[1] !== $change_value) {
                    $putContent = true;
                }
                if($entry[0] === $change_key) {
                    if(Str::contains($change_value, ' ') || Str::contains($change_value, '{') && Str::contains($change_value, '}') && Str::contains($change_value, '$')) {
                        $new_env[] = $entry[0] . '="' . $change_value . '"';
                    } else {
                        $new_env[] = $entry[0] . '=' . $change_value;
                    }
                } else {
                    $new_env[] = $entry[0] . '=' . $entry[1];
                }
            }
            if($putContent) {
                file_put_contents(base_path() . '/.env', implode(PHP_EOL, $new_env));
            }
        }
    }
}

if (! function_exists('setEnv')) {
    function setEnv($string) {
        if(isset($string)) {
            $env = file_get_contents(base_path() . '/.env');
            $env = array_filter(explode(PHP_EOL, $env));
            array_push($env, $string);
            file_put_contents(base_path() . '/.env', implode(PHP_EOL, $env));
        }
    }
}

if (! function_exists('rebuildEnv')) {
    function rebuildEnv() {
        $env = file_get_contents(base_path() . '/.env');
        $env = explode(PHP_EOL, $env);
        $env = array_map('trim', $env);
        $env = array_filter($env);

        $env = Collection::make($env)->groupBy(function ($item, $key) {
            return substr($item, 0, 2);
        })->toArray();

        $new_env = [];
        foreach($env as $key => $value) {
            foreach ($value as $_key => $_value) {
                $new_env[] = $_value . "\r";
            }
            $new_env[] = '';
        }
        file_put_contents(base_path() . '/.env', implode(PHP_EOL, $new_env));
    }
}

if (! function_exists('favicon')) {
    function favicon($path, $size) {
        $mime = explode('.', $path)[1];
        $name = explode('.', $path)[0];
        return Maia::image($name . '_' . $size . '.' . $mime);
    }
}

if (!function_exists('robots_all')) {
    function robots_all($name, $data)
    {
        $global = $name === 'pages' ? 1 : seo('seo_' . $name . '_global_index');
        if($name === 'home') {
            $current = 1;
        } else if(!is_null($data) && !is_null(jsonProp($data, 'robots'))) {
            $current = intval(json_decode($data)->robots);
        } else {
            $current = 0;
        }
        $return = $global === 0 ? 0 : intval($current);
        return $return === 0 ? 'noindex, nofollow, noodp' : 'index, follow, noodp';
    }
}

if (!function_exists('robots_google')) {
    function robots_google($name, $data)
    {
        $global = $name === 'pages' ? 1 : seo('seo_' . $name . '_google_bot_index');
        if($name === 'home') {
            $current = 1;
        } else if(!is_null($data) && !is_null(jsonProp($data, 'google'))) {
            $current = intval(json_decode($data)->google);
        } else {
            $current = 0;
        }
        $return = $global === 0 ? 0 : intval($current);
        return $return === 0 ? 'noindex, nofollow, noodp' : 'index, follow, noodp';
    }
}

if (!function_exists('robots_yandex')) {
    function robots_yandex($name, $data)
    {
        $global = $name === 'pages' ? 1 : seo('seo_' . $name . '_yandex_bot_index');
        if($name === 'home') {
            $current = 1;
        } else if(!is_null($data) && !is_null(jsonProp($data, 'yandex'))) {
            $current = intval(json_decode($data)->yandex);
        } else {
            $current = 0;
        }
        $return = $global === 0 ? 0 : intval($current);
        return $return === 0 ? 'noindex, nofollow, noodp' : 'index, follow, noodp';
    }
}

if (!function_exists('robots_bing')) {
    function robots_bing($name, $data)
    {
        $global = $name === 'pages' ? 1 : seo('seo_' . $name . '_bing_bot_index');
        if($name === 'home') {
            $current = 1;
        } else if(!is_null($data) && !is_null(jsonProp($data, 'bing'))) {
            $current = intval(json_decode($data)->bing);
        } else {
            $current = 0;
        }
        $return = $global === 0 ? 0 : intval($current);
        return $return === 0 ? 'noindex, nofollow, noodp' : 'index, follow, noodp';
    }
}

if (!function_exists('robots_yahoo')) {
    function robots_yahoo($name, $data)
    {
        $global = $name === 'pages' ? 1 : seo('seo_' . $name . '_slurp_bot_index');
        if($name === 'home') {
            $current = 1;
        } else if(!is_null($data) && !is_null(jsonProp($data, 'yahoo'))) {
            $current = intval(json_decode($data)->yahoo);
        } else {
            $current = 0;
        }
        $return = $global === 0 ? 0 : intval($current);
        return $return === 0 ? 'noindex, nofollow, noodp' : 'index, follow, noodp';
    }
}

if (!function_exists('robots_duck')) {
    function robots_duck($name, $data)
    {
        $global = $name === 'pages' ? 1 : seo('seo_' . $name . '_duck_bot_index');
        if($name === 'home') {
            $current = 1;
        } else if(!is_null($data) && !is_null(jsonProp($data, 'duck'))) {
            $current = intval(json_decode($data)->duck);
        } else {
            $current = 0;
        }
        $return = $global === 0 ? 0 : intval($current);
        return $return === 0 ? 'noindex, nofollow, noodp' : 'index, follow, noodp';
    }
}

if (!function_exists('robots_baidu')) {
    function robots_baidu($name, $data)
    {
        $global = $name === 'pages' ? 1 : seo('seo_' . $name . '_baidu_bot_index');
        if($name === 'home') {
            $current = 1;
        } else if(!is_null($data) && !is_null(jsonProp($data, 'baidu'))) {
            $current = intval(json_decode($data)->baidu);
        } else {
            $current = 0;
        }
        $return = $global === 0 ? 0 : intval($current);
        return $return === 0 ? 'noindex, nofollow, noodp' : 'index, follow, noodp';
    }
}

if (!function_exists('state')) {
    function state($name, $data)
    {
        if($name === 'home' || $name === 'pages') {
            $state = seo('seo_' . $name . '_document_state');
        } else {
            $state = $data;
        }
        $state = is_null($state) ? 'dynamic' : '';
        return $state;
    }
}

if (!function_exists('variables_array')) {
    function variables_array()
    {
        return [
            '%%author_name%%',
            '%%author_firstName%%',
            '%%author_lastName%%',
            '%%author_middleName%%',
            '%%resource_url%%',
            '%%resource_title%%',
            '%%resource_excerpt%%',
            '%%resource_meta_title%%',
            '%%resource_meta_description%%',
            '%%resource_meta_keywords%%',
            '%%resource_status%%',
            '%%resource_created_at%%',
            '%%resource_updated_at%%',
            '%%website_url%%',
            '%%website_title%%',
            '%%website_excerpt%%',
            '%%website_description%%'
        ];
    }
}

if (!function_exists('check_author')) {
    function check_author($id, $result)
    {
        if (isset($id)) {
            $user = User::find($id);
            if (!is_null($user->$result)) {
                if(Str::contains($result, '->')) {
                    $str = explode('->', $result, 2);
                    if(!is_null(jsonProp($user->{$str[0]}, $str[1]))) {
                        return json_decode($user->$result)->{$str[1]};
                    }
                } else {
                    return $user->$result;
                }
            }
        }
        return '';
    }
}

if (!function_exists('variables_result')) {
    function variables_result($item, $url)
    {
        if (is_null($item)) {
            $author_name = '';
            $author_first_name = '';
            $author_last_name = '';
            $author_middle_name = '';
            $resource_url = url('');
            $resource_title = '';
            $resource_excerpt = '';
            $resource_meta_title = strip_tags(seo('seo_home_meta_title'));
            $resource_meta_description = strip_tags(seo('seo_home_meta_description'));
            $resource_meta_keywords = strip_tags(seo('seo_home_meta_keywords'));
            $resource_status = '';
            $resource_created_at = '';
            $resource_updated_at = '';
        } else {
            $author_name = check_author($item->author_id, 'name');
            $author_first_name = check_author($item->author_id, 'fullName->firstName');
            $author_last_name = check_author($item->author_id, 'fullName->lastName');
            $author_middle_name = check_author($item->author_id, 'fullName->middleName');
            $resource_url = $url;
            $resource_title = $item->title;
            $resource_excerpt = strip_tags($item->excerpt);
            $resource_meta_title = strip_tags($item->meta_title);
            $resource_meta_description = strip_tags($item->meta_description);
            $resource_meta_keywords = strip_tags($item->meta_keywords);
            $resource_status = isset($item->status) ? $item->status : '';
            $resource_created_at = date('Y-m-d', strtotime($item->created_at));
            $resource_updated_at = date('Y-m-d', strtotime($item->updated_at));
        }
        $website_url = env('APP_URL');
        $website_title = setting('site_title');
        $website_excerpt = setting('site_excerpt');
        $website_description = setting('site_description');
        return [
            $author_name,
            $author_first_name,
            $author_last_name,
            $author_middle_name,
            $resource_url,
            $resource_title,
            $resource_excerpt,
            $resource_meta_title,
            $resource_meta_description,
            $resource_meta_keywords,
            $resource_status,
            $resource_created_at,
            $resource_updated_at,
            $website_url,
            $website_title,
            $website_excerpt,
            $website_description
        ];
    }
}

if (!function_exists('meta_title')) {
    function meta_title($name, $url, $item)
    {
        $global = seo('seo_' . $name . '_meta_title');
        if($name === 'home') {
            $meta_title = is_null($global) ? _trans('maia::resources.home') : str_replace(variables_array(), variables_result(null, null), $global);
        } else {
            if(!is_null($item->meta_title)) {
                $meta_title = str_replace(variables_array(), variables_result($item, $url), $item->meta_title);
            } else {
                $meta_title = str_replace(variables_array(), variables_result($item, $url), $item->title);
            }
        }
        return $meta_title;
    }
}

if (!function_exists('meta_description')) {
    function meta_description($name, $url, $item)
    {
        $global = seo('seo_' . $name . '_meta_description');
        if($name === 'home') {
            $meta_description = str_replace(variables_array(), variables_result($item, $url), $global);
        } else {
            $meta_description = '';
            if(!is_null($item->meta_description)) {
                $meta_description = str_replace(variables_array(), variables_result($item, $url), $item->meta_description);
            } elseif (!is_null($global)) {
                $meta_description = str_replace(variables_array(), variables_result($item, $url), $global);
            } elseif (!is_null($item->excerpt)) {
                $meta_description = str_replace(variables_array(), variables_result($item, $url), $item->excerpt);
            }
        }
        return $meta_description;
    }
}

if (!function_exists('meta_keywords')) {
    function meta_keywords($name, $url, $item)
    {
        $global = seo('seo_' . $name . '_meta_keywords');
        if ($name === 'home') {
            $meta_keywords = str_replace(variables_array(), variables_result($item, $url), $global);
        } else {
            if (!is_null($item->meta_keywords)) {
                $meta_keywords = str_replace(variables_array(), variables_result($item, $url), $item->meta_keywords);
            } else {
                $meta_keywords = is_null($global) ? '' : str_replace(variables_array(), variables_result($item, $url), $global);
            }
        }
        return $meta_keywords;
    }
}

if (!function_exists('json_ld')) {
    function json_ld($name, $url, $item)
    {
        $global = seo('seo_' . $name . '_json_ld');
        if ($name === 'home') {
            $json_ld = str_replace(variables_array(), variables_result($item, $url), $global);
        } else {
            if ($item->json_ld !== '') {
                $json_ld = str_replace(variables_array(), variables_result($item, $url), $item->json_ld);
            } else {
                $json_ld = is_null($global) ? '' : str_replace(variables_array(), variables_result($item, $url), $global);
            }
        }
        return $json_ld;
    }
}

if (!function_exists('open_graph')) {
    function open_graph($name, $url, $item)
    {
        $global = seo('seo_' . $name . '_open_graph');
        if ($name === 'home') {
            $open_graph = str_replace(variables_array(), variables_result($item, $url), $global);
        } else {
            if ($item->open_graph !== '') {
                $open_graph = str_replace(variables_array(), variables_result($item, $url), $item->open_graph);
            } else {
                $open_graph = is_null($global) ? '' : str_replace(variables_array(), variables_result($item, $url), $global);
            }
        }
        return $open_graph;
    }
}

if (!function_exists('jsonProp')) {
    function jsonProp($json, $property)
    {
        $return = null;
        if(Str::contains($property, '->')) {
            $property = explode('->', $property, 2);
            if (!is_null($json) && property_exists(json_decode($json), $property[0]) && property_exists(json_decode($json)->{$property[0]}, $property[1])) {
                $return =  json_decode($json)->{$property[0]}->{$property[1]};
            }
        } else {
            if (!is_null($json) && property_exists(json_decode($json), $property)) {
                $return =  json_decode($json)->$property;
            }
        }
        return $return;
    }
}

if (!function_exists('put_session_view')) {
    function put_session_view($table, $item)
    {
        if (Request::session()->has('_session_view')) {
            if(!in_array($table . '_' . $item->id, session('_session_view'))) {
                !is_null($item->view_unique) ? DB::table($table)->where('id', $item->id)->increment('view_unique') : DB::table($table)->where('id', $item->id)->update(['view_unique' => 1]);
            }
        } else {
            Request::session()->put('_session_view', [$table . '_' . $item->id]);
            !is_null($item->view_unique) ? DB::table($table)->where('id', $item->id)->increment('view_unique') : DB::table($table)->where('id', $item->id)->update(['view_unique' => 1]);
        }
        !is_null($item->view) ? DB::table($table)->where('id', $item->id)->increment('view') : DB::table($table)->where('id', $item->id)->update(['view' => 1]);
    }
}

if (!function_exists('getTemplate')) {
    function getTemplate($type)
    {
        if (File::exists(resource_path('views/templates/' . $type))) {
            $files = File::allFiles(resource_path('views/templates/' . $type));
            $templates = ['default' => _trans('maia::resources.default')];
            foreach ($files as $file) {
                if($file->getContents()[0] === '{') {
                    $contents = trim(str_replace('Template:', '', preg_split('/\{{--|\--}}(, *)?/', $file->getContents(), -1, PREG_SPLIT_NO_EMPTY)[0]));
                    if($contents !== 'Example') {
                        $names = str_replace('.blade.php', '', $file->getFilename());
                        $templates = Arr::add($templates, $names, $contents);
                    }
                }
            }
        } else {
            $templates = ['default' => _trans('maia::resources.default')];
        }
        return $templates;
    }
}

if (!function_exists('body_class')) {
    function body_class()
    {
        $request = Request::url();
        $routeName = str_replace(['maia.', 'parent-'], '', Route::currentRouteName());
        $url = explode('/', $request)[sizeof(explode('/', $request)) - 1];
        return $routeName . ' ' . Str::slug($url, '-');
    }
}

if (!function_exists('isNofollow')) {
    function isNofollow($path)
    {
        return Request::is($path) ? ' rel="nofollow"' : '';
    }
}

if (!function_exists('isBlog')) {
    function isBlog()
    {
        if(!Schema::hasTable('settings')) {
            return false;
        }
        if (!Cache::has('siteBlog')) {
            Cache::forever('siteBlog', boolval(setting('site_blog')));
        }
        return Cache::get('siteBlog');
    }
}

if (!function_exists('isPortfolio')) {
    function isPortfolio()
    {
        if(!Schema::hasTable('settings')) {
            return false;
        }
        if (!Cache::has('sitePortfolio')) {
            Cache::forever('sitePortfolio', boolval(setting('site_portfolio')));
        }
        return Cache::get('sitePortfolio');
    }
}

if (!function_exists('isShop')) {
    function isShop()
    {
        if(!Schema::hasTable('settings')) {
            return false;
        }
        if (!Cache::has('siteShop')) {
            Cache::forever('siteShop', boolval(setting('site_shop')));
        }
        return Cache::get('siteShop');
    }
}

if (!function_exists('isActiveShop')) {
    function isActiveShop()
    {
        if(!Schema::hasTable('settings')) {
            return false;
        }
        if(Cache::get('siteShop')) {
            $shop = Models\Shop::where(['author_id' => Auth::id(), 'deleted_at' => null])->whereIn('status', ['published', 'pending'])->count();
            if($shop > 0) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('siteIndex')) {
    function siteIndex()
    {
        if (!Cache::has('siteIndex')) {
            Cache::forever('siteIndex', boolval(setting('site_index')));
        }
        return Cache::get('siteIndex');
    }
}

if (!function_exists('siteFavicon')) {
    function siteFavicon()
    {
        if (!Cache::has('siteFavicon')) {
            Cache::forever('siteFavicon', setting('site_favicon'));
        }
        return Cache::get('siteFavicon');
    }
}

if (!function_exists('isDeveloper')) {
    function isDeveloper($user)
    {
        foreach ($user->roles as $role) {
            if($role->name === 'developer') {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('isParent')) {
    function isParent($single)
    {
        if(isset($single->parent_id) && !is_null($single->parent_id)) {
            return $single->parent->getUrl(true);
        }
        return '';
    }
}

if (!function_exists('isPagination')) {
    function isPagination($single, $type)
    {
        $paginate = $single->paginateItems;
        if(isset($paginate)) {
            if($type === 'first') {
                return $single->getUrl(true);
            } elseif ($type === 'last') {
                return url()->current() . '?page=' . $paginate->lastPage();
            } elseif ($type === 'next') {
                return $paginate->nextPageUrl();
            } elseif ($type === 'prev') {
                return str_replace('?page=1', '', $paginate->previousPageUrl());
            }
        }
        return '';
    }
}

if (!function_exists('successSvg')) {
    function successSvg()
    {
        return "<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' aria-labelledby='check-circle' role='presentation' class='fill-current text-success'><path d='M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z'></path></svg>";
    }
}

if (!function_exists('errorSvg')) {
    function errorSvg()
    {
        return "<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' aria-labelledby='x-circle' role='presentation' class='fill-current text-danger'><path d='M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z'></path></svg>";
    }
}

if (!function_exists('linkSvg')) {
    function linkSvg($url)
    {
        return "<a style='padding-top: 2px; text-decoration: none' class='inline-flex cursor-pointer text-70 hover:text-primary' href='{$url}' target='_blank' aria-role='button'><svg width='22' height='22' class='fill-current' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'><path d='M9.26 13a2 2 0 0 1 .01-2.01A3 3 0 0 0 9 5H5a3 3 0 0 0 0 6h.08a6.06 6.06 0 0 0 0 2H5A5 5 0 0 1 5 3h4a5 5 0 0 1 .26 10zm1.48-6a2 2 0 0 1-.01 2.01A3 3 0 0 0 11 15h4a3 3 0 0 0 0-6h-.08a6.06 6.06 0 0 0 0-2H15a5 5 0 0 1 0 10h-4a5 5 0 0 1-.26-10z' /></svg></a>";
    }
}

if (!function_exists('getVariableVue')) {
    function getVariableVue($item)
    {
        $user = Auth::user();
        $avatar = 'https://secure.gravatar.com/avatar';
        if($user && !is_null($user->avatar)) {
            $avatar = !is_null($user->avatar) ? Maia::image($user->avatar) : $avatar . md5($user->email) .  '?size=512';
        }
        JavaScript::put([
            'resource' => (object)[
                'name' => get_class($item),
                'id' => $item->id
            ],
            'user' => (object)[
                'auth' => $user ? true : false,
                'name' => $user ? $user->getName() : 'guest',
                'id' => $user ? $user->id : 0,
                'avatar' => $avatar
            ]
        ]);
    }
}

if (! function_exists('_trans')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array   $replace
     * @param  string|null  $locale
     * @return Translator|string|array|null
     */
    function _trans($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return app('translator');
        }

        return app('translator')->trans($key, $replace, $locale);
    }
}