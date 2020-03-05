<?php

namespace SpaceCode\Maia\Commands;

use Illuminate\Console\Command;
use SpaceCode\Maia\Contracts\Role as RoleContract;
use SpaceCode\Maia\Contracts\Permission as PermissionContract;

class CreateRole extends Command
{
    protected $signature = 'maia:create-role
        {name : The name of the role}
        {guard? : The name of the guard}
        {permissions? : A list of permissions to assign to the role, separated by | }';

    protected $description = 'Create a role';

    public function handle()
    {
        $roleClass = app(RoleContract::class);
        $role = $roleClass::findOrCreate($this->argument('name'), $this->argument('guard'));
        $role->givePermissionTo($this->makePermissions($this->argument('permissions')));
        $this->info(trans('maia::commands.role.created', ['name' => $role->name]));
    }

    protected function makePermissions($string = null)
    {
        if (empty($string)) {
            return;
        }
        $permissionClass = app(PermissionContract::class);
        $permissions = explode('|', $string);
        $models = [];
        foreach ($permissions as $permission) {
            $models[] = $permissionClass::findOrCreate(trim($permission), $this->argument('guard'));
        }
        return collect($models);
    }
}
