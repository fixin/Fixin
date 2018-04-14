<?php
/**
 * /Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Base\Dictionary;

use Fixin\Resource\ResourceInterface;

interface DictionaryInterface extends ResourceInterface
{
    /**
     * Delete all items
     *
     * @return $this
     */
    public function clear(): DictionaryInterface;

    /**
     * Decrement value of a numeric item
     *
     * @param string $key
     * @param int $step
     * @return int
     */
    public function decrement(string $key, int $step = 1): int;

    /**
     * Delete an item
     *
     * @param string $key
     * @return $this
     */
    public function delete(string $key): DictionaryInterface;

    /**
     * Delete multiple items
     *
     * @param array $keys
     * @return $this
     */
    public function deleteMultiple(array $keys): DictionaryInterface;

    /**
     * Retrieve an item
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Retrieve multiple items
     *
     * @param array $keys
     * @return array
     */
    public function getMultiple(array $keys): array;

    /**
     * Increment value of a numeric item
     *
     * @param string $key
     * @param int $step
     * @return int
     */
    public function increment(string $key, int $step = 1): int;

    /**
     * Store an item
     *
     * @param string $key
     * @param $value
     * @param int|null $expireTime
     * @return $this
     */
    public function set(string $key, $value, ?int $expireTime = null): DictionaryInterface;

    /**
     * Set expire time of an item
     *
     * @param string $key
     * @param int|null $expireTime
     * @return $this
     */
    public function setExpireTime(string $key, ?int $expireTime = null): DictionaryInterface;

    /**
     * Store multiple items
     *
     * @param array $items
     * @param int|null $expireTime
     * @return $this
     */
    public function setMultiple(array $items, ?int $expireTime = null): DictionaryInterface;
}
