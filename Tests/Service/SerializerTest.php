<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Tests\Service;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as JmsSerializer;
use PHPUnit\Framework\TestCase;
use Pioniro\RequestResponseModelConverterBundle\Exception\NoSerializersException;
use Pioniro\RequestResponseModelConverterBundle\Service\Serializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializer;

/**
 * @group serializer
 */
class SerializerTest extends TestCase
{
    public function testEmptySerialize()
    {
        $this->expectException(NoSerializersException::class);
        new Serializer();
    }

    public function testJmsSerialize()
    {
        if (!$this->checkConcreteSerializers(JmsSerializer::class)) {
            return;
        }
        $data = ['some' => 'data'];
        $format = 'json';
        $groups = ['group1', 'group2'];
        $string = json_encode($data);

        $concreteSerializer = $this->createMock(JmsSerializer::class);
        $concreteSerializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($data),
                $this->equalTo($format),
                $this->equalTo((new SerializationContext())->setGroups($groups)))
            ->willReturn($string);
        $serializer = new Serializer($concreteSerializer);
        $actualData = $serializer->serialize($data, $format, $groups);
        $this->assertEquals($string, $actualData);
    }

    public function testSymfonySerialize()
    {
        if (!$this->checkConcreteSerializers(SymfonySerializer::class)) {
            return;
        }
        $data = ['some' => 'data'];
        $format = 'json';
        $groups = ['group1', 'group2'];
        $string = json_encode($data);

        $concreteSerializer = $this->createMock(SymfonySerializer::class);
        $concreteSerializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->equalTo($data),
                $this->equalTo($format),
                $this->equalTo(['groups' => $groups])
            )
            ->willReturn($string);
        $serializer = new Serializer(null, $concreteSerializer);
        $actualData = $serializer->serialize($data, $format, $groups);
        $this->assertEquals($string, $actualData);
    }

    public function testJmsDeserialize()
    {
        if (!$this->checkConcreteSerializers(JmsSerializer::class)) {
            return;
        }
        $data = ['some' => 'data'];
        $format = 'json';
        $groups = ['group1', 'group2'];
        $string = json_encode($data);

        $concreteSerializer = $this->createMock(JmsSerializer::class);
        $concreteSerializer->expects($this->once())
            ->method('deserialize')
            ->with(
                $this->equalTo($string),
                $this->equalTo('array'),
                $this->equalTo($format),
                $this->equalTo((new DeserializationContext())->setGroups($groups)))
            ->willReturn($data);
        $serializer = new Serializer($concreteSerializer);
        $actualData = $serializer->deserialize($string, 'array', $format, $groups);
        $this->assertEquals($data, $actualData);
    }

    public function testSymfonyDeserialize()
    {
        if (!$this->checkConcreteSerializers(SymfonySerializer::class)) {
            return;
        }
        $data = ['some' => 'data'];
        $format = 'json';
        $groups = ['group1', 'group2'];
        $string = json_encode($data);

        $concreteSerializer = $this->createMock(SymfonySerializer::class);
        $concreteSerializer->expects($this->once())
            ->method('deserialize')
            ->with(
                $this->equalTo($string),
                $this->equalTo('array'),
                $this->equalTo($format),
                $this->equalTo(['groups' => $groups])
            )
            ->willReturn($data);
        $serializer = new Serializer(null, $concreteSerializer);
        $actualData = $serializer->deserialize($string, 'array', $format, $groups);
        $this->assertEquals($data, $actualData);
    }

    protected function checkConcreteSerializers($class)
    {
        if (
            !interface_exists(JmsSerializer::class) &&
            !interface_exists(SymfonySerializer::class)
        ) {
            throw new NoSerializersException();
        }
        if (!interface_exists($class)) {
            $this->markTestSkipped(sprintf('%s is not provided', $class));
            return false;
        }
        return true;
    }
}
