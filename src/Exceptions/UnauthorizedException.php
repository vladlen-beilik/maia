<?php

namespace SpaceCode\Maia\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    private $requiredRoles = [];

    private $requiredPermissions = [];

    public static function forRoles(array $roles): self
    {
        $message = _trans('maia::exeptions.unauthorized.forRoles.message');
        if (config('maia.permission.display_permission_in_exception')) {
            $permStr = implode(', ', $roles);
            $message = _trans('maia::exeptions.unauthorized.forRoles.message_value', ['str' => $permStr]);
        }
        $exception = new static(403, $message, null, []);
        $exception->requiredRoles = $roles;
        return $exception;
    }

    public static function forPermissions(array $permissions): self
    {
        $message = _trans('maia::exeptions.unauthorized.forPermissions.message');
        if (config('maia.permission.display_permission_in_exception')) {
            $permStr = implode(', ', $permissions);
            $message = _trans('maia::exeptions.unauthorized.forPermissions.message_value', ['str' => $permStr]);
        }
        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $permissions;
        return $exception;
    }

    public static function forRolesOrPermissions(array $rolesOrPermissions): self
    {
        $message = _trans('maia::exeptions.unauthorized.forRolesOrPermissions.message');
        if (config('maia.permission.display_permission_in_exception') && config('maia.permission.display_role_in_exception')) {
            $permStr = implode(', ', $rolesOrPermissions);
            $message = _trans('maia::exeptions.unauthorized.forRolesOrPermissions.message_value', ['str' => $permStr]);
        }
        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $rolesOrPermissions;
        return $exception;
    }

    public static function notLoggedIn(): self
    {
        return new static(403, _trans('maia::exeptions.unauthorized.notLoggedIn'), null, []);
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}
