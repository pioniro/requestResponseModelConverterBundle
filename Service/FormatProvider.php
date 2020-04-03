<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class FormatProvider
{
    public function getRequestFormat(Request $request): string
    {
        return $request->attributes->has('_format') ? $request->attributes->get('_format') : 'json';
    }

    public function getResponseFormat(Request $request): string
    {
        return $request->attributes->has('_format') ? $request->attributes->get('_format') : 'json';
    }
}