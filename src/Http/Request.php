<?php

namespace pjpawel\LightApi\Http;

use pjpawel\LightApi\Http\Exception\ForbiddenHttpException;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-consistent-constructor
 */
class Request
{
    public string $ip;
    public string $path;
    public string $method;
    public ValuesBag $query;
    public ValuesBag $request;
    public ValuesBag $attributes;
    public ValuesBag $server;
    public ValuesBag $files;
    public ValuesBag $cookies;
    public ?string $content;

    public function __construct(
        array $query = [],
        array $request = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        array $attributes = [],
        $content = null)
    {
        $this->query = new ValuesBag($query);
        $this->request = new ValuesBag($request);
        $this->attributes = new ValuesBag($attributes);
        $this->server = new ValuesBag($server);
        $this->files = new ValuesBag($files);
        $this->cookies = new ValuesBag($cookies);
        $this->content = $content;

        $this->ip = $this->server->get('REMOTE_ADDR');
        $this->path = $this->server->get('REQUEST_URI');
        $this->method = $this->server->get('REQUEST_METHOD', 'GET');
    }

    public static function makeFromGlobals(): static
    {
        return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }

    /**
     * @param array $trustedIPs
     * @return void
     * @throws ForbiddenHttpException
     */
    public function validateIp(array $trustedIPs = []): void
    {
        if (empty($trustedIPs)) {
            return;
        }
        if (!in_array($this->ip, $trustedIPs)) {
            throw new ForbiddenHttpException();
        }
    }

    public function logRequest(LoggerInterface $logger): void
    {
        $logger->info(
            sprintf('Requested path: %s, with %s method from ip: %s',
                $this->path, $this->method, $this->ip)
        );
    }
}