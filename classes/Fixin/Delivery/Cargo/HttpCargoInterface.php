<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Cargo;

use Fixin\Base\Container\VariableContainerInterface;
use Fixin\Base\Uri\UriInterface;

interface HttpCargoInterface extends CargoInterface {

    /**
     * Add header value
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function addHeader(string $name, string $value): HttpCargoInterface;

    /**
     * Clear all headers
     *
     * @return self
     */
    public function clearHeaders(): HttpCargoInterface;

    /**
     * Get cookie value
     *
     * @param string $name
     * @return string|null
     */
    public function getCookie(string $name);

    /**
     * Get environment parameters
     *
     * @return VariableContainerInterface
     */
    public function getEnvironmentParameters(): VariableContainerInterface;

    /**
     * Get header values
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Get protocol version
     *
     * @return string
     */
    public function getProtocolVersion(): string;

    /**
     * Get request header value
     *
     * @param string $name
     * @return string|null
     */
    public function getRequestHeader(string $name);

    /**
     * Get request method
     *
     * @return string
     */
    public function getRequestMethod(): string;

    /**
     * Get request parameters
     *
     * @return VariableContainerInterface
     */
    public function getRequestParameters(): VariableContainerInterface;

    /**
     * Get request protocol version
     *
     * @return string
     */
    public function getRequestProtocolVersion(): string;

    /**
     * Get request URI
     *
     * @return UriInterface
     */
    public function getRequestUri();

    /**
     * Get server parameters
     *
     * @return VariableContainerInterface
     */
    public function getServerParameters(): VariableContainerInterface;

    /**
     * Get status code
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * Set cookies
     *
     * @param array $cookies
     * @return self
     */
    public function setCookies(array $cookies): HttpCargoInterface;

    /**
     * Set header value
     *
     * @param string $name
     * @param string|array $value
     * @return self
     */
    public function setHeader(string $name, $value): HttpCargoInterface;

    /**
     * Set protocol version
     *
     * @param string $protocolVersion
     * @return self
     */
    public function setProtocolVersion(string $protocolVersion): HttpCargoInterface;

    /**
     * Set request header values
     *
     * @param array $headers
     * @return self
     */
    public function setRequestHeaders(array $headers): HttpCargoInterface;

    /**
     * Set request method
     *
     * @param string $method
     * @return self
     */
    public function setRequestMethod(string $method): HttpCargoInterface;

    /**
     * Set request protocol version
     *
     * @param string $protocolVersion
     * @return self
     */
    public function setRequestProtocolVersion(string $protocolVersion): HttpCargoInterface;

    /**
     * Set request URI
     *
     * @param UriInterface $requestUri
     * @return self
     */
    public function setRequestUri(UriInterface $requestUri): HttpCargoInterface;

    /**
     * Set status code
     *
     * @param int $statusCode
     * @return self
     */
    public function setStatusCode(int $statusCode): HttpCargoInterface;
}