<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntityIdInterface extends PrototypeInterface {

    const OPTION_ENTITY_ID = 'entityId';
    const OPTION_REPOSITORY = 'repository';

    /**
     * Delete entity of ID
     *
     * @return bool
     */
    public function deleteEntity(): bool;

    /**
     * Get array copy
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Get entity of ID
     *
     * @return EntityInterface|null
     */
    public function getEntity();

    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;
}