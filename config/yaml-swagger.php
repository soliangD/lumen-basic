<?php

return [

    'default' => 'api',

    'module' => [
        'admin' => [
            'api' => [
                'title' => 'Swagger Lume Admin Doc',
            ],

            'routes' => [
                'api' => '/documentation',
                'docs' => '/docs',
                'oauth2_callback' => '/oauth2-callback',
                'assets' => '/swagger-ui-assets',
                'middleware' => [
                    'api' => ['apiDoc'], // 添加中间件验证访问权限
                    'asset' => [],
                    'docs' => [],
                    'oauth2_callback' => [],
                ],
            ],

            'paths' => [
                'docs' => storage_path('admin-docs'),
                'docs_json' => 'admin-docs.json',
                'annotations' => base_path('app/admin'),
                'excludes' => [],
                'base' => env('L5_SWAGGER_BASE_PATH', null),
                'views' => base_path('resources/views/vendor/swagger-lume'),
                'yaml_annotations' => base_path('app/admin'),
            ],
        ],
        'api' => [
            'api' => [
                'title' => 'Swagger Lume Api Doc',
            ],

            'routes' => [
                'api' => '/documentation',
                'docs' => '/docs',
                'oauth2_callback' => '/oauth2-callback',
                'assets' => '/swagger-ui-assets',
                'middleware' => [
                    'api' => ['apiDoc'], // 添加中间件验证访问权限
                    'asset' => [],
                    'docs' => [],
                    'oauth2_callback' => [],
                ],
            ],

            'paths' => [
                'docs' => storage_path('api-docs'),
                'docs_json' => 'api-docs.json',
                'annotations' => base_path('app/api'),
                'excludes' => [],
                'base' => env('L5_SWAGGER_BASE_PATH', null),
                'views' => base_path('resources/views/vendor/swagger-lume'),
                'yaml_annotations' => base_path('app/api'),
            ],
        ]
    ],

    'common' => [
    ],
];
