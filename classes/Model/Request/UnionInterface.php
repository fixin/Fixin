<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request;

use Fixin\Resource\PrototypeInterface;

interface UnionInterface extends PrototypeInterface
{
    public const
        REQUEST = 'request',
        TYPE = 'type',
        TYPE_ALL = 'all',
        TYPE_NORMAL = 'normal';

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string;
}
