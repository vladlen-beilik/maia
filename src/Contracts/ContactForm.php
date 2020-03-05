<?php

namespace SpaceCode\Maia\Contracts;

interface ContactForm
{
    /**
     * @param string $title
     * @return static
     */
    public static function findByTitle(string $title): self;

    /**
     * @param int $id
     * @return static
     */
    public static function findById(int $id): self;

    /**
     * @param int $id
     * @return static
     */
    public static function findOrCreate(int $id): self;
}
