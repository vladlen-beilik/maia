<?php
namespace SpaceCode\Maia\Models;

use SpaceCode\Maia\Guard;
use Illuminate\Database\Eloquent\Model;
use SpaceCode\Maia\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use SpaceCode\Maia\Exceptions\GuardDoesNotMatch;
use SpaceCode\Maia\Exceptions\RoleDoesNotExist;
use SpaceCode\Maia\Traits\RefreshesPermissionCache;
use SpaceCode\Maia\Contracts\Role as RoleContract;
use SpaceCode\Maia\Contracts\Permission as PermissionContract;

class Role extends Model implements RoleContract
{
    use HasPermissions, RefreshesPermissionCache;

    protected $guarded = ['id'];

    /**
     * Role constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->setTable('roles');
    }

    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            \SpaceCode\Maia\Models\Permission::class,
            'role_has_permissions',
            'role_id',
            'permission_id'
        );
    }

    /**
     * @return MorphToMany
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        );
    }

    /**
     * @param string $name
     * @param string|null $guardName
     * @return RoleContract|RoleContract
     * @throws RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::where('name', $name)->where('guard_name', $guardName)->first();
        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }
        return $role;
    }

    /**
     * @param int $id
     * @param string|null $guardName
     * @return RoleContract|RoleContract
     * @throws RoleDoesNotExist
     */
    public static function findById(int $id, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::where('id', $id)->where('guard_name', $guardName)->first();
        if (! $role) {
            throw RoleDoesNotExist::withId($id);
        }
        return $role;
    }

    /**
     * @param string $name
     * @param string|null $guardName
     * @return RoleContract
     */
    public static function findOrCreate(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $role = static::where('name', $name)->where('guard_name', $guardName)->first();
        if (! $role) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
        }
        return $role;
    }

    /**
     * @param PermissionContract|string $permission
     * @return bool
     */
    public function hasPermissionTo($permission): bool
    {
        $permissionClass = $this->getPermissionClass();
        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }
        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }
        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }
        return $this->permissions->contains('id', $permission->id);
    }
}
