<?php

namespace Zbxin\ApiTokenAuth\Facades;

use Illuminate\Support\Facades\Facade;
use Zbxin\ApiTokenAuth\ApiTokenManager;
use Zbxin\ApiTokenAuth\Token;

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
