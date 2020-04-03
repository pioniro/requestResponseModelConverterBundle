<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Service;

use Pioniro\RequestResponseModel\RequestModelInterface;
use Pioniro\RequestResponseModelConverterBundle\Event\Events;
use Pioniro\RequestResponseModelConverterBundle\Event\RequestDataGatheringEvent;
use Pioniro\RequestResponseModelConverterBundle\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class RequestModelArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * RequestModelArgumentResolver constructor.
     * @param Serializer $serializer
     * @param FormatProvider $formatProvider
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(Serializer $serializer, EventDispatcherInterface $dispatcher)
    {
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if (empty($argument->getType())) return false;
        foreach (class_implements($argument->getType()) as $inter) {
            if ($inter === RequestModelInterface::class)
                return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $event = new RequestDataGatheringEvent($request);
        $this->dispatch(Events::REQUEST_GATHERING, $event);

        if (!$event->hasData() || !$event->hasFormat()) {
            throw new Exception('Can not collect a data or the format of data from the request');
        }
        yield $this->serializer->deserialize($event->getData(), $argument->getType(), $event->getFormat(), []);
    }

    /**
     * @param $eventName
     * @param $event
     * @codeCoverageIgnore
     */
    protected function dispatch($eventName, $event)
    {
        if ($this->dispatcher instanceof ContractsEventDispatcherInterface) {
            $this->dispatcher->dispatch($event, $eventName);
        } else {
            $this->dispatcher->dispatch($eventName, $event);
        }
    }
}
