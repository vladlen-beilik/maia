<?php

namespace SpaceCode\Maia;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Filesystem\Filesystem as Filesystem;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactory;
use SpaceCode\Maia\Models\Sitemap as Model;

class Sitemap
{
    /**
     * Model instance.
     *
     * @var Model
     */
    public $model = null;

    /**
     * CacheRepository instance.
     *
     * @var CacheRepository
     */
    public $cache = null;

    /**
     * ConfigRepository instance.
     *
     * @var ConfigRepository
     */
    protected $configRepository = null;

    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    protected $file = null;

    /**
     * ResponseFactory instance.
     *
     * @var ResponseFactory
     */
    protected $response = null;

    /**
     * ViewFactory instance.
     *
     * @var ViewFactory
     */
    protected $view = null;

    /**
     * Using constructor we populate our model from configuration file
     * and loading dependencies.
     *
     * @param array $config
     * @param CacheRepository $cache
     * @param ConfigRepository $configRepository
     * @param Filesystem $file
     * @param ResponseFactory $response
     * @param ViewFactory $view
     */
    public function __construct(array $config, CacheRepository $cache, ConfigRepository $configRepository, Filesystem $file, ResponseFactory $response, ViewFactory $view)
    {
        $this->cache = $cache;
        $this->configRepository = $configRepository;
        $this->file = $file;
        $this->response = $response;
        $this->view = $view;
        $this->model = new Model($config);
    }

    /**
     * Set cache options.
     *
     * @param string              $key
     * @param Carbon|Datetime|int $duration
     * @param bool                $useCache
     */
    public function setCache($key = null, $duration = null, $useCache = true)
    {
        $this->model->setUseCache($useCache);
        if (null !== $key) {
            $this->model->setCacheKey($key);
        }
        if (null !== $duration) {
            $this->model->setCacheDuration($duration);
        }
    }

