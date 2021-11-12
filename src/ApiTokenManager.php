<?php

namespace Zbxin\ApiTokenAuth;

class ApiTokenManager
{
    /**
     * @var \Illuminate\Config\Repository
     */

    protected $config;

    /**
     * @var Token[]
     */

    protected $tokens = [];

    /**
     * ApiTokenManager constructor.
     * @param $config
     */

    public function __construct($config)
    {
        $this->config = $config;
        foreach ($this->config->get('api-token.tokens') as $name => $config) {
            $this->tokens[$name] = new Token($config);
        }
    }

    /**
     * @param $token
     * @return null|Token
     */

    public function get($token = null)
    {
        if ($token === null) {
            return $this->tokens[$this->config->get('api-token.default')];
        }
        return isset($this->tokens[$token]) ? $this->tokens[$token] : null;
    }
}

