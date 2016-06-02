<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Node;

use Fixin\Delivery\Cargo\CargoInterface;

class JsonToArray extends Node {

    const JSON_TYPES = ['application/json', 'application/jsonml+json'];

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        if (in_array($cargo->getContentType(), static::JSON_TYPES)) {
            $cargo->setContent($this->container->get('Base\Json\Json')->decode($cargo->getContent()));
        }

        return $cargo;
    }
}