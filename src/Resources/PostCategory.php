<?php

namespace SpaceCode\Maia\Resources;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;
use SpaceCode\Maia\Fields\SluggableText;
use SpaceCode\Maia\Fields\Slug;
use SpaceCode\Maia\Fields\Tabs;
use SpaceCode\Maia\Fields\TabsOnEdit;
use SpaceCode\Maia\Fields\Toggle;
use Waynestate\Nova\CKEditor;

class PostCategory extends Resource
{
    use TabsOnEdit;

    protected $casts = [
        'index' => 'array'
    ];

    /**
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\PostCategory::class;

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
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return trans('maia::navigation.sidebar-blog');
    }

    /**
     * @return array|string|null
     */
    public static function label()
    {
        return trans('maia::resources.postCategories');
    }

    /**
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return trans('maia::resources.postCategory');
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
        return [
            (new Tabs($this->singularLabel(), [
                trans('maia::resources.general') => [
                    ID::make()->asBigInt()->sortable(),

                    Select::make(trans('maia::resources.guard_name'), 'guard_name')
                        ->options($guardOptions->toArray())
                        ->rules('required', Rule::in($guardOptions))
                        ->hideFromIndex(),

                    Select::make(trans('maia::resources.template'), 'template')
                        ->options(getTemplate('postCategories'))
                        ->rules('required')
                        ->hideFromIndex()
                        ->displayUsingLabels(),

                    Number::make(trans('maia::resources.order'), 'order')
                        ->min(1)
                        ->max(1000)
                        ->step(1)
                        ->rules('required', 'digits_between:1,1000')
                        ->sortable()
                ],
                trans('maia::resources.parent') => [
                    BelongsTo::make(trans('maia::resources.parent'), 'parent', self::class)
                        ->nullable()
                        ->searchable()
                ],
                trans('maia::resources.content') => [
                    Image::make(trans('maia::resources.image'), 'image')
                        ->disk(config('maia.filemanager.disk'))
                        ->path('postCategories/images')
                        ->deletable(false)
                        ->prunable(),

                    SluggableText::make(trans('maia::resources.title'), 'title')
                        ->slug()
                        ->rules('required', 'max:255')
                        ->sortable(),

                    Slug::make(trans('maia::resources.slug'), 'slug')
                        ->slugUnique()
                        ->onlyOnForms()
                        ->slugModel(static::$model)
                        ->rules('required', 'max:255'),

                    Text::make(trans('maia::resources.site.url'), 'slug', function () {
                        if(seo('seo_post_categories_show_index')) {
                            return linkSvg($this->getUrl(true));
                        } else {
                            return "<p>—</p>";
                        }
                    })->exceptOnForms()->asHtml(),

                    Textarea::make(trans('maia::resources.excerpt'), 'excerpt')
                        ->rules('max:255')
                        ->hideFromIndex(),

                    CKEditor::make(trans('maia::resources.body'), 'body')->options([
                        'height' => 600,
                        'toolbar' => [
                            ['Undo','Redo', '-'],
                            ['Bold','Italic','Strike','-','Subscript','Superscript'],
                            ['NumberedList','BulletedList','-','Outdent','Indent', '-', 'Blockquote','CreateDiv'],
                            ['Image','Table','SpecialChar', '-'],
                            ['JustifyLeft','JustifyCenter','JustifyRight'],
                            ['Link','Unlink','Anchor'],
                            '/',
                            ['Source', '-', 'Replace', 'RemoveFormat'],
                            ['Format'],
                            ['Maximize', 'ShowBlocks','-']
                        ],
                        'language' => env('APP_LOCALE'),
                        'format_tags' => 'p;h1;h2;h3;h4;h5;h6;pre;address;div'
                    ])->hideFromIndex(),

                    Text::make(trans('maia::resources.robots'), 'index')
                        ->onlyOnIndex()
                        ->displayUsing(function() {
                            $robots = is_null(jsonProp($this->index, 'robots')) ? successSvg() : errorSvg();
                            $google = is_null(jsonProp($this->index, 'google')) ? successSvg() : errorSvg();
                            $yandex = is_null(jsonProp($this->index, 'yandex')) ? successSvg() : errorSvg();
                            $bing = is_null(jsonProp($this->index, 'duck')) ? successSvg() : errorSvg();
                            $duck = is_null(jsonProp($this->index, 'google')) ? successSvg() : errorSvg();
                            $baidu = is_null(jsonProp($this->index, 'baidu')) ? successSvg() : errorSvg();
                            $yahoo = is_null(jsonProp($this->index, 'yahoo')) ? successSvg() : errorSvg();
                            return $robots . $google . $yandex . $bing . $duck . $baidu . $yahoo;
                        })->asHtml(),

                    DateTime::make(trans('maia::resources.created_at'), 'created_at')
                        ->exceptOnForms()
                        ->hideFromIndex(),

                    Text::make(trans('maia::resources.created_at'), 'created_at')
                        ->onlyOnIndex()
                        ->sortable()
                        ->displayUsing(function($date) {
                            return $date->diffForHumans();
                        }),

                    DateTime::make(trans('maia::resources.updated_at'), 'updated_at')
                        ->exceptOnForms()
                        ->hideFromIndex(),

                    Text::make(trans('maia::resources.updated_at'), 'updated_at')
                        ->onlyOnIndex()
                        ->sortable()
                        ->displayUsing(function($date) {
                            return $date->diffForHumans();
                        })
                ],
                trans('maia::resources.meta_fields') => [
                    Select::make(trans('maia::resources.document_state'), 'document_state')
                        ->options(['static' => trans('maia::resources.static'), 'dynamic' => trans('maia::resources.dynamic')])
                        ->displayUsingLabels()
                        ->rules('required')
                        ->hideFromIndex(),

                    Text::make(trans('maia::resources.meta_title'), 'meta_title')
                        ->rules('max:55')
                        ->hideFromIndex(),

                    Textarea::make(trans('maia::resources.meta_description'), 'meta_description')
                        ->hideFromIndex(),

                    Textarea::make(trans('maia::resources.meta_keywords'), 'meta_keywords')
                        ->hideFromIndex()
                ],
                trans('maia::resources.json_ld') => [
                    Textarea::make(trans('maia::resources.json_ld'), 'json_ld')
                        ->hideFromIndex()
                ],
                trans('maia::resources.open_graph') => [
                    Textarea::make(trans('maia::resources.open_graph'), 'open_graph')
                        ->hideFromIndex()
                ],
                trans('maia::resources.indexing') => [
                    Toggle::make(trans('maia::resources.robots'), 'index->robots')->resolveUsing(function () {
                        return is_null(jsonProp($this->index, 'robots')) ? 1 : json_decode($this->index)->robots;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'robots')) && json_decode($this->index)->robots === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(trans('maia::resources.googlebot'), 'index->google')->resolveUsing(function () {
                        return is_null(jsonProp($this->index, 'google')) ? 1 : json_decode($this->index)->google;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'google')) && json_decode($this->index)->google === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(trans('maia::resources.yandexbot'), 'index->yandex')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'yandex')) ? json_decode($this->index)->yandex : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'yandex')) && json_decode($this->index)->yandex === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(trans('maia::resources.bingbot'), 'index->bing')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'bing')) ? json_decode($this->index)->bing : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'bing')) && json_decode($this->index)->bing === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(trans('maia::resources.duckbot'), 'index->duck')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'duck')) ? json_decode($this->index)->duck : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'duck')) && json_decode($this->index)->duck === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(trans('maia::resources.baidubot'), 'index->baidu')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'baidu')) ? json_decode($this->index)->baidu : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'baidu')) && json_decode($this->index)->baidu === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    Toggle::make(trans('maia::resources.yahoobot'), 'index->yahoo')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'yahoo')) ? json_decode($this->index)->yahoo : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'yahoo')) && json_decode($this->index)->yahoo === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
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
