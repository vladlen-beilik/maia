<?php

namespace SpaceCode\Maia\Resources;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;
use SpaceCode\Maia\Fields\Tabs;
use SpaceCode\Maia\Fields\TabsOnEdit;

class Comment extends Resource
{
    use TabsOnEdit;

    protected $casts = [
        'index' => 'array'
    ];

    /**
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\Comment::class;

    /**
     * @var string
     */
    public static $id = 'id';

    /**
     * @var array
     */
    public static $statuses = [
        'pending' => 'warning',
        'published' => 'success',
        'deleted' => 'danger'
    ];

    /**
     * @return array|string|null
     */
    public static function label()
    {
        return trans('maia::resources.comments');
    }

    /**
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return trans('maia::resources.comment');
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
//        if (Auth::user()->hasRole('developer') || $this->author_id === Auth::user()->id) {
//            $author = BelongsTo::make(trans('maia::resources.author'), 'user', 'App\Nova\User')
//                ->rules('required')
//                ->hideWhenCreating()
//                ->sortable();
//        } else {
//            $author = BelongsTo::make(trans('maia::resources.author'), 'user', 'App\Nova\User')
//                ->rules('required')
//                ->hideWhenCreating()
//                ->sortable()
//                ->readonly();
//        }

        return [
            (new Tabs($this->singularLabel(), [
                trans('maia::resources.general') => [
                    ID::make()->asBigInt()->sortable(),

                    Select::make(trans('maia::resources.guard_name'), 'guard_name')
                        ->options($guardOptions->toArray())
                        ->rules('required', Rule::in($guardOptions))
                        ->sortable(),

//                    $author,

                    Badge::make(trans('maia::resources.status'), 'status', function () {
                        if (!is_null($this->deleted_at))
                            return 'deleted';
                        return $this->status;
                    })->map(static::$statuses)
                        ->sortable(),

                    Select::make(trans('maia::resources.status'), 'status')
                        ->options(collect(static::$model::$statuses)->mapWithKeys(function ($key) {
                            return [$key => ucfirst($key)];
                        }))->onlyOnForms()
                        ->rules('required')
                        ->displayUsingLabels()
                ],
//                trans('maia::resources.parent') => [
//                    BelongsTo::make(trans('maia::resources.parent'), 'parent', self::class)
//                        ->nullable()
//                        ->searchable()
//                ],
                trans('maia::resources.content') => [
                    Textarea::make(trans('maia::resources.body'), 'body')
                        ->rules('required')
                        ->hideFromIndex(),

                    DateTime::make(trans('maia::resources.created_at'), 'created_at')
                        ->exceptOnForms()
                        ->sortable(),

                    DateTime::make(trans('maia::resources.updated_at'), 'updated_at')
                        ->exceptOnForms()
                        ->sortable()
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
