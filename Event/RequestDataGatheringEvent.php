<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class RequestDataGatheringEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string|null
     */
    protected $data;

    /**
     * @var string|null
     */
    protected $format;

    /**
     * RequestGatheringEvent constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param string|null $data
     */
    public function setData(?string $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string|null $format
     */
    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function hasData(): bool
    {
        return !is_null($this->data);
    }

    public function hasFormat(): bool
    {
        return !is_null($this->format);
    }
}