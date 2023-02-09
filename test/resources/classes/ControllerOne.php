<?php

namespace pjpawel\LightApi\Test\resources\classes;

use pjpawel\LightApi\Endpoint\AsRoute;
use pjpawel\LightApi\Http\Request;
use pjpawel\LightApi\Http\Response;

class ControllerOne
{

    #[AsRoute('/index')]
    public function index(): Response
    {
        return new Response('index');
    }

    #[AsRoute('/echo/{identifier}', methods: ['POST'])]
    public function echo(string $identifier): Response
    {
        return new Response('echo');
    }

    #[AsRoute('/echo/{identifierInt}', methods: ['GET'])]
    public function echoInt(int $identifierInt): Response
    {
        return new Response('echo' . $identifierInt);
    }

    #[AsRoute('/echo/{channel}/list/{identifier}', methods: ['POST', 'PUT'])]
    public function echoTwoParams(string $channel, int $identifier): Response
    {
        return new Response("echo:$channel:$identifier");
    }

    #[AsRoute('/echo/{identifier}', methods: ['GET'])]
    public function echoQuery(Request $request, string $identifier): Response
    {
        $id = $request->query->get('id');
        return new Response('requestId:' . $id);
    }

}