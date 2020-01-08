<?php

namespace SpaceCode\Maia;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Http\Requests\NovaRequest;
use SpaceCode\Maia\Models\Permission as PermissionModel;
use SpaceCode\Maia\PermissionRegistrar;
use SpaceCode\Maia\Traits\HasPermissions;

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
        $options = $permissionClass::get()->pluck('name', 'name')->toArray();
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
        if (! $request->exists($requestAttribute)) {
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