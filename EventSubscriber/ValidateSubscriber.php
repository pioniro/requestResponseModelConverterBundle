<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\EventSubscriber;

use Pioniro\RequestResponseModel\RequestModelInterface;
use Pioniro\RequestResponseModelConverterBundle\Exception\RequestModelInvalidException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class ValidateSubscriber implements EventSubscriberInterface
{

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * ValidateSubscriber constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onControllerArguments'
        ];
    }

    /**
     * @param ControllerArgumentsEvent $event
     * @throws RequestModelInvalidException
     */
    public function onControllerArguments(ControllerArgumentsEvent $event)
    {
        foreach ($event->getArguments() as $argument) {
            if ($argument instanceof RequestModelInterface) {
                $this->validateRequestModel($argument);
            }
        }
    }

    /**
     * @param RequestModelInterface $argument
     * @throws RequestModelInvalidException
     */
    private function validateRequestModel(RequestModelInterface $argument)
    {
        $errors = $this->validator->validate($argument);
        if (count($errors) === 0) {
            return;
        }
        throw new RequestModelInvalidException($errors);
    }
}