<?php
/*
 * Set specific configuration variables here
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    | Avatar use Intervention Image library to process image.
    | Meanwhile, Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */
    'driver' => 'gd',

    // Initial generator class
    'generator' => \Laravolt\Avatar\Generator\DefaultGenerator::class,

    // Whether all characters supplied must be replaced with their closest ASCII counterparts
    'ascii' => false,

    // Image shape: circle or square
    'shape' => 'circle',

    // Image width, in pixel
    'width' => 300,

    // Image height, in pixel
    'height' => 300,

    // Number of characters used as initials. If name consists of single word, the first N character will be used
    'chars' => 2,

    // font size
    'fontSize' => 120,

    // convert initial letter in uppercase
    'uppercase' => false,

    // Fonts used to render text.
    // If contains more than one fonts, randomly selected based on name supplied
    // 配置多个时，根据 文字 随机匹配字体
    'fonts' => [base_path('resources/fonts/jiashuti.ttf')],

    // List of foreground colors to be used, randomly selected based on name supplied
    'foregrounds' => [
        '#FFFFFF',
    ],

    // List of background colors to be used, randomly selected based on name supplied
    'backgrounds' => [
        '#f44336',
        '#E91E63',
        '#9C27B0',
        '#673AB7',
        '#3F51B5',
        '#2196F3',
        '#03A9F4',
        '#00BCD4',
        '#009688',
        '#4CAF50',
        '#8BC34A',
        '#CDDC39',
        '#FFC107',
        '#FF9800',
        '#FF5722',
    ],

    'border' => [
        'size' => 1,

        // border color, available value are:
        // 'foreground' (same as foreground color)
        // 'background' (same as background color)
        // or any valid hex ('#aabbcc')
        'color' => 'background',
    ],
];
