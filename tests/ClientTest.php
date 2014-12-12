<?php
namespace Quartet\BaseApi;

use Guzzle\Http\ClientInterface;
use League\OAuth2\Client\Provider\ProviderInterface;
use League\OAuth2\Client\Token\AccessToken;
use Phake;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private function buildClient(ProviderInterface $provider = null, ClientInterface $httpClient = null)
    {
        return new Client('', '', '', [], $provider, $httpClient);
    }

    public function test_request()
    {
        // mock http client.
        $httpClient = Phake::mock('\Guzzle\Http\Client');
        Phake::when($httpClient)->createRequest('method', '/path/to/api', ['Authorization' => 'Bearer access_token'], null, ['param' => 1])->thenReturn('request');
        Phake::when($httpClient)->send('request')->thenReturn('response');

        $client = $this->buildClient(null, $httpClient);
        $client->token = new AccessToken(['access_token' => 'access_token']);

        $this->assertEquals('response', $client->request('method', '/path/to/api', ['param' => 1]));
    }

    public function test_request_before_authorized()
    {
        $this->setExpectedException('\Quartet\BaseApi\Exception\RuntimeException');

        $client = $this->buildClient();
        $client->request('method', '/path/to/api');
    }

    /**
     * @dataProvider responseExceptionProvider
     */
    public function test_request_with_http_errors($idx, $exception)
    {
        // mock http client.
        $httpClient = Phake::mock('\Guzzle\Http\Client');
        Phake::when($httpClient)->createRequest(Phake::anyParameters())->thenReturn('request');
        Phake::when($httpClient)->send('request')->thenThrow($exception);

        $client = $this->buildClient(null, $httpClient);
        $client->token = new AccessToken(['access_token' => 'access_token']);

        switch ($idx) {
            case 0:
                $this->setExpectedException('\Quartet\BaseApi\Exception\RateLimitExceededException');
                $client->request('method', '/path/to/api');
                break;
            case 1:
                $this->setExpectedException('\Quartet\BaseApi\Exception\BaseApiErrorResponseException');
                $client->request('method', '/path/to/api');
                break;
        }
    }

    /**
     * data provider for test_request_with_http_errors
     */
    public function responseExceptionProvider()
    {
        $messages = [
            0 => Client::RATE_LIMIT_EXCEEDED_MESSAGE,
            1 => 'default exception',
        ];

        $data = [];

        foreach ($messages as $idx => $message) {
            $responseException = $this->getResponseException([
                'error' => 'error',
                'error_description' => $message,
            ]);

            $data[] = [$idx, $responseException];
        }

        return $data;
    }

    public function test_authorize()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');

        $client = $this->buildClient($provider);

        $client->authorize(['param' => 1]);
        Phake::verify($provider)->authorize(['param' => 1]);
    }

    public function test_getAuthorizationUrl()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');

        $client = $this->buildClient($provider);

        $client->getAuthorizationUrl(['param' => 1]);
        Phake::verify($provider)->getAuthorizationUrl(['param' => 1]);
    }

    public function test_authenticate()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');
        Phake::when($provider)->getAccessToken(Phake::anyParameters())->thenReturn('token');

        $client = $this->buildClient($provider);

        $this->assertNull($client->token);

        $client->authenticate('test_code');
        Phake::verify($provider)->getAccessToken('authorization_code', ['code' => 'test_code']);

        $this->assertEquals('token', $client->token);
    }

    public function test_refresh()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');

        $client = $this->buildClient($provider);
        $client->token = new AccessToken([
            'access_token' => '',
            'refresh_token' => 'test_refresh_token'
        ]);

        $client->refresh();
        Phake::verify($provider)->getAccessToken('refresh_token', ['refresh_token' => 'test_refresh_token']);
    }

    public function test_refresh_with_http_error()
    {
        $exception = $this->getResponseException();

        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');
        Phake::when($provider)->getAccessToken(Phake::anyParameters())->thenThrow($exception);

        $client = $this->buildClient($provider);
        $client->token = new AccessToken([
            'access_token' => '',
            'refresh_token' => 'test_refresh_token'
        ]);

        $this->setExpectedException('\Quartet\BaseApi\Exception\AccessTokenExpiredException');
        $client->refresh();
    }

    private function getResponseException(array $responseBody = ['error' => 'error', 'error_description' => 'error_description'])
    {
        // mock response.
        $response = Phake::mock('\Guzzle\Http\Message\Response');
        Phake::when($response)->getBody()->thenReturn(json_encode($responseBody));

        // mock response exception.
        $responseException = Phake::mock('\Guzzle\Http\Exception\BadResponseException');
        Phake::when($responseException)->getResponse()->thenReturn($response);

        return $responseException;
    }
}
