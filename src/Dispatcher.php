<?php

/*
 * (C) 2019, AII (Alexey Ilyin).
 */

namespace Ailixter\Puzzle;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @author AII (Alexey Ilyin)
 */
class Dispatcher implements RequestHandlerInterface, MiddlewareInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private $queue = [];
    /**
     * @var MiddlewareInterface
     */
    private $fallback;

    /**
     * @param MiddlewareInterface $fallback - the middleware if nothing was found to handle a request
     *                                      (to return 404 or something like that)
     */
    public function __construct(MiddlewareInterface $fallback)
    {
        $this->fallback = $fallback;
    }

    final public function enqueue(MiddlewareInterface $middleware): self
    {
        $this->queue[] = $middleware;
        return $this;
    }

    final public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->queue) ?? $this->fallback;
        return $middleware->process($request, $this);
    }

    /**
     * Allows to chain Dispatcher to handlers queue.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler = null): ResponseInterface
    {
        return $this->handle($request);
    }
}
