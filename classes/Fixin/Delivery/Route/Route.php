<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Delivery\Route;

use Fixin\Delivery\Cargo\CargoInterface;
use Fixin\Delivery\Node\NodeInterface;
use Fixin\Resource\Resource;

class Route extends Resource implements RouteInterface {

    const EXCEPTION_INVALID_NODE = "Invalid node resource '%s'";

    /**
     * @var NodeInterface[]
     */
    protected $loadedNodes = [];

    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * Get node instance for index
     *
     * @param int $index
     * @throws InvalidArgumentException
     * @return NodeInterface
     */
    protected function getNode(int $index): NodeInterface {
        if (isset($this->loadedNodes[$index])) {
            return $this->loadedNodes[$index];
        }

        $node = $this->nodes[$index];

        if (is_string($node)) {
            $node = $this->container->get($node);
        }

        if ($node instanceof NodeInterface) {
            $this->loadedNodes[$index] = $node;

            return $node;
        }

        throw new InvalidArgumentException(sprintf(static::EXCEPTION_INVALID_NODE, $index));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Delivery\Cargo\CargoHandlerInterface::handle($cargo)
     */
    public function handle(CargoInterface $cargo): CargoInterface {
        $cargo->setDelivered(false);

        $index = 0;
        $length = count($this->nodes);

        while ($index < $length) {
            $cargo = $this->getNode($index)->handle($cargo);

            if ($cargo->isDelivered()) {
                break;
            }

            $index++;
        }

        return $cargo;
    }

    /**
     * Set nodes
     *
     * @param array $nodes
     */
    protected function setNodes(array $nodes) {
        $this->nodes = array_values($nodes);
    }
}