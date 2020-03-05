<?php

namespace SpaceCode\Maia\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role
{
    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany;

    /**
     * @param string $name
     * @param $guardName
     * @return static
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * @param int $id
     * @param $guardName
     * @return static
     */
    public static function findById(int $id, $guardName): self;

    /**
     * @param string $name
     * @param $guardName
     * @return static
     */
    public static function findOrCreate(string $name, $guardName): self;

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission): bool;
}
