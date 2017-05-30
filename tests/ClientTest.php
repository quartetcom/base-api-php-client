<?php
namespace Quartet\BaseApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Phake;
use Psr\Http\Message\ResponseInterface;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    private function buildClient(AbstractProvider $provider = null, ClientInterface $httpClient = null)
    {
        return new Client('', '', '', [], $provider, $httpClient);
    }

    public function test_request()
    {
        // mock http response.
        $response = Phake::mock(ResponseInterface::class);
        Phake::when($response)->getBody()->thenReturn(json_encode(['json' => 'test']));

        // mock http client.
        $httpClient = Phake::mock(ClientInterface::class);
        Phake::when($httpClient)->request('method', '/path/to/api', [
            'headers' => ['Authorization' => 'Bearer access_token'],
            'form_params' => ['param' => 1],
            ])->thenReturn($response);

        $client = $this->buildClient(null, $httpClient);
        $client->token = new AccessToken(['access_token' => 'access_token']);

        $this->assertEquals(['json' => 'test'], $client->request('method', '/path/to/api', ['param' => 1]));
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
        // mock http response.
        $response = Phake::mock(ResponseInterface::class);
        Phake::when($response)->getBody()->thenReturn(json_encode(['json' => 'test']));

        // mock http client.
        $httpClient = Phake::mock(ClientInterface::class);
        Phake::when($httpClient)->request(Phake::anyParameters())->thenThrow($exception);

        $token = new AccessToken([
            'access_token' => 'access token',
            'refresh_token' => 'refresh token',
        ]);

        // mock oauth2 provider.
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');
        Phake::when($provider)->getAccessToken(Phake::anyParameters())->thenReturn($token);

        $client = $this->buildClient($provider, $httpClient);
        $client->token = $token;

        switch ($idx) {
            case 0:
                Phake::when($httpClient)->request(Phake::anyParameters())->thenReturn($response);
                $data = $client->request('method', '/path/to/api');
                $this->assertEquals(['json' => 'test'], $data);
                break;
            case 1:
                $this->setExpectedException('\Quartet\BaseApi\Exception\RateLimitExceededException');
                $client->request('method', '/path/to/api');
                break;
            case 2:
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
            0 => Client::ACCESS_TOKEN_EXPIRED_MESSAGE,
            1 => Client::RATE_LIMIT_EXCEEDED_MESSAGE,
            2 => 'default exception',
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

        $client->authenticate('test code');
        Phake::verify($provider)->getAccessToken('authorization_code', ['code' => 'test code']);

        $this->assertEquals('token', $client->token);
    }

    public function test_refresh()
    {
        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');

        $client = $this->buildClient($provider);
        $client->token = new AccessToken([
            'access_token' => 'test access token',
            'refresh_token' => 'test refresh token',
        ]);

        $client->refresh();
        Phake::verify($provider)->getAccessToken('refresh_token', ['refresh_token' => 'test refresh token']);
    }

    public function test_refresh_with_http_error()
    {
        $exception = $this->getResponseException();

        $provider = Phake::mock('\Quartet\BaseApi\Provider\Base');
        Phake::when($provider)->getAccessToken(Phake::anyParameters())->thenThrow($exception);

        $client = $this->buildClient($provider);
        $client->token = new AccessToken([
            'access_token' => 'test access token',
            'refresh_token' => 'test refresh token',
        ]);

        $this->setExpectedException('\Quartet\BaseApi\Exception\AccessTokenExpiredException');
        $client->refresh();
    }

    private function getResponseException(array $responseBody = ['error' => 'error', 'error_description' => 'error description'])
    {
        // mock response.
        $response = Phake::mock(ResponseInterface::class);
        Phake::when($response)->getBody()->thenReturn(json_encode($responseBody));

        // mock response exception.
        $responseException = Phake::mock(RequestException::class);
        Phake::when($responseException)->getResponse()->thenReturn($response);

        return $responseException;
    }
}
