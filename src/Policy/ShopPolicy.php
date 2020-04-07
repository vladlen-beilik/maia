<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use Illuminate\Auth\Access\HandlesAuthorization;

class ShopPolicy
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
        return $this->checkAssignment($user, 'viewAny shops');
    }

    /**
     * @param $user
     * @return bool
     */
    public function view($user)
    {
        return $this->checkAssignment($user, 'view shops');
    }

    /**
     * @param $user
     * @return bool
     */
    public function create($user)
    {
        return $this->checkAssignment($user, 'create shops');
    }

    /**
     * @param $user
     * @return bool
     */
    public function update($user)
    {
        return $this->checkAssignment($user, 'update shops');
    }

    /**
     * @param $user
     * @return bool
     */
    public function delete($user)
    {
        return $this->checkAssignment($user, 'delete shops');
    }

    /**
     * @param $user
     * @return bool
     */
    public function restore($user)
    {
        return $this->checkAssignment($user, 'restore shops');
    }

    /**
     * @param $user
     * @return bool
     */
    public function forceDelete($user)
    {
        return $this->checkAssignment($user, 'forceDelete shops');
    }
}
