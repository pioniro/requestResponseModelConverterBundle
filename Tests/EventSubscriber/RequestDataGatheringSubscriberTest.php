<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Pioniro\RequestResponseModelConverterBundle\Event\RequestDataGatheringEvent;
use Pioniro\RequestResponseModelConverterBundle\EventSubscriber\RequestDataGatheringSubscriber;
use Pioniro\RequestResponseModelConverterBundle\Service\FormatProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group collector
 */
class RequestDataGatheringSubscriberTest extends TestCase
{

    public function testRequestGatheringQuery()
    {
        $data = ['some' => 'value'];
        $string = json_encode($data);
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request($data, [], [], [], [], [], null);
        $request->setMethod(Request::METHOD_GET);
        $event = new RequestDataGatheringEvent($request);
        $subscriber->requestGatheringQuery($event);
        $this->assertEquals($string, $event->getData());
        $this->assertEquals('json', $event->getFormat());
    }

    public function testRequestGatheringQuerySkipOverhandler()
    {
        $data = ['some' => 'value'];
        $string = json_encode($data);
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request($data, [], [], [], [], [], null);
        $event = new RequestDataGatheringEvent($request);
        $event->setFormat('json');
        $event->setData($string);
        $this->assertEquals(null, $subscriber->requestGatheringQuery($event));
    }

    public function testRequestGatheringQuerySkipUnsupported()
    {
        $data = ['some' => 'value'];
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request($data, [], [], [], [], [], null);
        $request->setMethod(Request::METHOD_POST);
        $event = new RequestDataGatheringEvent($request);
        $this->assertEquals(null, $subscriber->requestGatheringQuery($event));
    }

    public function testRequestGatheringBody()
    {
        $data = ['some' => 'value'];
        $string = json_encode($data);
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request([], $data, [], [], [], [], null);
        $request->setMethod(Request::METHOD_POST);
        $event = new RequestDataGatheringEvent($request);
        $subscriber->requestGatheringBody($event);
        $this->assertEquals($string, $event->getData());
        $this->assertEquals('json', $event->getFormat());
    }

    public function testRequestGatheringBodySkipHandled()
    {
        $data = ['some' => 'value'];
        $string = json_encode($data);
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request([], $data, [], [], [], [], null);
        $request->setMethod(Request::METHOD_POST);
        $event = new RequestDataGatheringEvent($request);
        $event->setData($string);
        $event->setFormat('json');
        $this->assertEquals(null, $subscriber->requestGatheringBody($event));
    }

    public function testRequestGatheringBodySkipUnsupported()
    {
        $data = ['some' => 'value'];
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request([], $data, [], [], [], [], null);
        $request->setMethod(Request::METHOD_GET);
        $event = new RequestDataGatheringEvent($request);
        $this->assertEquals(null, $subscriber->requestGatheringBody($event));
    }

    public function testRequestGatheringContent()
    {
        $data = ['some' => 'value'];
        $string = json_encode($data);
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request([], [], [
            '_format' => 'json'
        ], [], [], [], $string);
        $request->setMethod(Request::METHOD_POST);
        $event = new RequestDataGatheringEvent($request);
        $subscriber->requestGatheringContent($event);
        $this->assertEquals($string, $event->getData());
        $this->assertEquals('json', $event->getFormat());
    }

    public function testRequestGatheringContentSkipHandled()
    {
        $data = ['some' => 'value'];
        $string = json_encode($data);
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request([], [], [
            '_format' => 'json'
        ], [], [], [], $string);
        $request->setMethod(Request::METHOD_POST);
        $event = new RequestDataGatheringEvent($request);
        $event->setData($string);
        $event->setFormat('json');
        $this->assertEquals(null, $subscriber->requestGatheringContent($event));
    }

    public function testRequestGatheringContentSkipUnsupported()
    {
        $data = ['some' => 'value'];
        $subscriber = new RequestDataGatheringSubscriber(new FormatProvider());
        $request = new Request([], [], [
            '_format' => 'json'
        ], [], [], [], json_encode($data));
        $request->setMethod(Request::METHOD_GET);
        $event = new RequestDataGatheringEvent($request);
        $this->assertEquals(null, $subscriber->requestGatheringContent($event));
    }
}
