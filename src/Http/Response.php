<?php

namespace pjpawel\LightApi\Http;

use function function_exists;

class Response
{

    public string $content;
    public ResponseStatus $status;
    public HeadersBag $headers;
    public string $version;

    public function __construct(string|array $content = '', ResponseStatus $status = ResponseStatus::OK, array $headers = [], $version = '1.0')
    {
        $this->status = $status;
        $this->headers = new HeadersBag($headers);
        $this->version = $version;
        if (is_string($content)) {
            $this->content = $content;
            $this->headers->add('Content-Type', 'text/html');
        } else {
            $this->content = json_encode($content);
            $this->headers->add('Content-Type', 'application/json');
        }
    }

    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        } /*elseif (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true)) {
            static::closeOutputBuffers(0, true);
            flush();
        }*/
    }

    private function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        /*foreach ($this->headers->allPreserveCaseWithoutCookies() as $name => $values) {
            //TODO: change
            $replace = 0 === strcasecmp($name, 'Content-Type');
            foreach ($values as $value) {
                header($name.': '.$value, $replace, $this->status->value);
            }
        }

        foreach ($this->headers->getCookies() as $cookie) {
            //TODO:
            header('Set-Cookie: '.$cookie, false, $this->status->value);
        }*/

        // status
        header(sprintf('HTTP/%s %s %s', $this->version, $this->status->value, $this->status->getText()), true, $this->status->value);
    }

    private function sendContent(): void
    {
        echo $this->content;
    }
}