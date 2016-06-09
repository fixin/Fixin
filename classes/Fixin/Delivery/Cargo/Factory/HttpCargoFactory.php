<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo\Factory;

use Fixin\Delivery\Cargo\HttpCargoInterface;
use Fixin\Resource\Factory\Factory;
use Fixin\Support\Http;

class HttpCargoFactory extends Factory {

    /**
     * {@inheritDoc}
     * @see \Fixin\Resource\Factory\FactoryInterface::__invoke()
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __invoke(array $options = NULL, string $name = NULL) {
        $variables = $this->container->clonePrototype('Base\Container\VariableContainer');

        /** @var HttpCargoInterface $cargo */
        $cargo = $this->container->clonePrototype('Delivery\Cargo\HttpCargo', [
            'environmentParameters' => $variables,
            'requestParameters' => clone $variables,
            'serverParameters' => clone $variables
        ]);

        // Setup data
        $this->setupRequest($cargo);
        $this->setupParameters($cargo);

        $cargo->setCookies($_COOKIE);

        // POST
        if ($cargo->getRequestMethod() === Http::METHOD_POST) {
            $this->setupPost($cargo);
        }

        return $cargo;
    }

    /**
     * Get header values
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getHeaders(): array {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (!strncmp($name, 'HTTP_', 5)) {
                $headers[strtr(ucwords(strtolower(substr($name, 5)), '_'), '_', '-')] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get method
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get POST parameters
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getPostParameters(): array {
        $post = $_POST;

        // Files
        if ($_FILES) {
            foreach ($_FILES as $key => $file) {
                $post[$key] = $file;
            }
        }

        return $post;
    }

    /**
     * Get protocol version
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getProtocolVersion(): string {
        return isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], Http::PROTOCOL_VERSION_1_0)
            ? Http::PROTOCOL_VERSION_1_0 : Http::PROTOCOL_VERSION_1_1;
    }

    /**
     * Setup parameter containers
     *
     * @param HttpCargoInterface $cargo
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function setupParameters(HttpCargoInterface $cargo) {
        $cargo->getRequestParameters()->setFrom($_GET);
        $cargo->getEnvironmentParameters()->setFrom($_ENV);
        $cargo->getServerParameters()->setFrom($_SERVER);
    }

    /**
     * Setup POST data
     *
     * @param HttpCargoInterface $cargo
     */
    protected function setupPost(HttpCargoInterface $cargo) {
        $cargo->setContent($this->getPostParameters());

        // Content type
        if ($contentType = $cargo->getRequestHeader(Http::HEADER_CONTENT_TYPE)) {
            $cargo->setContentType($contentType);
        }
    }

    /**
     * Setup request data
     *
     * @param HttpCargoInterface $cargo
     */
    protected function setupRequest(HttpCargoInterface $cargo) {
        $cargo
        ->setRequestProtocolVersion($this->getProtocolVersion())
        ->setRequestMethod($this->getMethod())
        ->setRequestUri($this->container->clonePrototype('Base\Uri\Factory\EnvironmentUriFactory'))
        ->setRequestHeaders($this->getHeaders());
    }
}