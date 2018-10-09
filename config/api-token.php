<?php
return [
    //默认认证token
    'default' => 'api',

    /**
     * 认证token类型列表
     */

    'tokens' => [
        'api' => [
            'header' => 'X-Access-Token',
            'cache' => 'access-token'
        ]
    ]
];
