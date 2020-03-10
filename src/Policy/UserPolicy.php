<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $this->checkAssignment($user, 'viewAny users');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $this->checkAssignment($user, 'view users');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $this->checkAssignment($user, 'create users');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        if(isDeveloper($user)) {
            return true;
        }
        if ($user->name === 'developer') {
            return false;
        }
        return $this->checkAssignment($user, 'update users');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        if(isDeveloper($user)) {
            return true;
        }
        if ($user->name === 'developer') {
            return false;
        }
        return $this->checkAssignment($user, 'delete users');
    }
}
