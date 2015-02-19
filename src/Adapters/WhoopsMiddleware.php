<?php namespace Winglian\MiddlewareAdapter\Adapters;

use Illuminate\Foundation\Application;
use Winglian\MiddlewareAdapter\AbstractMiddlewareAdapter;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Closure;

class WhoopsMiddleware extends AbstractMiddlewareAdapter {

    protected $adaptedClass = 'Whoops\StackPhp\WhoopsMiddleWare';

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function getClassAdapterInstance(HttpKernelInterface $app)
    {
        $adaptedClass = $this->adaptedClass;
        return new $adaptedClass($app, $this->shouldCatchExceptions(true, false), $this->shouldCatchErrors(true, false));
    }

    protected function wrapClosureInMiddleware(Closure $next)
    {
        $closureMiddleware = new static($this->app);
        $closureMiddleware->setNext($next);

        return $closureMiddleware;
    }

    protected function shouldCatchExceptions($default = true, $production_default = false)
    {
        if ($this->app->environment() == 'production')
        {
            return $production_default;
        }
        else
        {
            return $default;
        }
    }

    protected function shouldCatchErrors($default = true, $production_default = false)
    {
        if ($this->app->environment() == 'production')
        {
            return $production_default;
        }
        else
        {
            return $default;
        }
    }

}

