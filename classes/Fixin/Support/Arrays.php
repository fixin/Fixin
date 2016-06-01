<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Support;

class Arrays extends DoNotCreate {

    /**
     * Get array item
     *
     * @param array $array
     * @param string|int $key
     * @return mixed|null
     */
    public static function arrayForKey(array $array, $key) {
        $value = $array[$key] ?? null;

        return is_array($value) ? $value : null;
    }
}