<?php

namespace ZhiEq\ApiTokenAuth;

use Carbon\Carbon;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Token
{
  protected $headerKey;

  protected $cacheKey;

  protected $expired;

  protected $autoRefresh;

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
      return cache()->tags($this->cacheKey)->get($this->getToken());
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
      if ($this->autoRefresh === true && $userCode !== null) {
        cache()->tags($this->cacheKey)->put($this->getToken(), $userCode, Carbon::now()->addMinutes($this->expired));
      }
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
      $this->getCode() && cache()->tags($this->cacheKey)->forget($this->getToken());
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
  }

  /**
   * @param $userCode
   */

  public function setUserCode($userCode)
  {
    try {
      $this->generateToken();
      cache()->tags($this->cacheKey)->add($this->getToken(), $userCode, $this->expired);
    } catch (\Exception $exception) {
      logs()->error($exception);
    }
  }
}
