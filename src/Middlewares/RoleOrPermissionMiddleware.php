<?php

namespace SpaceCode\Maia\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use SpaceCode\Maia\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission)
    {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);
        if (! Auth::user()->hasAnyRole($rolesOrPermissions) && ! Auth::user()->hasAnyPermission($rolesOrPermissions)) {
            throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
        }
        return $next($request);
    }
}
