<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Exception;

use Throwable;

class NoSerializersException extends Exception
{
    protected const MESSAGE = 'No one serializer is provided. Install `jms/serializer-bundle` and/or `symfony/serializer-pack`';

    public function __construct($context = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, $context, $code, $previous);
    }

}