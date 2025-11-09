<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
//use Livekit\SIPClient;
use MFullbrook\Livekit\AccessToken;
use MFullbrook\Livekit\Grants\SIPGrant;
use Psr\Http\Message\RequestInterface;

include 'vendor/autoload.php';

$addr = 'https://kaira-ventures-test-1zf6z073.livekit.cloud';



class AuthorizationInjector
{
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $apiKey = 'APIz8EwPQFE9XJa';
            $secret = 'fuxae6irRcamrf517FjpyEWHofclKQCFeVlm7KeVzKLM';
            $accessToken = new AccessToken($apiKey, $secret);
            $accessToken->sipGrant = new SIPGrant(admin: true, call: true);
            print_r($accessToken);
            print_r($accessToken->toJwt());
            $request = $request->withHeader('Authorization', 'Bearer '.$accessToken->toJwt());
            return $handler($request, $options);
        };
    }

}

//$stack = HandlerStack::create();
//$stack->push(new AuthorizationInjector, 'withAuthorization');
//
//$httpClient = new Client(['handler' => $stack]);
//
//$client = new SIPClient($addr, $httpClient);
//
//print_r($client->ListSIPDispatchRule([], new \Livekit\ListSIPDispatchRuleRequest()));

$apiKey = 'APIz8EwPQFE9XJa';
$secret = 'fuxae6irRcamrf517FjpyEWHofclKQCFeVlm7KeVzKLM';
$client = new \MFullbrook\Livekit\SIPClient($addr, $apiKey, $secret);

print_r($client->listDispatchRules());