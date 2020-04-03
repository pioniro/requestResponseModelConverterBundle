<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Service;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as JMSSerializer;
use Pioniro\RequestResponseModelConverterBundle\Exception\NoSerializersException;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializer;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class Serializer
{
    /**
     * @var JMSSerializer|null
     */
    protected $jmsSerializer;

    /**
     * @var SymfonySerializer|null
     */
    protected $symfonySerializer;

    /**
     * Serializer constructor.
     * @param JMSSerializer|null $jmsSerializer
     * @param SymfonySerializer $symfonySerializer
     */
    public function __construct(?JMSSerializer $jmsSerializer = null, ?SymfonySerializer $symfonySerializer = null)
    {
        if (is_null($jmsSerializer) && is_null($symfonySerializer)) {
            throw new NoSerializersException();
        }
        $this->jmsSerializer = $jmsSerializer;
        $this->symfonySerializer = $symfonySerializer;
    }

    public function serialize($data, $format, $context = []): string
    {
        if ($this->jmsSerializer instanceof JMSSerializer) {
            $ctx = new SerializationContext();
            $ctx->setGroups($context);
            return $this->jmsSerializer->serialize($data, $format, $ctx);
        }
        return $this->symfonySerializer->serialize($data, $format, ['groups' => $context]);
    }

    public function deserialize($string, $type, $format, $context = [])
    {
        if ($this->jmsSerializer instanceof JMSSerializer) {
            $ctx = new DeserializationContext();
            $ctx->setGroups($context);
            return $this->jmsSerializer->deserialize($string, $type, $format, $ctx);
        }
        return $this->symfonySerializer->deserialize($string, $type, $format, ['groups' => $context]);
    }
}