<?php

use Illuminate\Database\Seeder;
use SpaceCode\Maia\Models\Permission;
use SpaceCode\Maia\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    public function run() {

        /**
         * Roles
         */
        foreach (['admin', 'developer'] as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
        $admin = Role::where('name', 'admin')->firstOrFail();

        /**
         * Assignment
         */
        $permissions = [
            'viewAny roles', 'view roles', 'create roles', 'update roles', 'delete roles',
            'viewAny permissions', 'view permissions', 'create permissions', 'update permissions', 'delete permissions',
            'viewAny users', 'view users', 'create users', 'update users', 'delete users',
            'viewAny pages', 'view pages', 'create pages', 'update pages', 'delete pages', 'restore pages', 'forceDelete pages'
        ];
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        foreach ($permissions as $key => $value) {
            if($value === 'create permissions' || $value === 'update permissions' || $value === 'delete permissions') {
                unset($permissions[$key]);
            }
        }
        $admin->syncPermissions($permissions);
    }
}
