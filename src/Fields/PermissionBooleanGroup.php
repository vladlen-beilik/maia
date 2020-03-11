<?php

namespace SpaceCode\Maia\Fields;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Http\Requests\NovaRequest;
use SpaceCode\Maia\Models\Permission as PermissionModel;
use SpaceCode\Maia\PermissionRegistrar;
use SpaceCode\Maia\Traits\HasPermissions;
use Illuminate\Support\Arr;

class PermissionBooleanGroup extends BooleanGroup
{
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct(
            $name,
            $attribute,
            $resolveCallback ?? static function (Collection $permissions) {
                return $permissions->mapWithKeys(function (PermissionModel $permission) {
                    return [$permission->name => true];
                });
            }
        );
        $permissionClass = app(PermissionRegistrar::class)->getPermissionClass();
        $options = $permissionClass::get()->sortByDesc('name')->pluck('name', 'name')->toArray();
        if(!isBlog()) {
            $options = Arr::except($options, ['viewAny posts', 'view posts', 'create posts', 'update posts', 'attachAnyPostCategory posts', 'detachAnyPostCategory posts', 'attachAnyPostTag posts', 'detachAnyPostTag posts', 'delete posts', 'restore posts', 'forceDelete posts', 'viewAny postTags', 'view postTags', 'create postTags', 'update postTags', 'delete postTags', 'restore postTags', 'forceDelete postTags', 'viewAny postCategories', 'view postCategories', 'create postCategories', 'update postCategories', 'delete postCategories', 'restore postCategories', 'forceDelete postCategories']);
        }
        if(!isPortfolio()) {
            $options = Arr::except($options, ['viewAny portfolio', 'view portfolio', 'create portfolio', 'update portfolio', 'attachAnyPortfolioCategory portfolio', 'detachAnyPortfolioCategory portfolio', 'attachAnyPortfolioTag portfolio', 'detachAnyPortfolioTag portfolio', 'delete portfolio', 'restore portfolio', 'forceDelete portfolio', 'viewAny portfolioTags', 'view portfolioTags', 'create portfolioTags', 'update portfolioTags', 'delete portfolioTags', 'restore portfolioTags', 'forceDelete portfolioTags', 'viewAny portfolioCategories', 'view portfolioCategories', 'create portfolioCategories', 'update portfolioCategories', 'delete portfolioCategories', 'restore portfolioCategories', 'forceDelete portfolioCategories']);
        }
        $this->options($options);
    }

    /**
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param HasPermissions $model
     * @param string $attribute
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (!$request->exists($requestAttribute)) {
            return;
        }
        $values = collect(json_decode($request[$requestAttribute], true))
            ->filter(static function (bool $value) {
                return $value;
            })
            ->keys()
            ->toArray();
        $model->syncPermissions($values);
    }
}