    /**
     * Checks if content is cached.
     *
     * @return bool
     */
    public function isCached()
    {
        if ($this->model->getUseCache()) {
            if ($this->cache->has($this->model->getCacheKey())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add new sitemap item to $items array.
     *
     * @param string $loc
     * @param string $lastmod
     * @param string $priority
     * @param string $freq
     * @param array  $images
     * @param string $title
     * @param array  $translations
     * @param array  $videos
     * @param array  $googlenews
     * @param array  $alternates
     *
     * @return void
     */
    public function add($loc, $lastmod = null, $priority = null, $freq = null, $images = [], $title = null, $translations = [], $videos = [], $googlenews = [], $alternates = [])
    {
        $params = [
            'loc'           => $loc,
            'lastmod'       => $lastmod,
            'priority'      => $priority,
            'freq'          => $freq,
            'images'        => $images,
            'title'         => $title,
            'translations'  => $translations,
            'videos'        => $videos,
            'googlenews'    => $googlenews,
            'alternates'    => $alternates,
        ];

        $this->addItem($params);
    }

    /**
     * Add new sitemap one or multiple items to $items array.
     *
     * @param array $params
     *
     * @return void
     */
    public function addItem($params = [])
    {
        if (array_key_exists(1, $params)) {
            foreach ($params as $a) {
                $this->addItem($a);
            }
            return;
        }
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        if (! isset($loc)) {
            $loc = '/';
        }
        if (! isset($lastmod)) {
            $lastmod = null;
        }
        if (! isset($priority)) {
            $priority = null;
        }
        if (! isset($freq)) {
            $freq = null;
        }
        if (! isset($title)) {
            $title = null;
        }
        if (! isset($images)) {
            $images = [];
        }
        if (! isset($translations)) {
            $translations = [];
        }
        if (! isset($alternates)) {
            $alternates = [];
        }
        if (! isset($videos)) {
            $videos = [];
        }
        if (! isset($googlenews)) {
            $googlenews = [];
        }
        if ($this->model->getEscaping()) {
            $loc = htmlentities($loc, ENT_XML1);
            if ($title != null) {
                htmlentities($title, ENT_XML1);
            }
            if ($images) {
                foreach ($images as $k => $image) {
                    foreach ($image as $key => $value) {
                        $images[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }
            if ($translations) {
                foreach ($translations as $k => $translation) {
                    foreach ($translation as $key => $value) {
                        $translations[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }
            if ($alternates) {
                foreach ($alternates as $k => $alternate) {
                    foreach ($alternate as $key => $value) {
                        $alternates[$k][$key] = htmlentities($value, ENT_XML1);
                    }
                }
            }
            if ($videos) {
                foreach ($videos as $k => $video) {
                    if (! empty($video['title'])) {
                        $videos[$k]['title'] = htmlentities($video['title'], ENT_XML1);
                    }
                    if (! empty($video['description'])) {
                        $videos[$k]['description'] = htmlentities($video['description'], ENT_XML1);
                    }
                }
            }
            if ($googlenews) {
                if (isset($googlenews['sitename'])) {
                    $googlenews['sitename'] = htmlentities($googlenews['sitename'], ENT_XML1);
                }
            }
        }

        $googlenews['sitename'] = isset($googlenews['sitename']) ? $googlenews['sitename'] : '';
        $googlenews['language'] = isset($googlenews['language']) ? $googlenews['language'] : 'en';
        $googlenews['publication_date'] = isset($googlenews['publication_date']) ? $googlenews['publication_date'] : date('Y-m-d H:i:s');
        $this->model->setItems([
            'loc'          => $loc,
            'lastmod'      => $lastmod,
            'priority'     => $priority,
            'freq'         => $freq,
            'images'       => $images,
            'title'        => $title,
            'translations' => $translations,
            'videos'       => $videos,
            'googlenews'   => $googlenews,
            'alternates'   => $alternates,
        ]);
    }

    /**
     * Add new sitemap to $sitemaps array.
     *
     * @param string $loc
     * @param string $lastmod
     *
     * @return void
     */
    public function addSitemap($loc, $lastmod = null)
    {
        $this->model->setSitemaps([
            'loc'     => $loc,
            'lastmod' => $lastmod,
        ]);
    }

    /**
     * Add new sitemap to $sitemaps array.
     *
     * @param array $sitemaps
     * @return void
     */
    public function resetSitemaps($sitemaps = [])
    {
        $this->model->resetSitemaps($sitemaps);
    }

    /**
     * Returns document with all sitemap items from $items array.
     *
     * @param string $format (options: xml, html, txt, ror-rss, ror-rdf, google-news)
     * @param string $style (path to custom xls style like '/styles/xsl/xml-sitemap.xsl')
     *
     * @return Response
     */
    public function render($format = 'xml', $style = null)
    {
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            $this->model->limitSize($this->model->getMaxSize());
        } elseif ('google-news' == $format && count($this->model->getItems()) > 1000) {
            $this->model->limitSize(1000);
        } elseif ('google-news' != $format && count($this->model->getItems()) > 50000) {
            $this->model->limitSize();
        }
        $data = $this->generate($format, $style);
        return $this->response->make($data['content'], 200, $data['headers']);
    }

    /**
     * Generates document with all sitemap items from $items array.
     *
     * @param string $format (options: xml, html, txt, ror-rss, ror-rdf, sitemapindex, google-news)
     * @param string $style  (path to custom xls style like '/styles/xsl/xml-sitemap.xsl')
     *
     * @return array
     */
    public function generate($format = 'xml', $style = null)
    {
        if ($this->isCached()) {
            ('sitemapindex' == $format) ? $this->model->resetSitemaps($this->cache->get($this->model->getCacheKey())) : $this->model->resetItems($this->cache->get($this->model->getCacheKey()));
        } elseif ($this->model->getUseCache()) {
            ('sitemapindex' == $format) ? $this->cache->put($this->model->getCacheKey(), $this->model->getSitemaps(), $this->model->getCacheDuration()) : $this->cache->put($this->model->getCacheKey(), $this->model->getItems(), $this->model->getCacheDuration());
        }
        if (! $this->model->getLink()) {
            $this->model->setLink($this->configRepository->get('app.url'));
        }
        if (! $this->model->getTitle()) {
            $this->model->setTitle('Sitemap for '.$this->model->getLink());
        }
        $channel = [
            'title' => $this->model->getTitle(),
            'link'  => $this->model->getLink(),
        ];
        if ($this->model->getUseStyles()) {
            if (null != $this->model->getSloc() && file_exists(public_path($this->model->getSloc().$format.'.xsl'))) {
                $style = $this->model->getSloc().$format.'.xsl';
            } else {
                $style = null;
            }
        } else {
            $style = null;
        }
        switch ($format) {
            case 'ror-rss':
                return ['content' => $this->view->make('maia-sitemap::ror-rss', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/rss+xml; charset=utf-8']];
            case 'ror-rdf':
                return ['content' => $this->view->make('maia-sitemap::ror-rdf', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/rdf+xml; charset=utf-8']];
            case 'html':
                return ['content' => $this->view->make('maia-sitemap::html', ['items' => $this->model->getItems(), 'channel' => $channel, 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/html; charset=utf-8']];
            case 'txt':
                return ['content' => $this->view->make('maia-sitemap::txt', ['items' => $this->model->getItems(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/plain; charset=utf-8']];
            case 'sitemapindex':
                return ['content' => $this->view->make('maia-sitemap::sitemapindex', ['sitemaps' => $this->model->getSitemaps(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/xml; charset=utf-8']];
            default:
                return ['content' => $this->view->make('maia-sitemap::'.$format, ['items' => $this->model->getItems(), 'style' => $style])->render(), 'headers' => ['Content-type' => 'text/xml; charset=utf-8']];
        }
    }

    /**
     * Generate sitemap and store it to a file.
     *
     * @param string $format   (options: xml, html, txt, ror-rss, ror-rdf, sitemapindex, google-news)
     * @param string $filename (without file extension, may be a path like 'sitemaps/sitemap1' but must exist)
     * @param string $path     (path to store sitemap like '/www/site/public')
     * @param string $style    (path to custom xls style like '/styles/xsl/xml-sitemap.xsl')
     *
     * @return void
     */
    public function store($format = 'xml', $filename = 'sitemap', $path = null, $style = null)
    {
        $this->model->setUseCache(false);
        (in_array($format, ['txt', 'html'], true)) ? $fe = $format : $fe = 'xml';
        if (true == $this->model->getUseGzip()) {
            $fe = $fe.".gz";
        }
        if ($this->model->getMaxSize() > 0 && count($this->model->getItems()) > $this->model->getMaxSize()) {
            if ($this->model->getUseLimitSize()) {
                $this->model->limitSize($this->model->getMaxSize());
                $data = $this->generate($format, $style);
            } else {
                foreach (array_chunk($this->model->getItems(), $this->model->getMaxSize()) as $key => $item) {
                    $this->model->resetItems($item);
                    $this->store($format, $filename.'-'.$key, $path, $style);
                    if ($path != null) {
                        $this->addSitemap($filename.'-'.$key.'.'.$fe);
                    } else {
                        $this->addSitemap(url($filename.'-'.$key.'.'.$fe));
                    }
                }
                $data = $this->generate('sitemapindex', $style);
            }
        } elseif (('google-news' != $format && count($this->model->getItems()) > 50000) || ($format == 'google-news' && count($this->model->getItems()) > 1000)) {
            ('google-news' != $format) ? $max = 50000 : $max = 1000;
            if (! $this->model->getUseLimitSize()) {
                foreach (array_chunk($this->model->getItems(), $max) as $key => $item) {
                    $this->model->resetItems($item);
                    $this->store($format, $filename.'-'.$key, $path, $style);
                    if (null != $path) {
                        $this->addSitemap($filename.'-'.$key.'.'.$fe);
                    } else {
                        $this->addSitemap(url($filename.'-'.$key.'.'.$fe));
                    }
                }
                $data = $this->generate('sitemapindex', $style);
            } else {
                $this->model->limitSize($max);
                $data = $this->generate($format, $style);
            }
        } else {
            $data = $this->generate($format, $style);
        }
        if ('sitemapindex' == $format) {
            $this->model->resetSitemaps();
        }
        $this->model->resetItems();
        if (null == $path) {
            $file = public_path().DIRECTORY_SEPARATOR.$filename.'.'.$fe;
        } else {
            $file = $path.DIRECTORY_SEPARATOR.$filename.'.'.$fe;
        }
        if (true == $this->model->getUseGzip()) {
            $this->file->put($file, gzencode($data['content'], 9));
        } else {
            $this->file->put($file, $data['content']);
        }
    }
}
