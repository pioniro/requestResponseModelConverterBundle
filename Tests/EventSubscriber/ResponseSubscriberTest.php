<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Pioniro\RequestResponseModel\ResponseModelInterface;
use Pioniro\RequestResponseModelConverterBundle\EventSubscriber\ResponseSubscriber;
use Pioniro\RequestResponseModelConverterBundle\Service\FormatProvider;
use Pioniro\RequestResponseModelConverterBundle\Tests\Service\TestSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Mime\MimeTypesInterface;

/**
 * @group response
 */
class ResponseSubscriberTest extends TestCase
{
    public function testOnModelResponse()
    {
        $model = new ResponseModel();
        $model->some = 'value';
        $string = json_encode($model);

        $serializer = new TestSerializer();
        $formatProvider = new FormatProvider();
        $mime = $this->createMock(MimeTypesInterface::class);
        $subscriber = new ResponseSubscriber($serializer, $formatProvider, $mime);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $event = new ViewEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $model);
        $subscriber->onModelResponse($event);
        $this->assertTrue($event->hasResponse());
        $response = $event->getResponse();
        $this->assertEquals($string, $response->getContent());
    }
    public function testOnModelResponseInvalidModel()
    {
        $model = new NotOurResponseModel();
        $model->some = 'value';

        $serializer = new TestSerializer();
        $formatProvider = new FormatProvider();
        $mime = $this->createMock(MimeTypesInterface::class);
        $subscriber = new ResponseSubscriber($serializer, $formatProvider, $mime);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $event = new ViewEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $model);
        $subscriber->onModelResponse($event);
        $this->assertFalse($event->hasResponse());
    }
}


class ResponseModel implements ResponseModelInterface {
    public $some;
}

class NotOurResponseModel {
    public $some;
}