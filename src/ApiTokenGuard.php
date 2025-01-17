<?php

namespace Zbxin\ApiTokenAuth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Zbxin\ApiTokenAuth\Facades\ApiToken;

class ApiTokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var Token
     */

    protected $apiToken;

    /**
     * ApiTokenGuard constructor.
     * @param $provider
     * @param $config
     */

    public function __construct($provider, $config)
    {
        $this->setProvider($provider);
        $tokenName = isset($config['token']) ? $config['token'] : null;
        $this->apiToken = ApiToken::get($tokenName);
    }


    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!$userCode = $this->apiToken->getUserCode()) {
            return null;
        }
        return $this->provider->retrieveById($userCode);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return false;
    }

}
