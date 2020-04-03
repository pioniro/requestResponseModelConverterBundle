<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\EventSubscriber;

use Pioniro\RequestResponseModelConverterBundle\Event\Events;
use Pioniro\RequestResponseModelConverterBundle\Event\RequestDataGatheringEvent;
use Pioniro\RequestResponseModelConverterBundle\Service\FormatProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class RequestDataGatheringSubscriber implements EventSubscriberInterface
{
    protected const QUERY_METHODS = [
        Request::METHOD_CONNECT,
        Request::METHOD_OPTIONS,
        Request::METHOD_HEAD,
        Request::METHOD_GET,
    ];

    /**
     * @var FormatProvider
     */
    protected $formatProvider;

    /**
     * RequestDataGatheringListener constructor.
     * @param FormatProvider $formatProvider
     */
    public function __construct(FormatProvider $formatProvider)
    {
        $this->formatProvider = $formatProvider;
    }

    /**
     * @return array|string[]
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents()
    {
        return [Events::REQUEST_GATHERING => [
            'requestGatheringBody' => -90,
            'requestGatheringContent' => -91,
            'requestGatheringQuery' => -92,
        ]];
    }

    public function requestGatheringQuery(RequestDataGatheringEvent $event)
    {
        if ($event->hasData() && $event->hasFormat()) {
            return;
        }
        $request = $event->getRequest();
        if (!$request->isMethodSafe()) {
            return;
        }
        $event->setData(json_encode($request->query->all()));
        $event->setFormat('json');
    }

    public function requestGatheringContent(RequestDataGatheringEvent $event)
    {
        if ($event->hasData() && $event->hasFormat()) {
            return;
        }
        $request = $event->getRequest();
        if ($request->isMethodSafe() || $request->request->count() > 0) {
            return;
        }
        $event->setData($request->getContent());
        $event->setFormat($this->formatProvider->getRequestFormat($request));
    }

    public function requestGatheringBody(RequestDataGatheringEvent $event)
    {
        if ($event->hasData() && $event->hasFormat()) {
            return;
        }
        $request = $event->getRequest();
        if ($request->isMethodSafe() || $request->request->count() === 0) {
            return;
        }
        $event->setData(json_encode($request->request->all()));
        $event->setFormat('json');
    }

//    public function requestGathering(RequestDataGatheringEvent $event)
//    {
//        if ($event->hasData() && $event->hasFormat()) {
//            return;
//        }
//        $request = $event->getRequest();
//        $format = $this->formatProvider->getRequestFormat($request);
//        $data = null;
//        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
//            $data = json_encode($request->query->all());
//            $format = 'json';
//        } elseif ($this->isJson($request)) {
//            $data = $request->getContent();
//        }
//        $event->setData($data);
//        $event->setFormat($format);
//    }
}