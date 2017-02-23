<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Resource;

interface PrototypeInterface extends ResourceInterface
{
    /**
     * Clone instance and change options
     */
    public function withOptions(array $options);
}
