<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository\Where;

abstract class WhereIdentifier extends Where {

    const OPTION_IDENTIFIER = 'identifier';
    const THIS_REQUIRES = [
        self::OPTION_IDENTIFIER => self::TYPE_ANY,
    ];

    /**
     * @var string|RepositoryRequestInterface|\Closure
     */
    protected $identifier;

    /**
     * Get identifier
     *
     * @return string|RepositoryRequestInterface|\Closure
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Set identifier
     *
     * @param string|RepositoryRequestInterface|\Closure $identifier
     */
    protected function setIdentifier($identifier) {
        $this->identifier = $identifier instanceof \Closure ? $this->closureToRequest($identifier) : $identifier;
    }
}