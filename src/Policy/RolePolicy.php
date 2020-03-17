<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use SpaceCode\Maia\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

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
     * @param $user
     * @return bool
     */
    public function viewAny($user)
    {
        return $this->checkAssignment($user, 'viewAny roles');
    }

    /**
     * @param $user
     * @return bool
     */
    public function view($user)
    {
        return $this->checkAssignment($user, 'view roles');
    }

    /**
     * @param $user
     * @return bool
     */
    public function create($user)
    {
        return $this->checkAssignment($user, 'create roles');
    }

    /**
     * @param $user
     * @param Role $role
     * @return bool
     */
    public function update($user, Role $role)
    {
        if(isDeveloper($user)) {
            return true;
        }
        if ($role->name === 'developer') {
            return false;
        }
        return $this->checkAssignment($user, 'update roles');
    }

    /**
     * @param $user
     * @param Role $role
     * @return bool
     */
    public function delete($user, Role $role)
    {
        if(isDeveloper($user)) {
            return true;
        }
        if ($role->name === 'developer') {
            return false;
        }
        return $this->checkAssignment($user, 'delete roles');
    }
}
