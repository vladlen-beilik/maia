<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function checkAssignment($user, $perm)
    {
        if ($user->roles->count() > 0) {
            foreach ($user->roles as $role) {
                if($role->name === 'developer') {
                    return true;
                }
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
     * @return bool
     */
    public function update(User $user)
    {
        return $this->checkAssignment($user, 'update permissions');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $this->checkAssignment($user, 'delete permissions');
    }
}