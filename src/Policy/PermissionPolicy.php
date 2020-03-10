<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use SpaceCode\Maia\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    protected $globalPermissions = [
        'viewNova',
        'viewHorizon',
        'viewAny pages', 'view pages', 'create pages', 'update pages', 'delete pages', 'restore pages', 'forceDelete pages',
        'viewAny roles', 'view roles', 'create roles', 'update roles', 'delete roles',
        'viewAny permissions', 'view permissions', 'create permissions', 'update permissions', 'delete permissions',
        'viewAny users', 'view users', 'create users', 'update users', 'delete users',
        'viewAny posts', 'view posts', 'create posts', 'update posts', 'attachCategory posts', 'detachCategory posts', 'attachTag posts', 'detachTag posts', 'delete posts', 'restore posts', 'forceDelete posts',
        'viewAny postTags', 'view postTags', 'create postTags', 'update postTags', 'delete postTags', 'restore postTags', 'forceDelete postTags',
        'viewAny postCategories', 'view postCategories', 'create postCategories', 'update postCategories', 'delete postCategories', 'restore postCategories', 'forceDelete postCategories',
        'viewAny portfolio', 'view portfolio', 'create portfolio', 'update portfolio', 'attachCategory portfolio', 'detachCategory portfolio', 'attachTag portfolio', 'detachTag portfolio', 'delete portfolio', 'restore portfolio', 'forceDelete portfolio',
        'viewAny portfolioTags', 'view portfolioTags', 'create portfolioTags', 'update portfolioTags', 'delete portfolioTags', 'restore portfolioTags', 'forceDelete portfolioTags',
        'viewAny portfolioCategories', 'view portfolioCategories', 'create portfolioCategories', 'update portfolioCategories', 'delete portfolioCategories', 'restore portfolioCategories', 'forceDelete portfolioCategories',
        'viewAny contactForms', 'view contactForms', 'delete contactForms', 'restore contactForms', 'forceDelete contactForms',
    ];

    public function checkAssignment($user, $perm)
    {
        if(isDeveloper($user)) {
            return true;
        }
        if ($user->roles->count() > 0) {
            foreach ($user->roles as $role) {
                if ($role->permissions->contains('name', $perm)) {
                    return true;
                }
            }
        }
        if ($user->permissions->count() > 0) {
            if ($user->permissions->contains('name', $perm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $this->checkAssignment($user, 'viewAny permissions');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $this->checkAssignment($user, 'view permissions');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $this->checkAssignment($user, 'create permissions');
    }

    /**
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function update(User $user, Permission $permission)
    {
        if(isDeveloper($user)) {
            return true;
        }
        if (in_array($permission->name, $this->globalPermissions)) {
            return false;
        }
        return $this->checkAssignment($user, 'update permissions');
    }

    /**
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function delete(User $user, Permission $permission)
    {
        if(isDeveloper($user)) {
            return true;
        }
        if (in_array($permission->name, $this->globalPermissions)) {
            return false;
        }
        return $this->checkAssignment($user, 'delete permissions');
    }
}
