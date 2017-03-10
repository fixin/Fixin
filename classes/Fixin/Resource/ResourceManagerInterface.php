<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */
namespace Fixin\Resource;

interface ResourceManagerInterface
{
    /**
     * Clone prototype
     */
    public function clone(string $name, array $options = []);

    /**
     * Get instance
     *
     * @return object|null
     */
    public function get(string $name);

    /**
     * Check if the name has been accessible
     */
    public function has(string $name): bool;
}
