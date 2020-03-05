<?php

namespace SpaceCode\Maia\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany;

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
}
