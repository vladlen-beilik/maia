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
    public function attachAnyPortfolioCategory(User $user)
    {
        return $this->checkAssignment($user, 'attachAnyPortfolioCategory portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function detachAnyPortfolioCategory(User $user)
    {
        return $this->checkAssignment($user, 'detachAnyPortfolioCategory portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function attachAnyPortfolioTag(User $user)
    {
        return $this->checkAssignment($user, 'attachAnyPortfolioTag portfolio');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function detachAnyPortfolioTag(User $user)
    {
        return $this->checkAssignment($user, 'detachAnyPortfolioTag portfolio');
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
