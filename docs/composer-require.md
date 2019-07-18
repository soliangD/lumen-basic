## 基础包

### require

自带
- php
- laravel/lumen-framework
- vlucas/phpdotenv：用于加载 .env 文件

新增(common)
- laravel/helpers：5.8去除helper方法，封装为了helpers
- guzzlehttp/guzzle：PHP的HTTP客户端
- illuminate/mail：mail
- illuminate/redis
- predis/predis：redis
新增(other)
- league/flysystem：filesystem需要
- laravolt/avatar：生成文字头像
- tymon/jwt-auth(1.0.0-rc.4)：jwt auth

### require-dev

自带
- fzaninotto/faker：生成模拟数据
- phpunit/phpunit
- mockery/mockery

新增
- barryvdh/laravel-ide-helper：ide helper //见[详解](https://learnku.com/articles/10172/laravel-super-good-code-prompt-tool-laravel-ide-helper)
- laravelista/lumen-vendor-publish：支持 artisan vendor:publish 命令(已废弃?待定)
- soliangd/lumen-yaml-swagger：swagger-yaml 文档
