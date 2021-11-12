<?php

namespace Zbxin\ApiTokenAuth;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class Token
{
    protected $headerKey;

    protected $cacheKey;

    protected $expired;

    protected $autoRefresh;

    protected $redisCon;

    private $_token;

    /**
     * ApiToken constructor.
     * @param $config
     */

    public function __construct($config)
    {
        if (!isset($config['cache']) || empty($config['cache'])) {
            throw new \RuntimeException('config key "cache" can\'t empty.');
        }
        if (!isset($config['header']) || empty($config['header'])) {
            throw new \RuntimeException('config header "cache" can\'t empty.');
        }
        $this->headerKey = $config['header'];
        $this->cacheKey = $config['cache'];
        $this->expired = isset($config['expired']) ? $config['expired'] : 120;
        $this->autoRefresh = isset($config['refresh']) ? $config['refresh'] : true;
        $this->redisCon = isset($config['redis']) ? $config['redis'] : 'cache';
    }

    /**
     * @return array|string
     */

    public function getToken()
    {
        if ($this->_token === null) {
            $this->_token = Request::instance()->header($this->headerKey);
        }
        return $this->_token;
    }

    /**
     * @return null
     */

    protected function getCode()
    {
        try {
            $cacheCode = Redis::connection($this->redisCon)->get($this->redisCacheKey());
            return empty($cacheCode) ? null : unserialize($cacheCode);
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return null
     */

    public function getUserCode()
    {
        try {
            $userCode = $this->getCode();
            $this->autoRefresh === true && $userCode !== null && $this->cacheUserCode($userCode);
            return $userCode;
        } catch (\Exception $exception) {
            logs()->error($exception);
            return null;
        }
    }

    /**
     *
     */

    public function clearUserCode()
    {
        try {
            $this->getCode() && Redis::connection($this->redisCon)->forget($this->redisCacheKey());
        } catch (\Exception $exception) {
            logs()->error($exception);
        }
    }

    /**
     *
     */

    protected function generateToken()
    {
        $this->_token = Str::random(40);
        return $this;
    }

    /**
     * @param $userCode
     */

    public function setUserCode($userCode)
    {
        try {
            $this->generateToken()->cacheUserCode($userCode);
        } catch (\Exception $exception) {
            logs()->error($exception);
        }
    }

    /**
     * @param $userCode
     * @return bool
     */

    protected function cacheUserCode($userCode)
    {
        try {
            Redis::connection($this->redisCon)->set($this->redisCacheKey(), serialize($userCode), 'EX', $this->expired * 60);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @return string
     */

    protected function redisCacheKey()
    {
        return config('cache.prefix') . ':' . $this->cacheKey . $this->getToken();
    }
}
