<?php

namespace SpaceCode\Maia\Resources;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;
use SpaceCode\Maia\Fields\SluggableText;
use SpaceCode\Maia\Fields\Slug;
use SpaceCode\Maia\Fields\Tabs;
use SpaceCode\Maia\Fields\TabsOnEdit;
use SpaceCode\Maia\Fields\Toggle;

class PostTag extends Resource
{
    use TabsOnEdit;

    protected $casts = [
        'index' => 'array'
    ];

    /**
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\PostTag::class;

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
        return trans('maia::resources.postTags');
    }

    /**
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return trans('maia::resources.postTag');
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
                    // ID
                    ID::make()->asBigInt()->sortable(),

                    // Guard Name
                    Select::make(trans('maia::resources.guard_name'), 'guard_name')
                        ->options($guardOptions->toArray())
                        ->rules(['required', Rule::in($guardOptions)])
                        ->sortable(),

                    // Template
                    Select::make(trans('maia::resources.template'), 'template')->resolveUsing(function ($value) {
                        return is_null($this->template) ? 'default' : $value;
                    })->options(getTemplate('postTags'))
                        ->rules(['required'])
                        ->displayUsingLabels()
                ],
                trans('maia::resources.content') => [
                    // Title
                    SluggableText::make(trans('maia::resources.title'), 'title')
                        ->slug('Slug')
                        ->rules(['required'])
                        ->sortable(),

                    // Slug
                    Slug::make(trans('maia::resources.slug'), 'slug')
                        ->rules(['required'])
                        ->slugUnique()
                        ->slugModel(static::$model)
                        ->hideFromIndex(),

                    // Excerpt
                    Textarea::make(trans('maia::resources.excerpt'), 'excerpt')
                        ->rules(['max:255'])
                        ->hideFromIndex(),

                    // Body
                    Code::make(trans('maia::resources.body'), 'body')
                        ->language('php')
                        ->hideFromIndex(),

                    DateTime::make(trans('maia::resources.created_at'), 'created_at')
                        ->exceptOnForms()
                        ->sortable(),
                    DateTime::make(trans('maia::resources.updated_at'), 'updated_at')
                        ->exceptOnForms()
                        ->sortable()
                ],
                trans('maia::resources.meta_fields') => [
                    // Document State
                    Select::make(trans('maia::resources.document_state'), 'document_state')
                        ->resolveUsing(function ($value) {
                            return is_null($this->document_state) || empty($this->document_state) ? 'dynamic' : $value;
                        })->options(['static' => trans('maia::resources.static'), 'dynamic' => trans('maia::resources.dynamic')])
                        ->displayUsingLabels()
                        ->hideFromIndex(),

                    // Meta Title
                    Text::make(trans('maia::resources.meta_title'), 'meta_title')
                        ->rules(['max:55'])
                        ->hideFromIndex(),

                    // Meta Description
                    Textarea::make(trans('maia::resources.meta_description'), 'meta_description')
                        ->hideFromIndex(),

                    // Meta Keywords
                    Textarea::make(trans('maia::resources.meta_keywords'), 'meta_keywords')
                        ->hideFromIndex()
                ],
                trans('maia::resources.json_ld') => [
                    // Json-ld
                    Textarea::make(trans('maia::resources.json_ld'), 'json_ld')
                        ->hideFromIndex()
                ],
                trans('maia::resources.open_graph') => [
                    // OpenGraph
                    Textarea::make(trans('maia::resources.open_graph'), 'open_graph')
                        ->hideFromIndex()
                ],
                trans('maia::resources.indexing') => [
                    // Robots
                    Toggle::make(trans('maia::resources.robots'), 'index->robots')->resolveUsing(function () {
                        return is_null(jsonProp($this->index, 'robots')) ? 1 : json_decode($this->index)->robots;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'robots')) && json_decode($this->index)->robots === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    // Google Bot
                    Toggle::make(trans('maia::resources.googlebot'), 'index->google')->resolveUsing(function () {
                        return is_null(jsonProp($this->index, 'google')) ? 1 : json_decode($this->index)->google;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'google')) && json_decode($this->index)->google === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    // Yandex Bot
                    Toggle::make(trans('maia::resources.yandexbot'), 'index->yandex')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'yandex')) ? json_decode($this->index)->yandex : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'yandex')) && json_decode($this->index)->yandex === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    // Bing Bot
                    Toggle::make(trans('maia::resources.bingbot'), 'index->bing')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'bing')) ? json_decode($this->index)->bing : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'bing')) && json_decode($this->index)->bing === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    // DuckDuck Bot
                    Toggle::make(trans('maia::resources.duckbot'), 'index->duck')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'duck')) ? json_decode($this->index)->duck : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'duck')) && json_decode($this->index)->duck === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    // Baidu Bot
                    Toggle::make(trans('maia::resources.baidubot'), 'index->baidu')->resolveUsing(function () {
                        return !is_null(jsonProp($this->index, 'baidu')) ? json_decode($this->index)->baidu : 0;
                    })->displayUsing(function () {
                        return !is_null(jsonProp($this->index, 'baidu')) && json_decode($this->index)->baidu === '1' ? trans('maia::resources.on') : trans('maia::resources.off');
                    })->hideFromIndex(),

                    // Yahoo Bot
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
