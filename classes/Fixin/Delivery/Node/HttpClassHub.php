<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoHandlerInterface;
use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Delivery\Node\Exception;
use Fixin\Support\Http;
use Fixin\Support\Strings;

class HttpClassHub extends HttpHub
{
    protected const
        CLASS_NAME_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_\\\\]*$/',
        INVALID_CLASS_EXCEPTION = "Class '%s' is invalid, CargoHandlerInterface required",
        THIS_SETS = [
            self::DEPTH => self::INT_TYPE
        ];

    public const
        BASE_PATH = 'basePath',
        CLASS_PREFIX = 'classPrefix',
        DEPTH = 'depth';

    /**
     * @var string
     */
    protected $basePath = '/';

    /**
     * @var string
     */
    protected $classPrefix = '';

    /**
     * @var int
     */
    protected $depth = 2;

    /**
     * @throws Exception\RuntimeException
     */
    protected function getHandlerForPath(string $name): ?CargoHandlerInterface
    {
        if (preg_match(static::CLASS_NAME_PATTERN, $name)) {
            $fullName = $this->classPrefix . Strings::toClassName($name);

            // Test class
            if ($this->resourceManager->has($fullName)) {
                $instance = $this->resourceManager->get($fullName);

                if ($instance instanceof CargoHandlerInterface) {
                    return $instance;
                }

                throw new Exception\RuntimeException(sprintf(static::INVALID_CLASS_EXCEPTION, get_class($instance)));
            }
        }

        return null;
    }

    protected function handleHttpCargo(HttpCargoInterface $cargo): CargoInterface
    {
        $path = $cargo->getUri()->getPath();
        $length = strlen($this->basePath);

        if (strncmp($path, $this->basePath, $length) === 0) {
            $path = substr($path, $length);
            if (strlen($path)) {
                return $this->handlePath($cargo, $path);
            }

            return $cargo->setStatusCode(Http::STATUS_NOT_FOUND_404);
        }

        return $cargo;
    }

    /**
     * Handle observed path
     */
    protected function handlePath(HttpCargoInterface $cargo, string $path): HttpCargoInterface
    {
        $depth = $this->depth;
        $tags = explode('/', rtrim($path, '/'), $depth + 2);

        if (!isset($tags[$depth + 1])) {
            // Action
            if (isset($tags[$depth])) {
                $cargo->getParameters()->set('action', $tags[$depth]);
            }

            // Controller
            if ($controller = $this->getHandlerForPath(implode('\\', array_slice($tags, 0, $depth)))) {
                return $controller->handle($cargo);
            }
        }

        return $cargo->setStatusCode(Http::STATUS_NOT_FOUND_404);
    }

    protected function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '/') . '/';
    }

    protected function setClassPrefix(string $classPrefix): void
    {
        $this->classPrefix = trim($classPrefix, '\\') . '\\';
    }
}
