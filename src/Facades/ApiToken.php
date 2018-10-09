<?php

namespace ZhiEq\ApiTokenAuth\Facades;

use Illuminate\Support\Facades\Facade;
use ZhiEq\ApiTokenAuth\ApiTokenManager;
use ZhiEq\ApiTokenAuth\Token;

/**
 * @method static Token|null get($token = null)
 *
 * @see ApiTokenManager
 */
class ApiToken extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'api-token';
    }
}
