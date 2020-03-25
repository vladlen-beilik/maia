<?php

namespace SpaceCode\Maia\Controllers;

use SpaceCode\Maia\Robots;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    /**
     * @param Robots $robots
     * @return ResponseFactory|Response
     */
    public function __invoke(Robots $robots)
    {
        if(!siteIndex()) {
            $robots->addUserAgent('*');
            $robots->addDisallow('/');
            $robots->addSpacer();
            $robots->addUserAgent('Googlebot');
            $robots->addDisallow('/');
            $robots->addSpacer();
            $robots->addUserAgent('Yandex');
            $robots->addDisallow('/');
            $robots->addSpacer();
            $robots->addUserAgent('Bingbot');
            $robots->addDisallow('/');
            $robots->addSpacer();
            $robots->addUserAgent('DuckDuckBot');
            $robots->addDisallow('/');
            $robots->addSpacer();
            $robots->addUserAgent('Baiduspider');
            $robots->addDisallow('/');
            $robots->addSpacer();
            $robots->addUserAgent('Slurp');
            $robots->addDisallow('/');
        } else {
            $robots->addUserAgent('*');
            $robots->addDisallow('');
            $robots->addDisallow(config('nova.path'));
            $robots->addSpacer();
            $robots->addHost(str_replace(['https://', 'http://'], '', url('/')));
            $robots->addSpacer();
            $robots->addSitemap(url('sitemap.xml'));
        }
        return response($robots->generate(), 200, ['Content-Type' => 'text/plain']);
    }
}