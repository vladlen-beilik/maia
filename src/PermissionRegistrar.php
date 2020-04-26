<?php

namespace SpaceCode\Maia;

use DateInterval;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Collection;
use SpaceCode\Maia\Contracts\Role;
use Illuminate\Contracts\Auth\Access\Gate;
use SpaceCode\Maia\Contracts\Permission;
use Illuminate\Contracts\Auth\Access\Authorizable;
use function array_key_exists;

class PermissionRegistrar
{
    /** @var Repository */
    protected $cache;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var string */
    protected $permissionClass;

    /** @var string */
    protected $roleClass;

    /** @var Collection */
    protected $permissions;

    /** @var DateInterval|int */
    public static $cacheExpirationTime;

    /** @var string */
    public static $cacheKey;

    /** @var string */
    public static $cacheModelKey;

    /**
     * PermissionRegistrar constructor.
     *
     * @param CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->permissionClass = Models\Permission::class;
        $this->roleClass = Models\Role::class;
        $this->cacheManager = $cacheManager;
        $this->initializeCache();
    }

    protected function initializeCache()
    {
        self::$cacheExpirationTime = config('maia.permission.cache.expiration_time', config('maia.permission.cache_expiration_time'));
        self::$cacheKey = config('maia.permission.cache.key');
        self::$cacheModelKey = config('maia.permission.cache.model_key');
        $this->cache = $this->getCacheStoreFromConfig();
    }

    protected function getCacheStoreFromConfig(): Repository
    {
        $cacheDriver = config('maia.permission.cache.store', 'default');
        if ($cacheDriver === 'default') {
            return $this->cacheManager->store();
        }
        if (! array_key_exists($cacheDriver, config('cache.stores'))) {
            $cacheDriver = 'array';
        }
        return $this->cacheManager->store($cacheDriver);
    }

    /**
     * Register the permission check method on the gate.
     * We resolve the Gate fresh here, for benefit of long-running instances.
     *
     * @return bool
     */
    public function registerPermissions(): bool
    {
        app(Gate::class)->before(function (Authorizable $user, string $ability) {
            if (method_exists($user, 'checkPermissionTo')) {
                return $user->checkPermissionTo($ability) ?: null;
            }
        });
        return true;
    }

    /**
     * Flush the cache.
     */
    public function forgetCachedPermissions()
    {
        $this->permissions = null;
        return $this->cache->forget(self::$cacheKey);
    }

    /**
     * Get the permissions based on the passed params.
     *
     * @param array $params
     *
     * @return Collection
     */
    public function getPermissions(array $params = []): Collection
    {
        if ($this->permissions === null) {
            $this->permissions = $this->cache->remember(self::$cacheKey, self::$cacheExpirationTime, function () {
                return $this->getPermissionClass()
                    ->with('roles')
                    ->get();
            });
        }
        $permissions = clone $this->permissions;
        foreach ($params as $attr => $value) {
            $permissions = $permissions->where($attr, $value);
        }
        return $permissions;
    }

    /**
     * Get an instance of the permission class.
     *
     * @return Permission
     */
    public function getPermissionClass(): Permission
    {
        return app($this->permissionClass);
    }

    public function setPermissionClass($permissionClass)
    {
        $this->permissionClass = $permissionClass;
        return $this;
    }

    /**
     * Get an instance of the role class.
     *
     * @return Role
     */
    public function getRoleClass(): Role
    {
        return app($this->roleClass);
    }

    /**
     * Get the instance of the Cache Store.
     *
     * @return Store
     */
    public function getCacheStore(): Store
    {
        return $this->cache->getStore();
    }
}
