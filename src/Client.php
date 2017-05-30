<?php
namespace Quartet\BaseApi;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\BadResponseException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Quartet\BaseApi\Exception\AccessTokenExpiredException;
use Quartet\BaseApi\Exception\BaseApiErrorResponseException;
use Quartet\BaseApi\Exception\RateLimitExceededException;
use Quartet\BaseApi\Exception\RuntimeException;
use Quartet\BaseApi\Provider\Base;

class Client
{
    const ACCESS_TOKEN_EXPIRED_MESSAGE = 'アクセストークンが無効です。';
    const REFRESH_TOKEN_EXPIRED_MESSAGE = 'リフレッシュトークンの有効期限が切れています。';
    const RATE_LIMIT_EXCEEDED_MESSAGE = '1日のAPIの利用上限を超えました。日付が変わってからもう一度アクセスしてください。';

    /**
     * @var AccessToken
     */
    public $token;

    /**
     * @var AbstractProvider
     */
    private $provider;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param array $scopes
     * @param AbstractProvider $provider
     * @param ClientInterface $httpClient
     */
    public function __construct($clientId, $clientSecret, $redirectUri, $scopes = [], AbstractProvider $provider = null, ClientInterface $httpClient = null)
    {
        if (is_null($provider)) {
            $this->provider = new Base([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUri,
                'scopes' => $scopes,
            ]);
        } else {
            $this->provider = $provider;
        }

        if (is_null($httpClient)) {
            $this->httpClient = new HttpClient();
        } else {
            $this->httpClient = $httpClient;
        }
        $this->httpClient->setBaseUrl(Base::BASE_URL);
    }

    /**
     * @param string $method
     * @param string $relativeUrl
     * @param array $params
     * @return array
     * @throws Exception\BaseApiErrorResponseException
     * @throws Exception\RuntimeException
     * @throws Exception\RateLimitExceededException
     */
    public function request($method, $relativeUrl, array $params = [])
    {
        if (! $this->token instanceof AccessToken) {
            throw new RuntimeException('Not authorized yet.');
        }

        $request = $this->httpClient->createRequest($method, $relativeUrl, ['Authorization' => "Bearer {$this->token->getToken()}"], null, $params);

        try {
            $response = $this->httpClient->send($request);
            $body = json_decode($response->getBody(), true) or [];

            return $body;

        } catch (BadResponseException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);

            switch ($body['error_description']) {
                case self::ACCESS_TOKEN_EXPIRED_MESSAGE:
                    $this->refresh();
                    return $this->request($method, $relativeUrl, $params);

                case self::RATE_LIMIT_EXCEEDED_MESSAGE:
                    throw new RateLimitExceededException(self::RATE_LIMIT_EXCEEDED_MESSAGE);

                default:
                    throw new BaseApiErrorResponseException($body, $e->getResponse()->getStatusCode());
            }
        }
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
     * @return AccessToken
     */
    public function authenticate($code)
    {
        $this->token = $this->provider->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        return $this->token;
    }

    /**
     * @return AccessToken
     */
    public function refresh()
    {
        try {
            $this->token = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $this->token->getRefreshToken(),
            ]);
        } catch (BadResponseException $e) {
            throw new AccessTokenExpiredException(self::REFRESH_TOKEN_EXPIRED_MESSAGE);
        }

        return $this->token;
    }
}
