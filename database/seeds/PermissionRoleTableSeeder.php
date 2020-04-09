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
        $developer = Role::where('name', 'developer')->firstOrFail();

        /**
         * Assignment
         */
        $permissions = [
            'viewNova',
            'viewHorizon',
            'viewAny pages', 'view pages', 'create pages', 'update pages', 'delete pages', 'restore pages', 'forceDelete pages',
            'viewAny roles', 'view roles', 'create roles', 'update roles', 'delete roles',
            'viewAny permissions', 'view permissions', 'create permissions', 'update permissions', 'delete permissions',
            'viewAny users', 'view users', 'create users', 'update users', 'delete users',
            'viewAny comments', 'view comments', 'update comments', 'delete comments', 'restore comments', 'forceDelete comments',
            'viewAny posts', 'view posts', 'create posts', 'update posts', 'attachAnyPostCategory posts', 'detachAnyPostCategory posts', 'attachAnyPostTag posts', 'detachAnyPostTag posts', 'delete posts', 'restore posts', 'forceDelete posts',
            'viewAny postTags', 'view postTags', 'create postTags', 'update postTags', 'delete postTags', 'restore postTags', 'forceDelete postTags',
            'viewAny postCategories', 'view postCategories', 'create postCategories', 'update postCategories', 'delete postCategories', 'restore postCategories', 'forceDelete postCategories',
            'viewAny portfolio', 'view portfolio', 'create portfolio', 'update portfolio', 'attachAnyPortfolioCategory portfolio', 'detachAnyPortfolioCategory portfolio', 'attachAnyPortfolioTag portfolio', 'detachAnyPortfolioTag portfolio', 'delete portfolio', 'restore portfolio', 'forceDelete portfolio',
            'viewAny portfolioTags', 'view portfolioTags', 'create portfolioTags', 'update portfolioTags', 'delete portfolioTags', 'restore portfolioTags', 'forceDelete portfolioTags',
            'viewAny portfolioCategories', 'view portfolioCategories', 'create portfolioCategories', 'update portfolioCategories', 'delete portfolioCategories', 'restore portfolioCategories', 'forceDelete portfolioCategories',
            'viewAny shops', 'view shops', 'create shops', 'update shops', 'delete shops', 'restore shops', 'forceDelete shops',
            'viewAny products', 'view products', 'create products', 'update products', 'attachAnyPostCategory products', 'detachAnyPostCategory products', 'attachAnyPostTag products', 'detachAnyPostTag products', 'attachAnyPostBrand products', 'detachAnyPostBrand products', 'delete products', 'restore products', 'forceDelete products',
            'viewAny productTags', 'view productTags', 'create productTags', 'update productTags', 'delete productTags', 'restore productTags', 'forceDelete productTags',
            'viewAny productCategories', 'view productCategories', 'create productCategories', 'update productCategories', 'delete productCategories', 'restore productCategories', 'forceDelete productCategories',
            'viewAny productBrands', 'view productBrands', 'create productBrands', 'update productBrands', 'delete productBrands', 'restore productBrands', 'forceDelete productBrands',
            'viewAny contactForms', 'view contactForms', 'delete contactForms', 'restore contactForms', 'forceDelete contactForms',
        ];
        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
        $admin->syncPermissions($permissions);
        $developer->syncPermissions(['viewNova', 'viewHorizon']);
    }
}
