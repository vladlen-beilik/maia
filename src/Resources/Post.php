<?php

namespace SpaceCode\Maia\Resources;

use App\Nova\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;
use SpaceCode\Maia\Fields\Editor;
use SpaceCode\Maia\Fields\Hidden;
use SpaceCode\Maia\Fields\SluggableText;
use SpaceCode\Maia\Fields\Slug;
use SpaceCode\Maia\Fields\Tabs;
use SpaceCode\Maia\Fields\TabsOnEdit;
use SpaceCode\Maia\Fields\Toggle;

class Post extends Resource
{
    use TabsOnEdit;

    protected $casts = [
        'index' => 'array'
    ];

    /**
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\Post::class;

    /**
     * @var string
     */
    public static $title = 'title';

    /**
     * @var array
     */
    public static $search = [
        'slug', 'title',
    ];

    /**
     * @var array
     */
    public static $statuses = [
        'pending' => 'warning',
        'published' => 'success',
        'deleted' => 'danger'
    ];

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return _trans('maia::navigation.sidebar-blog');
    }

    /**
     * @return array|string|null
     */
    public static function label()
    {
        return _trans('maia::resources.posts');
    }

    /**
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return _trans('maia::resources.post');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });
        if (Auth::user()->hasRole('developer') || $this->author_id === Auth::user()->id) {
            $author = BelongsTo::make(_trans('maia::resources.author'), 'user', User::class)
                ->rules('required')
                ->hideWhenCreating()
                ->sortable();
        } else {
            $author = BelongsTo::make(_trans('maia::resources.author'), 'user', User::class)
                ->rules('required')
                ->hideWhenCreating()
                ->sortable()
                ->readonly();
        }
        return [
            (new Tabs($this->singularLabel(), [
                _trans('maia::resources.general') => [
                    ID::make()->asBigInt()->sortable(),

                    Select::make(_trans('maia::resources.guard_name'), 'guard_name')
                        ->options($guardOptions->toArray())
                        ->rules('required', Rule::in($guardOptions))
                        ->hideFromIndex(),

                    $author,

                    Select::make(_trans('maia::resources.template'), 'template')
                        ->options(getTemplate('portfolio'))
                        ->rules('required')
                        ->hideFromIndex()
                        ->displayUsingLabels(),

                    Badge::make(_trans('maia::resources.status'), 'status', function () {
                        if (!is_null($this->deleted_at))
                            return 'deleted';
                        return $this->status;
                    })->map(static::$statuses)
                        ->sortable(),

                    Select::make(_trans('maia::resources.status'), 'status')
                        ->options(collect(static::$model::$statuses)->mapWithKeys(function ($key) {
                            return [$key => ucfirst($key)];
                        }))->onlyOnForms()
                        ->rules('required')
                        ->displayUsingLabels(),

                    Toggle::make(_trans('maia::resources.comments'), 'comments')
                        ->displayUsing(function () {
                            return $value === 0 ? _trans('maia::resources.inactive') : _trans('maia::resources.active');
                        })->hideFromIndex(),

                    Text::make(_trans('maia::resources.view'), 'view')
                        ->displayUsing(function () {
                            $view = is_null($this->view) ? 0 : intval($this->view);
                            $unique = is_null($this->view_unique) ? 0 : intval($this->view_unique);
                            return $view === $unique ? _trans('maia::resources.visitors.all', ['view' => $view]) : _trans('maia::resources.visitors.unique', ['view' => $view, 'unique' => $unique]);
                        })->exceptOnForms()
                        ->hideFromIndex()
                ],
                _trans('maia::resources.content') => [
                    Image::make(_trans('maia::resources.image'), 'image')
                        ->disk(config('maia.filemanager.disk'))
                        ->path('posts/images')
                        ->deletable(false)
                        ->prunable(),

                    SluggableText::make(_trans('maia::resources.title'), 'title')
                        ->slug()
                        ->rules('required', 'max:255')
                        ->sortable(),

                    Slug::make(_trans('maia::resources.slug'), 'slug')
                        ->onlyOnForms()
                        ->rules('required', 'max:255')
                        ->creationRules('unique:posts,slug')
                        ->updateRules('unique:posts,slug,{{resourceId}}'),

                    Text::make(_trans('maia::resources.site.url'), 'slug', function () {
                        return $this->id ? linkSvg($this->getUrl(true)) : null;
                    })->exceptOnForms()->asHtml(),

                    Textarea::make(_trans('maia::resources.excerpt'), 'excerpt')
                        ->rules('max:255')
                        ->hideFromIndex(),

                    Editor::make(_trans('maia::resources.body'), 'body')->withFiles(config('maia.filemanager.disk'))
                        ->hideFromIndex(),

                    Boolean::make(_trans('maia::resources.comments'), 'comments')
                        ->onlyOnIndex(),

                    Text::make(_trans('maia::resources.robots'), 'index')
                        ->onlyOnIndex()
                        ->displayUsing(function() {
                            $robots = !is_null(jsonProp($this->index, 'robots')) && json_decode($this->index)->robots === '1' ? successSvg() : errorSvg();
                            $google = !is_null(jsonProp($this->index, 'google')) && json_decode($this->index)->google === '1' ? successSvg() : errorSvg();
                            $yandex = !is_null(jsonProp($this->index, 'yandex')) && json_decode($this->index)->yandex === '1' ? successSvg() : errorSvg();
                            $bing = !is_null(jsonProp($this->index, 'bing')) && json_decode($this->index)->bing === '1' ? successSvg() : errorSvg();
                            $duck = !is_null(jsonProp($this->index, 'duck')) && json_decode($this->index)->duck === '1' ? successSvg() : errorSvg();
                            $baidu = !is_null(jsonProp($this->index, 'baidu')) && json_decode($this->index)->baidu === '1' ? successSvg() : errorSvg();
                            $yahoo = !is_null(jsonProp($this->index, 'yahoo')) && json_decode($this->index)->yahoo === '1' ? successSvg() : errorSvg();
                            return $robots . $google . $yandex . $bing . $duck . $baidu . $yahoo;
                        })->asHtml(),

                    DateTime::make(_trans('maia::resources.created_at'), 'created_at')
                        ->exceptOnForms()
                        ->hideFromIndex(),

                    Text::make(_trans('maia::resources.created_at'), 'created_at')
                        ->onlyOnIndex()
                        ->sortable()
                        ->displayUsing(function($date) {
                            return $date->diffForHumans();
                        }),

                    DateTime::make(_trans('maia::resources.updated_at'), 'updated_at')
                        ->exceptOnForms()
                        ->hideFromIndex(),

                    Text::make(_trans('maia::resources.updated_at'), 'updated_at')
                        ->onlyOnIndex()
                        ->sortable()
                        ->displayUsing(function($date) {
                            return $date->diffForHumans();
                        }),
                ],
                _trans('maia::resources.categories') => [
                    BelongsToMany::make(_trans('maia::resources.categories'), 'categories', PostCategory::class)->fields(function () {
                        return [
                            Hidden::make('type')->default('post_category')
                        ];
                    })
                ],
                _trans('maia::resources.tags') => [
                    BelongsToMany::make(_trans('maia::resources.tags'), 'tags', PostTag::class)->fields(function () {
                        return [
                            Hidden::make('type')->default('post_tag')
                        ];
                    })
                ],
                _trans('maia::resources.meta_fields') => [
                    Select::make(_trans('maia::resources.document_state'), 'document_state')
                        ->options(['static' => _trans('maia::resources.static'), 'dynamic' => _trans('maia::resources.dynamic')])
                        ->displayUsingLabels()
                        ->rules('required')
                        ->hideFromIndex(),

                    Text::make(_trans('maia::resources.meta_title'), 'meta_title')
                        ->rules('max:55')
                        ->hideFromIndex(),

                    Textarea::make(_trans('maia::resources.meta_description'), 'meta_description')
                        ->hideFromIndex(),

                    Textarea::make(_trans('maia::resources.meta_keywords'), 'meta_keywords')
                        ->hideFromIndex()
                ],
                _trans('maia::resources.json_ld') => [
                    Textarea::make(_trans('maia::resources.json_ld'), 'json_ld')
                        ->hideFromIndex()
                ],
                _trans('maia::resources.open_graph') => [
                    Textarea::make(_trans('maia::resources.open_graph'), 'open_graph')
                        ->hideFromIndex()
                ],
                _trans('maia::resources.indexing') => [
                    Toggle::make(_trans('maia::resources.robots'), 'index->robots')->resolveUsing(function () {
                        return is_null(jsonProp($this->index, 'robots')) ? 1 : json_decode($this->index)->robots;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'robots')) && json_decode($this->index)->robots === '1' ? _trans('maia::resources.on') : _trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(_trans('maia::resources.googlebot'), 'index->google')->resolveUsing(function () {
                        return is_null(jsonProp($this->index, 'google')) ? 1 : json_decode($this->index)->google;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'google')) && json_decode($this->index)->google === '1' ? _trans('maia::resources.on') : _trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(_trans('maia::resources.yandexbot'), 'index->yandex')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'yandex')) ? json_decode($this->index)->yandex : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'yandex')) && json_decode($this->index)->yandex === '1' ? _trans('maia::resources.on') : _trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(_trans('maia::resources.bingbot'), 'index->bing')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'bing')) ? json_decode($this->index)->bing : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'bing')) && json_decode($this->index)->bing === '1' ? _trans('maia::resources.on') : _trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(_trans('maia::resources.duckbot'), 'index->duck')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'duck')) ? json_decode($this->index)->duck : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'duck')) && json_decode($this->index)->duck === '1' ? _trans('maia::resources.on') : _trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(_trans('maia::resources.baidubot'), 'index->baidu')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'baidu')) ? json_decode($this->index)->baidu : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'baidu')) && json_decode($this->index)->baidu === '1' ? _trans('maia::resources.on') : _trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(_trans('maia::resources.yahoobot'), 'index->yahoo')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'yahoo')) ? json_decode($this->index)->yahoo : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'yahoo')) && json_decode($this->index)->yahoo === '1' ? _trans('maia::resources.on') : _trans('maia::resources.off');
                    })->hideFromIndex()
                ]
            ]))->withToolbar()
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
