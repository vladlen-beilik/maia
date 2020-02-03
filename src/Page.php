<?php

namespace SpaceCode\Maia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use SpaceCode\Maia\PermissionRegistrar;
use SpaceCode\Maia\SluggableText;
use SpaceCode\Maia\Slug;

class Page extends Resource
{
    /**
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\Page::class;

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
     * @return array|string|null
     */
    public static function label()
    {
        return __('maia::resources.pages');
    }

    /**
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return __('maia::resources.page');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $user = Auth::user();
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });
        $templates = [
            'default' => 'Default',
        ];
        $statuses = collect(\SpaceCode\Maia\Models\Page::$statuses)->mapWithKeys(function ($key) {
            return [$key => $key];
        });
        if($user->hasRole('developer') || $this->author_id === $user->id) {
            $author = BelongsTo::make(__('maia::resources.author'), 'user', 'App\Nova\User')
                ->rules(['required'])
                ->hideWhenCreating()
                ->sortable();
        } else {
            $author = BelongsTo::make(__('maia::resources.author'), 'user', 'App\Nova\User')
                ->rules(['required'])
                ->hideWhenCreating()
                ->sortable()
                ->readonly();
        }
        return [
            ID::make()->asBigInt()->sortable(),
            Select::make(__('maia::resources.guard_name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),
            $author,
            Select::make(__('maia::resources.template'), 'template')
                ->options($templates)
                ->withMeta(['value' => $this->template ?? 'default'])
                ->rules('required'),
            Select::make(__('maia::resources.status'), 'status')
                ->options($statuses)
                ->withMeta(['value' => $this->status ?? 'pending'])
                ->rules('required')
                ->sortable(),
            new Panel(__('maia::resources.main_fields'), $this->mainFields()),
            new Panel(__('maia::resources.meta_fields'), $this->metaFields()),
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

    /**
     * @return array
     */
    protected function mainFields()
    {
        return [
            SluggableText::make(__('maia::resources.title'), 'title')->slug('Slug')
                ->rules('required'),
            Slug::make(__('maia::resources.slug'), 'slug')
                ->help(__('maia::resources.slug_text'))
                ->rules('required')
                ->slugUnique()
                ->slugModel(static::$model),
            Textarea::make(__('maia::resources.excerpt'), 'excerpt')
                ->help(__('maia::resources.excerpt_text'))
                ->rules('max:255')
                ->hideFromIndex(),
            Textarea::make(__('maia::resources.body'), 'body')
                ->hideFromIndex(),
        ];
    }

    /**
     * @return array
     */
    protected function metaFields()
    {
        return [
            Text::make(__('maia::resources.meta_title'), 'meta_title')
                ->rules('max:55')
                ->help(__('maia::resources.meta_title_text'))
                ->hideFromIndex(),
            Text::make(__('maia::resources.meta_description'), 'meta_description')
                ->help(__('maia::resources.meta_description_text'))
                ->hideFromIndex(),
            Text::make(__('maia::resources.meta_keywords'), 'meta_keywords')
                ->help(__('maia::resources.meta_keywords_text'))
                ->hideFromIndex(),
            Textarea::make(__('maia::resources.json_ld'), 'json_ld')
                ->help(__('maia::resources.json_ld_text'))
                ->hideFromIndex(),
            Textarea::make(__('maia::resources.open_graph'), 'open_graph')
                ->help(__('maia::resources.open_graph_text'))
                ->hideFromIndex(),
        ];
    }
}
