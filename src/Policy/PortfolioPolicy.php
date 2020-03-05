<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PortfolioPolicy
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
        return $this->checkAssignment($user, 'viewAny portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $this->checkAssignment($user, 'view portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $this->checkAssignment($user, 'create portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $this->checkAssignment($user, 'update portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function attachCategory(User $user)
    {
        return $this->checkAssignment($user, 'attachCategory portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function detachCategory(User $user)
    {
        return $this->checkAssignment($user, 'detachCategory portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function attachTag(User $user)
    {
        return $this->checkAssignment($user, 'attachTag portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function detachTag(User $user)
    {
        return $this->checkAssignment($user, 'detachTag portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $this->checkAssignment($user, 'delete portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function restore(User $user)
    {
        return $this->checkAssignment($user, 'restore portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user)
    {
        return $this->checkAssignment($user, 'forceDelete portfolio');
    }
}
