<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Model\Repository\Where;

use Fixin\Resource\Prototype;
use Fixin\Model\Repository\RepositoryRequestInterface;

abstract class Where extends Prototype implements WhereInterface {

    const THIS_REQUIRES = [
        self::OPTION_JOIN => self::TYPE_STRING
    ];

    /**
     * @var string
     */
    protected $join = static::JOIN_AND;

    /**
     * @var boolean
     */
    protected $negated = false;

    /**
     * Closure to request process
     *
     * @param \Closure $closure
     * @return RepositoryRequestInterface
     */
    protected function closureToRequest(\Closure $closure) {
        $request = new static();
        $where($request);

        return $request;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\Where\WhereInterface::getJoin()
     */
    public function getJoin(): string {
        return $this->join;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Model\Repository\Where\WhereInterface::isNegated()
     */
    public function isNegated(): boolean {
        return $this->negated;
    }

    /**
     * Set join
     *
     * @param string $join
     */
    protected function setJoin(string $join) {
        $this->join = $join;
    }

    /**
     * Set negated
     *
     * @param boolean $negated
     */
    protected function setNegated(boolean $negated) {
        $this->negated = $negated;
    }
}