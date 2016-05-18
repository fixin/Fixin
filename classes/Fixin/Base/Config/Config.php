<?php

namespace Fixin\Base\Config;
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

use Fixin\Base\Exception\InvalidKeyException;
use Fixin\Support\ContainerInterface;
use Fixin\Support\ToStringTrait;

class Config implements ContainerInterface {

    use ToStringTrait;

    const EXCEPTION_NOT_DEFINED = "Value for '%s' is not defined";

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * Get value for name
     *
     * @param string $name
     * @throws InvalidKeyException
     * @return mixed
     */
    public function get(string $name) {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        throw new InvalidParameterException(sprintf(static::EXCEPTION_NOT_DEFINED, $name));
    }

    /**
     * Check if name has value
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool {
        return isset($this->config[$name]);
    }
}