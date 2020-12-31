<?php

namespace CompleteSolar\ApiClientsTests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\UriInterface;

class FakeClient extends Client
{
    protected $postHistory = [];

    /**
     * @param string|UriInterface $uri
     * @param array $options
     * @return Response|\Psr\Http\Message\ResponseInterface
     */
    public function post($uri, array $options = [])
    {
        $this->postHistory[] = [
            'time' => strtotime('now'),
            'url' => (string) $uri,
            'options' => $options,
        ];

        return new Response();
    }

    /**
     * @return array
     */
    public function getPostHistory(): array
    {
        return $this->postHistory;
    }
}