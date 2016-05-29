<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Uri\Factory;

use Fixin\Resource\Factory\Factory;

class EnvironmentUriFactory extends Factory {

    const EXCEPTION_CAN_T_DETERMINE = 'Can\'t determine the request URI';

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(array $options = NULL, string $name = null) {
        $uri = $this->container->clonePrototype('Base\Uri\Uri');

        $uri->setScheme(($https = $_SERVER['HTTPS'] ?? false) && $https !== 'off' ? 'https' : 'http')
        ->setHost($_SERVER['HTTP_HOST'])
        ->setPort($_SERVER['SERVER_PORT'])
        ->setPath($this->getPath())
        ->setQuery($_SERVER['QUERY_STRING']);

        return $uri;
    }

    /**
     * Get path
     *
     * @throws InvalidConfigException
     * @return string
     */
    protected function getPath(): string {
        $uri = $_SERVER['HTTP_X_REWRITE_URL'] ?? $_SERVER['REQUEST_URI'] ?? $_SERVER['ORIG_PATH_INFO'] ?? (function() {
            throw new InvalidConfigException(static::EXCEPTION_CAN_T_DETERMINE);
        })();

        if (null === $index = strpos($uri, '?')) {
            return $uri;
        }

        return substr($uri, 0, $index);
    }
}