<?php

namespace Quartet\BaseApi;

use League\OAuth2\Client\Token\AccessToken;
use Phake;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->client = new Client('clientId', 'clientSecret', 'redirectUri');
    }

    public function test_request()
    {
        // mock http client.
        $httpClient = Phake::mock('\Guzzle\Http\Client');
        Phake::when($httpClient)->createRequest('method', '/path/to/api', ['Authorization' => 'Bearer access_token'], null, ['param' => 1])->thenReturn('request');
        Phake::when($httpClient)->send('request')->thenReturn('response');

        $this->client->setHttpClient($httpClient);
        $this->client->token = new AccessToken(['access_token' => 'access_token']);

        $this->assertEquals('response', $this->client->request('method', '/path/to/api', ['param' => 1]));
    }

    public function test_request_before_authorized()
    {
        $this->setExpectedException('\Quartet\BaseApi\Exception\RuntimeException');

        $this->client->request('method', '/path/to/api');
    }

    public function test_request_with_http_error()
    {
        // mock response.
        $response = Phake::mock('\Guzzle\Http\Message\Response');
        Phake::when($response)->getBody()->thenReturn(json_encode([
            'error' => 'error',
            'error_description' => 'error_description',
        ]));

        // mock response exception.
        $responseException = Phake::mock('\Guzzle\Http\Exception\BadResponseException');
        Phake::when($responseException)->getResponse()->thenReturn($response);

        // mock http client.
        $httpClient = Phake::mock('\Guzzle\Http\Client');
        Phake::when($httpClient)->createRequest(Phake::anyParameters())->thenReturn('request');
        Phake::when($httpClient)->send('request')->thenThrow($responseException);

        $this->client->setHttpClient($httpClient);
        $this->client->token = new AccessToken(['access_token' => 'access_token']);

        $this->setExpectedException('\Quartet\BaseApi\Exception\BaseApiException');

        $this->client->request('method', '/path/to/api');
    }

    public function test_authorize()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');

        $this->client->setProvider($provider);

        $this->client->authorize(['param' => 1]);
        Phake::verify($provider)->authorize(['param' => 1]);
    }

    public function test_getAuthorizationUrl()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');

        $this->client->setProvider($provider);

        $this->client->getAuthorizationUrl(['param' => 1]);
        Phake::verify($provider)->getAuthorizationUrl(['param' => 1]);
    }

    public function test_authenticate()
    {
        $this->assertNull($this->client->token);

        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');
        Phake::when($provider)->getAccessToken(Phake::anyParameters())->thenReturn('token');

        $this->client->setProvider($provider);

        $this->client->authenticate('test_code');
        Phake::verify($provider)->getAccessToken('authorization_code', ['code' => 'test_code']);

        $this->assertEquals('token', $this->client->token);
    }

    public function test_refresh()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');

        $this->client->setProvider($provider);
        $this->client->token = new AccessToken([
            'access_token' => '',
            'refresh_token' => 'test_refresh_token'
        ]);

        $this->client->refresh();
        Phake::verify($provider)->getAccessToken('refresh_token', ['refresh_token' => 'test_refresh_token']);
    }
}