<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostCategoryPolicy
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
        return $this->checkAssignment($user, 'viewAny postCategories');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $this->checkAssignment($user, 'view postCategories');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $this->checkAssignment($user, 'create postCategories');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $this->checkAssignment($user, 'update postCategories');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $this->checkAssignment($user, 'delete postCategories');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function restore(User $user)
    {
        return $this->checkAssignment($user, 'restore postCategories');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function forceDelete(User $user)
    {
        return $this->checkAssignment($user, 'forceDelete postCategories');
    }
}
