<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\EventSubscriber;

use Pioniro\RequestResponseModel\ResponseModelInterface;
use Pioniro\RequestResponseModelConverterBundle\Service\FormatProvider;
use Pioniro\RequestResponseModelConverterBundle\Service\Serializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mime\MimeTypesInterface;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class ResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var FormatProvider
     */
    protected $formatProvider;

    /**
     * @var MimeTypesInterface
     */
    protected $mimeGuesser;

    /**
     * ResponseListener constructor.
     * @param Serializer $serializer
     * @param FormatProvider $formatProvider
     * @param MimeTypesInterface $mimeGuesser
     */
    public function __construct(Serializer $serializer, FormatProvider $formatProvider, MimeTypesInterface $mimeGuesser)
    {
        $this->serializer = $serializer;
        $this->formatProvider = $formatProvider;
        $this->mimeGuesser = $mimeGuesser;
    }

    /**
     * @return array|string[]
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'onControllerResponse',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ViewEvent|\Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     */
    public function onModelResponse($event): void
    {
        $model = $event->getControllerResult();
        if (!($model instanceof ResponseModelInterface)) {
            return;
        }
        $format = $this->formatProvider->getResponseFormat($event->getRequest());
        $event->setResponse(
            new Response(
                $this->serializer->serialize($model, $format),
                200,
                [
                    'Content-Type' => $this->mimeGuesser->getMimeTypes($format)
                ]
            )
        );
    }
}