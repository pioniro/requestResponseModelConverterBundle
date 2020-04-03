<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Pioniro\RequestResponseModel\RequestModelInterface;
use Pioniro\RequestResponseModelConverterBundle\EventSubscriber\ValidateSubscriber;
use Pioniro\RequestResponseModelConverterBundle\Exception\RequestModelInvalidException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @group validator
 */
class ValidateSubscriberTest extends TestCase
{

    public function testOnControllerArgumentsValid()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $subscriber = new ValidateSubscriber($validator);
        $model = new ValidRequestModel();

        $validator->expects($this->once())
            ->method('validate')
            ->with($model)
            ->willReturn(new ConstraintViolationList());

        $event = new ControllerArgumentsEvent(
            $this->createMock(HttpKernelInterface::class),
            function () {
            },
            [$model],
            new Request(),
            HttpKernelInterface::MASTER_REQUEST
        );
        $subscriber->onControllerArguments($event);
    }

    public function testOnControllerArgumentsInvalid()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $subscriber = new ValidateSubscriber($validator);
        $model = new ValidRequestModel();

        $validator->expects($this->once())
            ->method('validate')
            ->with($model)
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation('some', '', [], null, 'some', 'value')
            ]));

        $event = new ControllerArgumentsEvent(
            $this->createMock(HttpKernelInterface::class),
            function () {
            },
            [$model],
            new Request(),
            HttpKernelInterface::MASTER_REQUEST
        );
        $this->expectException(RequestModelInvalidException::class);
        $subscriber->onControllerArguments($event);
    }
}


class ValidRequestModel implements RequestModelInterface {

}