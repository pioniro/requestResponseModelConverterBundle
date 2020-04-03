<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class RequestModelInvalidException extends Exception
{
    public const CONSTRAINT_VIOLATIONS_KEY = 'constraint_violations';

    /**
     * RequestModelInvalidException constructor.
     * @param ConstraintViolationListInterface $errors
     * @param array $context
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($errors, $context = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct('invalid request', $context, $code, $previous);
        $this->addContext([self::CONSTRAINT_VIOLATIONS_KEY => $errors]);
    }

}