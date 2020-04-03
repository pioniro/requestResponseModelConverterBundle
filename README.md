Contextable exception
=====
Is a common interfaces for adding a context in your exceptions.

It may be useful for providing more context data in logger
(or sentry, etc) more context data

*before:*
```php

function badFunction($id)
{
    throw new \Exception(sprintf('bad Id: %d', $id));
}
```

*after:*

```php

use Pioniro\ContextableException\ContextableInterface;
use Pioniro\ContextableException\ContextableTrait;

class MyException extends \Exception implements ContextableInterface {
    use ContextableTrait;
}

function badFunction($id)
{
    throw (new MyException('bad Id'))->addContext(['id' => $id]);
}
```

OR


```php

use Pioniro\ContextableException\ContextableInterface;

function badSuperFunction($id, $name)
{
    try {
        badThirdPartyFunction($id);
    } catch (ContextableInterface $e) {
        $e->addContext(['name' => $name]);
        throw $e;
    }
}
```

Did you see this? We provide more data for exception

That's why this library is.
