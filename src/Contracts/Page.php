<?php

namespace SpaceCode\Maia\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Page
{
    /**
     * Find page by its title and guard name.
     *
     * @param string $title
     * @param string|null $guardName
     *
     * @return \SpaceCode\Maia\Contracts\Page
     *
     * @throws \SpaceCode\Maia\Exceptions\PageDoesNotExist
     */
    public static function findByTitle(string $title, $guardName): self;
    /**
     * Find page by its slug and guard name.
     *
     * @param string $slug
     * @param string|null $guardName
     *
     * @return \SpaceCode\Maia\Contracts\Page
     *
     * @throws \SpaceCode\Maia\Exceptions\PageDoesNotExist
     */
    public static function findBySlug(string $slug, $guardName): self;

    /**
     * Find page by its id and guard name.
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @return \SpaceCode\Maia\Contracts\Page
     *
     * @throws \SpaceCode\Maia\Exceptions\PageDoesNotExist
     */
    public static function findById(int $id, $guardName): self;

    /**
     * Find or create page by its slug and guard name.
     *
     * @param string $slug
     * @param string|null $guardName
     *
     * @return \SpaceCode\Maia\Contracts\Page
     */
    public static function findOrCreate(string $slug, $guardName): self;
}
