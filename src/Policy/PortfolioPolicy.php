<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

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
     * @param $user
     * @return bool
     */
    public function viewAny($user)
    {
        return $this->checkAssignment($user, 'viewAny portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function view($user)
    {
        return $this->checkAssignment($user, 'view portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function create($user)
    {
        return $this->checkAssignment($user, 'create portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function update($user)
    {
        return $this->checkAssignment($user, 'update portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function attachAnyPortfolioCategory($user)
    {
        return $this->checkAssignment($user, 'attachAnyPortfolioCategory portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function detachAnyPortfolioCategory($user)
    {
        return $this->checkAssignment($user, 'detachAnyPortfolioCategory portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function attachAnyPortfolioTag($user)
    {
        return $this->checkAssignment($user, 'attachAnyPortfolioTag portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function detachAnyPortfolioTag($user)
    {
        return $this->checkAssignment($user, 'detachAnyPortfolioTag portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function delete($user)
    {
        return $this->checkAssignment($user, 'delete portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function restore($user)
    {
        return $this->checkAssignment($user, 'restore portfolio');
    }

    /**
     * @param $user
     * @return bool
     */
    public function forceDelete($user)
    {
        return $this->checkAssignment($user, 'forceDelete portfolio');
    }
}
