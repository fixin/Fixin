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

class ExistsTag extends Tag
{
    protected const
        THIS_REQUIRES = parent::THIS_REQUIRES + [
            self::REQUEST
        ],
        THIS_SETS = parent::THIS_SETS + [
            self::REQUEST => RequestInterface::class
        ];

    public const
        REQUEST = 'request';

    /**
     * @var RequestInterface
     */
    protected $request;

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
