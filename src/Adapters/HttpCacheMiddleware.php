<?php namespace Winglian\MiddlewareAdapter\Adapters;

use Winglian\MiddlewareAdapter\AbstractMiddlewareAdapter;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HttpCache extends AbstractMiddlewareAdapter {

    protected $adaptedClass = '\Symfony\Component\HttpKernel\HttpCache\HttpCache';

    protected $httpCacheOptions = [
        'debug' => false,
        'default_ttl' => 0,
        'private_headers' => array('Authorization', 'Cookie'),
        'allow_reload' => false,
        'allow_revalidate' => false,
        'stale_while_revalidate' => 2,
        'stale_if_error' => 60,
    ];

    protected function getClassAdapterInstance(HttpKernelInterface $app)
    {
        $adaptedClass = $this->adaptedClass;
        $cacheStore = new \Symfony\Component\HttpKernel\HttpCache\Store(
            storage_path() . '/framework/cache'
        );

        return new $adaptedClass($app, $cacheStore, null, $this->httpCacheOptions);
    }

}
