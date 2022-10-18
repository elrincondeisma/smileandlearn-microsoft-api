<?php


namespace Smileanlearn\Microsoft;

use Smileanlearn\Microsoft\Handlers\mSession;


class mAuth  {
    protected $host = "https://login.microsoftonline.com/";
    protected $resource = "https://graph.microsoft.com/";
    protected $tenant_id;
    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;
    protected $scopes;
    protected $guzzle;
    protected $refreshToken;
    public function __construct(string $tenant_id, string $client_id, string $client_secret, string $redirect_uri, array $scopes = [])
    {
        $this->tenant_id = $tenant_id;
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        $this->scopes = $scopes;
        mSession::set("host", $this->host);
        mSession::set("resource", $this->resource);
        mSession::set("tenant_id", $tenant_id);
        mSession::set("client_id", $client_id);
        mSession::set("client_secret", $client_secret);
        mSession::set("redirect_uri", $redirect_uri);
        mSession::set("scopes", $scopes);
        if (!mSession::get('state')) {
            mSession::set("state", random_int(1, 200000));
        }
        $this->guzzle = new \GuzzleHttp\Client();
    }
    public function setRefreshToken(string $refreshToken) 
    {
        $this->refreshToken = $refreshToken;
        mSession::set("refreshToken", $this->refreshToken);
        return mSession::get("refreshToken");
    }
    public function getAccessTokenUsingRefreshToken(string $refreshToken = null)
    {
        if ($refreshToken) {
            $this->setRefreshToken($refreshToken);
        }
        $url = $this->host. $this->tenant_id ."/oauth2/v2.0/token";
        $tokens = $this->guzzle->post($url, [
            'form_params' => [
                'client_id' => mSession::get("client_id"),
                'client_secret' => mSession::get("client_secret"),
                'grant_type' => 'refresh_token',
                'refresh_token' => mSession::get("refreshToken")
            ],
        ])->getBody()->getContents();
        return json_decode($tokens)->access_token;
    }
    public function setAccessToken(string $accessToken = null)
    {
        if (!$accessToken) {
            $this->accessToken = $this->getAccessTokenUsingRefreshToken();
        } else {
            $this->accessToken = trim($accessToken);
        }
        mSession::set("accessToken", $this->accessToken);
        return mSession::get("accessToken");
    }
    public function getAuthUrl()
    {
        $parameters = [
            'client_id' => $this->client_id,
            'response_type' => 'code',
            'redirect_uri' => $this->redirect_uri,
            'response_mode' => 'query',
            'scope' => implode(' ', $this->scopes),
            'state' => mSession::get("state")
        ];
        return $this->host . $this->tenant_id . "/oauth2/v2.0/authorize?". http_build_query($parameters);
    }
    public function getToken(string $code, string $state = null)
    {
        
        $url = $this->host. $this->tenant_id ."/oauth2/v2.0/token";
        $tokens = $this->guzzle->post($url, [
            'form_params' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri' => $this->redirect_uri,
                'scope' => implode(' ', $this->scopes),
                'grant_type' => 'authorization_code',
                'code' => $code
            ],
        ])->getBody()->getContents();
        return json_decode($tokens);
    }
}
