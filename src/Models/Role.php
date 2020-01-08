<?php
namespace SpaceCode\Maia\Models;

use SpaceCode\Maia\Guard;
use Illuminate\Database\Eloquent\Model;
use SpaceCode\Maia\Traits\HasPermissions;
use SpaceCode\Maia\Exceptions\RoleDoesNotExist;
use SpaceCode\Maia\Exceptions\GuardDoesNotMatch;
use SpaceCode\Maia\Exceptions\RoleAlreadyExists;
use SpaceCode\Maia\Contracts\Role as RoleContract;
use SpaceCode\Maia\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model implements RoleContract
{
    use HasPermissions;
    use RefreshesPermissionCache;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->setTable(config('maia.permission.table_names.roles'));
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        if (static::where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }
        return static::query()->create($attributes);
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('maia.permission.models.permission'),
            config('maia.permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('maia.permission.table_names.model_has_roles'),
            'role_id',
            config('maia.permission.column_names.model_morph_key')
        );
    }

    /**
     * Find a role by its name and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \SpaceCode\Maia\Contracts\Role|\SpaceCode\Maia\Models\Role
     *
     * @throws \SpaceCode\Maia\Exceptions\RoleDoesNotExist
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
     * Find or create role by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \SpaceCode\Maia\Contracts\Role
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
     * Determine if the user may perform the given permission.
     *
     * @param string|Permission $permission
     *
     * @return bool
     *
     * @throws \SpaceCode\Maia\Exceptions\GuardDoesNotMatch
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
