<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Exception;

use Pioniro\ContextableException\ContextableInterface;
use Pioniro\ContextableException\ContextableTrait;
use RuntimeException;
use Throwable;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class Exception extends RuntimeException implements ContextableInterface
{
    use ContextableTrait;

    public function __construct($message = "", $context = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->addContext($context);
    }
}