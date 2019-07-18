<?php

return [

    /** 开发者 */
    'develops' => explode(';', env('DEVELOPS')),

    /** sql log */
    'app_sql_log' => env('APP_SQL_LOG', false),

    /** api 跨域允许域名 */
    'allow_origin' => explode(',', env('CORS_ALLOW_ORIGIN', '*')),
];
