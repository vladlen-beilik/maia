<?php

declare(strict_types=1);

namespace SpaceCode\Maia\Policy;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    public function checkAssignment($user, $perm)
    {
        if($user->roles->count() > 0) {
            foreach ($user->roles as $role) {
                if($role->permissions->contains('name', $perm)) {
                    return true;
                }
            }
        }
        if($user->permissions->count() > 0) {
            if($user->permissions->contains('name', $perm)) {
                return true;
            }
        }
        return false;
    }

    public function viewAny(User $user): bool
    {
        return $this->checkAssignment($user, 'viewAny pages');
    }
    public function view(User $user): bool
    {
        return $this->checkAssignment($user, 'view pages');
    }
    public function create(User $user): bool
    {
        return $this->checkAssignment($user, 'create pages');
    }
    public function update(User $user): bool
    {
        return $this->checkAssignment($user, 'update pages');
    }
    public function delete(User $user): bool
    {
        return $this->checkAssignment($user, 'delete pages');
    }
    public function restore(): bool
    {
        return $this->checkAssignment($user, 'restore pages');
    }
    public function forceDelete(): bool
    {
        return $this->checkAssignment($user, 'forceDelete pages');
    }
}
