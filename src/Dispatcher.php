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
     * @var ResponseInterface
     */
    private $fallback;

    /**
     * @param ResponseInterface $fallback - the response if nothing was found to handle a request
     *                                      (404 or something like that)
     */
    public function __construct(ResponseInterface $fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     */
    public function add(MiddlewareInterface $middleware): self
    {
        $this->queue[] = $middleware;
        return $this;
    }

    /**
     */
    final public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->queue) ?? $this;
        return $middleware->process($request, $this);
    }

    /**
     * Returns fallback response by default.
     * Override it to customize its behavior.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        return $this->fallback;
    }
}
