<?php

namespace MFullbrook\Livekit;

use GuzzleHttp\Client;
use MFullbrook\Livekit\Grants\SIPGrant;
use MFullbrook\Livekit\Grants\VideoGrant;

/**
 * Abstract base class for LiveKit service clients
 */
abstract class ServiceClient
{
    private const string AUTHORIZATION = 'authorization';

    protected Client $httpClient;

    protected string $host;

    protected string $apiKey;

    protected string $apiSecret;

    /**
     * Create a new service client
     *
     * @param  string  $host  LiveKit server host URL
     * @param  string  $apiKey  API Key
     * @param  string  $apiSecret  API Secret
     * @param  array<string, mixed>  $httpOptions  Optional HTTP client options
     */
    public function __construct(string $host, string $apiKey, string $apiSecret, array $httpOptions = [])
    {
        $this->host = rtrim($host, '/');
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;

        // Create Guzzle HTTP client with default options
        $defaultOptions = [
            'base_uri' => $this->host,
            'timeout' => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ];

        $this->httpClient = new Client(array_merge($defaultOptions, $httpOptions));
    }

    /**
     * Generate authorization header with JWT token
     *
     * @param  VideoGrant|null  $grants  Video grants to include in token
     * @param  SIPGrant|null  $sip  SIP grants to include in token
     * @return array<string, string> Headers array with authorization
     */
    protected function authHeader(?VideoGrant $grants = null, ?SIPGrant $sip = null): array
    {
        $token = new AccessToken($this->apiKey, $this->apiSecret);

        if ($grants !== null) {
            $token->videoGrant = $grants;
        }

        if ($sip !== null) {
            $token->sipGrant = $sip;
        }

        $jwt = $token->toJwt();

        return [
            self::AUTHORIZATION => sprintf('Bearer %s', $jwt),
        ];
    }

    /**
     * Generate authorization header for API requests (admin access)
     *
     * @return array<string, string> Headers array with authorization
     */
    protected function authHeaderAdmin(): array
    {
        $grants = new VideoGrant;
        $grants->roomCreate = true;
        $grants->roomList = true;
        $grants->roomRecord = true;
        $grants->roomAdmin = true;

        return $this->authHeader($grants);
    }
}

