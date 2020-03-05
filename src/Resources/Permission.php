<?php

namespace SpaceCode\Maia;

use Illuminate\Http\Request;
use SpaceCode\Maia\Fields\RoleBooleanGroup;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use SpaceCode\Maia\PermissionRegistrar;

class Permission extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\Permission::class;
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];
    public static function getModel()
    {
        return app(PermissionRegistrar::class)->getPermissionClass();
    }
    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return trans('maia::navigation.sidebar-assignment');
    }
    /**
     * Determine if this resource is available for navigation.
     *
     * @param Request $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return Gate::allows('viewAny', app(PermissionRegistrar::class)->getPermissionClass());
    }
    public static function label()
    {
        return trans('maia::resources.permissions');
    }
    public static function singularLabel()
    {
        return trans('maia::resources.permission');
    }
    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });
        $userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));
        return [
            ID::make()->sortable(),
            Text::make(trans('maia::resources.name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:permissions')
                ->updateRules('unique:permissions,name,{{resourceId}}'),

            Text::make(trans('maia::resources.display_name'), function () {
                return trans('maia::resources.display_names.'.$this->name);
            })->canSee(function () {
                return is_array(trans('maia::resources.display_names'));
            }),
            Select::make(trans('maia::resources.guard_name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),
            DateTime::make(trans('maia::resources.created_at'), 'created_at')->exceptOnForms(),
            DateTime::make(trans('maia::resources.updated_at'), 'updated_at')->exceptOnForms(),
            RoleBooleanGroup::make(trans('maia::resources.roles')),
            MorphToMany::make(trans('maia::resources.' . strtolower($userResource::label())), 'users', $userResource)
                ->searchable()
                ->singularLabel($userResource::singularLabel()),
        ];
    }
    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }
    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }
    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }
    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new Action\AttachToRole,
        ];
    }
}
