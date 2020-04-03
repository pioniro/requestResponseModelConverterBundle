<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Pioniro\RequestResponseModelConverterBundle\Service\FormatProvider;
use Symfony\Component\HttpFoundation\Request;

class FormatProviderTest extends TestCase
{
    /**
     * @dataProvider requestProvider
     * @param Request $request
     * @param string $format
     */
    public function testGetRequestFormat(Request $request, string $format)
    {
        $provider = new FormatProvider();
        $this->assertEquals($format, $provider->getRequestFormat($request));
    }

    /**
     * @dataProvider requestProvider
     * @param Request $request
     * @param string $format
     */
    public function testGetResponseFormat(Request $request, string $format)
    {
        $provider = new FormatProvider();
        $this->assertEquals($format, $provider->getResponseFormat($request));
    }

    public function requestProvider()
    {
        return [
            'some format' => [
                'request' => new Request([], [], ['_format' => 'some']),
                'format' => 'some'
            ],
            'default format' => [
                'request' => new Request(),
                'format' => 'json'
            ],
        ];
    }
}
