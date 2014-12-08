<?php
namespace Quartet\BaseApi;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\Exception\BadResponseException;
use League\OAuth2\Client\Provider\ProviderInterface;
use League\OAuth2\Client\Token\AccessToken;
use Quartet\BaseApi\Exception\BaseApiException;
use Quartet\BaseApi\Exception\RuntimeException;
use Quartet\BaseApi\Provider\Base;

class Client
{
    /**
     * @var \League\OAuth2\Client\Token\AccessToken
     */
    public $token;

    /**
     * @var \League\OAuth2\Client\Provider\ProviderInterface
     */
    private $provider;

    /**
     * @var \Guzzle\Http\Client
     */
    private $httpClient;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param array $scopes
     */
    public function __construct($clientId, $clientSecret, $redirectUri, $scopes = [])
    {
        $this->provider = new Base([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $redirectUri,
            'scopes' => $scopes,
        ]);

        $this->httpClient = new HttpClient(Base::BASE_URL);
    }

    /**
     * @param $method
     * @param $relativeUrl
     * @param array $params
     * @return array|\Guzzle\Http\Message\Response|mixed|null
     * @throws Exception\RuntimeException
     * @throws Exception\BaseApiException
     */
    public function request($method, $relativeUrl, array $params = [])
    {
        if (! $this->token instanceof AccessToken) {
            throw new RuntimeException('Not authorized yet.');
        }

        $request = $this->httpClient->createRequest($method, $relativeUrl, ['Authorization' => "Bearer {$this->token->accessToken}"], null, $params);

        try {
            $response = $this->httpClient->send($request);
        } catch (BadResponseException $e) {
            $response = json_decode($e->getResponse()->getBody(), true);
            throw new BaseApiException($response, $e->getResponse()->getStatusCode());
        }

        return $response;
    }

    /**
     * Redirect to authorization url.
     *
     * @param array $params
     */
    public function authorize(array $params = [])
    {
        $this->provider->authorize($params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getAuthorizationUrl(array $params = [])
    {
        return $this->provider->getAuthorizationUrl($params);
    }

    /**
     * @param string $code
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function authenticate($code)
    {
        $this->token = $this->provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        return $this->token;
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function refresh()
    {
        return $this->token = $this->provider->getAccessToken('refresh_token', [
            'refresh_token' => $this->token->refreshToken,
        ]);
    }

    /**
     * @param ProviderInterface $provider
     * @return $this
     */
    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @param HttpClient $httpClient
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;

        return $this;
    }
}
