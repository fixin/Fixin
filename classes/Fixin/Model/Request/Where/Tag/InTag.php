<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Model\Request\Where\Tag;

use Fixin\Model\Request\RequestInterface;

class InTag extends IdentifierTag
{
    protected const
        THIS_REQUIRES = parent::THIS_REQUIRES + [
            self::VALUES
        ],
        THIS_SETS = parent::THIS_SETS + [
            self::VALUES => [self::ARRAY_TYPE, RequestInterface::class]
        ];

    public const
        VALUES = 'values';

    /**
     * @var array|RequestInterface
     */
    protected $values;

    /**
     * @return array|RequestInterface
     */
    public function getValues()
    {
        return $this->values;
    }
}
