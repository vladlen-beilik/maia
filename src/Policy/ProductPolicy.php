<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
        return $this->checkAssignment($user, 'viewAny products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function view($user)
    {
        return $this->checkAssignment($user, 'view products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function create($user)
    {
        return $this->checkAssignment($user, 'create products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function update($user)
    {
        return $this->checkAssignment($user, 'update products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function attachAnyProductCategory($user)
    {
        return $this->checkAssignment($user, 'attachAnyProductCategory products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function detachAnyProductCategory($user)
    {
        return $this->checkAssignment($user, 'detachAnyProductCategory products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function attachAnyProductTag($user)
    {
        return $this->checkAssignment($user, 'attachAnyProductTag products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function detachAnyProductTag($user)
    {
        return $this->checkAssignment($user, 'detachAnyProductTag products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function delete($user)
    {
        return $this->checkAssignment($user, 'delete products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function restore($user)
    {
        return $this->checkAssignment($user, 'restore products');
    }

    /**
     * @param $user
     * @return bool
     */
    public function forceDelete($user)
    {
        return $this->checkAssignment($user, 'forceDelete products');
    }
}
