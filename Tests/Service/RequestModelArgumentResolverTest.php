<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Tests\Service;

use Generator;
use PHPUnit\Framework\TestCase;
use Pioniro\RequestResponseModel\RequestModelInterface;
use Pioniro\RequestResponseModelConverterBundle\Event\RequestDataGatheringEvent;
use Pioniro\RequestResponseModelConverterBundle\Exception\Exception;
use Pioniro\RequestResponseModelConverterBundle\Service\RequestModelArgumentResolver;
use Pioniro\RequestResponseModelConverterBundle\Service\Serializer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestModelArgumentResolverTest extends TestCase
{

    /**
     * @group resolver
     * @param Request $request
     * @param $data
     * @dataProvider resolverProvider
     */
    public function testResolve(Request $request, $data)
    {
        $serializer = new TestSerializer();
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->returnCallback(function (RequestDataGatheringEvent $event) {
                $event->setData($event->getRequest()->getContent());
                $event->setFormat($event->getRequest()->attributes->get('_format'));
                return $event;
            }));
        $argument = new ArgumentMetadata('arg', 'array', false, false, null, false);
        $resolver = new RequestModelArgumentResolver($serializer, $dispatcher);
        /** @var Generator $generator */
        $generator = $resolver->resolve($request, $argument);
        foreach ($generator as $item) {
            $this->assertEquals($data, $item);
        }
    }

    /**
     * @group resolver
     * @param Request $request
     * @param $data
     * @dataProvider resolverProvider
     */
    public function testResolveEmptyData(Request $request, $data)
    {
        $serializer = new TestSerializer();
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->once())
            ->method('dispatch');
        $argument = new ArgumentMetadata('arg', 'array', false, false, null, false);
        $resolver = new RequestModelArgumentResolver($serializer, $dispatcher);
        $this->expectException(Exception::class);
        /** @var Generator $generator */
        $generator = $resolver->resolve($request, $argument);
        $generator->next();
    }

    public function resolverProvider()
    {
        return [
            'data from body' => [
                'request' =>
                    $request = new Request(
                        [],
                        [],
                        ['_format' => 'json'],
                        [],
                        [],
                        [],
                        json_encode(['some' => 'data'])
                    ),
                'data' => ['some' => 'data']
            ]
        ];
    }

    /**
     * @dataProvider modelProvider
     * @param string $class
     * @param bool $isImplemented
     * @group resolver
     */
    public function testSupports(string $class, bool $isImplemented)
    {
        $resolver = new RequestModelArgumentResolver(
            new TestSerializer(),
            $this->createMock(EventDispatcherInterface::class)
        );
        $request = new Request();
        $argument = new ArgumentMetadata('arg', $class, false, false, null, false);
        $this->assertEquals($isImplemented, $resolver->supports($request, $argument));
    }

    public function modelProvider()
    {
        return [
            'model implemented' => [
                'class' => OurModel::class,
                'isImplement' => true,
            ],
            'not model implemented' => [
                'class' => NotOurModel::class,
                'isImplement' => false,
            ]
        ];
    }
}


class TestSerializer extends Serializer
{

    public function __construct()
    {
    }

    public function serialize($data, $format, $context = []): string
    {
        return json_encode($data);
    }

    public function deserialize($string, $type, $format, $context = [])
    {
        return json_decode($string, true);
    }

}

class OurModel implements RequestModelInterface
{

}

class NotOurModel
{

}