Laravel Middleware Adapters for HttpKernelInterface Middlewares
===============================================================

Easily resuse your Laravel 4 and Symfony middlewares in Laravel.

How To Use
----------

Also see the example HttpCacheMiddlewareAdapter in `src`

```php
class YourMiddleware extends \Winglian\MiddlewareAdapter\AbstractMiddlewareAdapter {

    protected $adaptedClass = '\Namespace\Prefix\YourHttpKernelInterfaceMiddleware';

    protected function getClassAdapterInstance(HttpKernelInterface $app)
    {
        /**
         * You can optionally change this logic if you need to resolve your adapter from the IoC
         * or pass in other options to the class.
         */
        return parent::getClassAdapterInstance($app);
    }
}
```
