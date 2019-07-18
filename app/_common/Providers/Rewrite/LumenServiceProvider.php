<?php

namespace Common\Providers\Rewrite;

/**
 * Class LumenServiceProvider
 * @package Common\Providers\Rewrite
 * 重写原因： jwt-auth 对 Lumen 5.8.* 没有完全支持
 * ①：黑名单过期时间问题，lumen缓存时间单位已经从分钟改为秒，但是 jwt-auth 没有对此进行优化(只优化了laravel)
 */
class LumenServiceProvider extends \Tymon\JWTAuth\Providers\LumenServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected function registerStorageProvider()
    {
        $this->app->singleton('tymon.jwt.provider.storage', function () {
            $instance = $this->getConfigInstance('providers.storage');

            if (method_exists($instance, 'setLaravelVersion')) {
                $version = $this->app->version();
                preg_match('/\((.*?)\)/i', $version, $match);
                $instance->setLaravelVersion($match[1]);
            }

            return $instance;
        });
    }
}
