The request and response models converter bundle
=====
This is a bundle, that does this magic:

*before:*
```php
// Controller/MyAwfulController.php
namespace App\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class MyAwfulController extends AbstractController
{
    public function sum(Request $request)
    {
        if($request->isMethod('POST')) {
            $a = $request->request->getDigits('a');
            $b = $request->request->getDigits('b');
        } else {
            $a = $request->query->getDigits('a');
            $b = $request->query->getDigits('b');
        }
        if(is_null($a) || is_null($b)) {
            throw new RuntimeException('a and b should not be null');
        }
        return new JsonResponse(['result' => floatval($a) + floatval($b)]);
    }
}
```

Or using the forms. Awful, isn't it?
 
*after:*

```php

// Model\Request\SumRequest.php
namespace App\Model\Request;

use JMS\Serializer\Annotation as Serializer;
use Pioniro\RequestResponseModel\RequestModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SumRequest implements RequestModelInterface
{
    /**
    * @var float
    * @Serializer\Type("float")
    * @Assert\NotNull()
    */
    protected $a;

    /**
    * @var float
    * @Serializer\Type("float")
    * @Assert\NotNull()
    */
    protected $b;

    public function getA(): float
    {
        return $this->a;
    }

    public function getB(): float
    {
        return $this->b;
    }

}
// Model\Request\ResultResponse.php
namespace App\Model\Response;

use JMS\Serializer\Annotation as Serializer;
use Pioniro\RequestResponseModel\RequestModelInterface;
use Pioniro\RequestResponseModel\ResponseModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ResultResponse implements ResponseModelInterface
{
    /**
    * @var float
    * @Serializer\Type("float")
    */
    protected $result;

    public function __construct(float $result) {
        $this->result = $result;
    }
}

// Controller/MyAwesomeController.php
namespace App\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class MyAwesomeController extends AbstractController
{
    public function sum(SumRequest $request)
    {
        return new ResultResponse($request->getA() + $request->getB());
    }
}
```


Did you see this? No manual validation, no manual model filling,
no working with exception, no badly testable Response! Just your logic.

That's why this bundle is.
