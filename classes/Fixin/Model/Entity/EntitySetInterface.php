<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Entity;

use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\PrototypeInterface;

interface EntitySetInterface extends PrototypeInterface, \Iterator, \Countable
{
    public const
        OPTION_ENTITY_CACHE = 'entityCache',
        OPTION_ID_FETCH_MODE = 'idFetchMode',
        OPTION_ITEMS = 'items',
        OPTION_PREFETCH_SIZE = 'prefetchSize',
        OPTION_REPOSITORY = 'repository',
        OPTION_STORAGE_RESULT = 'storageResult';

    /**
     * @return EntityIdInterface[]
     */
    public function getEntityIds(): array;

    public function getRepository(): RepositoryInterface;
    public function shuffle(): EntitySetInterface;
}
