<?php namespace Winglian\MiddlewareAdapter;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class AbstractMiddlewareAdapter
 *
 * Steps
 *  1. Laravel calls this and we use handleLaravel
 *  2. handleLaravel wraps the Closure for $next into another Symfony Middleware that the Adapted class calls
 *  3. handleLaravel instantiates the adapted class with the previously created middleware
 *  4. handleLaravel calls the `handle` method on the adapted class
 *  5. The adapted class would then do it's own thing (either before or after #6)
 *  6. When the adapted class calls handle for the wrapped Closure, we use handleSymfony
 *
 * @package Winglian\MiddlewareAdapter
 */
abstract class AbstractMiddlewareAdapter implements HttpKernelInterface {

    /**
     * @var string The class which implements that we are adapting to work with Laravel 5
     */
    protected $adaptedClass;

    /**
     * @var Closure The next item in the stack that the static class would call
     */
    protected $next;

    /**
     * Used to bind the next laravel middleware in the stack to the adapter
     *
     * @param callable $next
     */
    public function setNext(Closure $next)
    {
        $this->next = $next;
    }

    public function getNext()
    {
        return $this->next;
    }

    /**
     * Instantiates the adapted class, you should probably override this method as you need
     *
     * @param HttpKernelInterface $app
     * @return mixed
     */
    protected function getClassAdapterInstance(HttpKernelInterface $app)
    {
        $adaptedClass = $this->adaptedClass;
        return new $adaptedClass($app);
    }

    /**
     * wraps the adapted class when the middleware is requested from Laravel
     *
     * @param Request  $request
     * @param callable $next
     * @return mixed
     */
    protected function handleLaravel(Request $request, Closure $next)
    {
        // wrap the Closure in an HttpKernelInterface type Middleware
        $closureMiddleware = $this->wrapClosureInMiddleware($next);

        $handler = $this->getClassAdapterInstance($closureMiddleware);
        return $handler->handle($request);
    }

    /**
     * wrap the closure into it's own Middleware
     *
     * @param callable $next
     * @return static
     */
    protected function wrapClosureInMiddleware(Closure $next)
    {
        $closureMiddleware = new static;
        $closureMiddleware->setNext($next);

        return $closureMiddleware;
    }

    /**
     * wraps the $next app in the middleware stack from within the Symfony middleware
     * 
     * @param SymfonyRequest $request
     * @param int            $type
     * @param bool           $catch
     * @return mixed
     */
    protected function handleSymfony(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $next = $this->next;
        return $next($request);
    }

    /**
     * "overloaded" implementation for handle that determines which scope we are in
     *
     * @param SymfonyRequest $request
     * @param int            $type
     * @param bool           $catch
     * @return mixed
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $arguments = func_get_args();

        if ($arguments[1] instanceof Closure) {
            return call_user_func_array(array($this, 'handleLaravel'), $arguments);
        } else {
            return call_user_func_array(array($this, 'handleSymfony'), $arguments);
        }
    }
}

