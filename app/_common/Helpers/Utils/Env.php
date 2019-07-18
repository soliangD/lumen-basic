<?php

namespace Common\Helpers\Utils;

class Env
{
    /**
     * @var array 缓存数据
     */
    private static $cache = [];

    /**
     * (开发环境、本地、测试用例) || 测试环境
     * @return bool
     */
    public static function isDevOrTest()
    {
        return self::isDev() || self::isTest();
    }

    /**
     * 测试用例
     * @return bool
     */
    public static function isTesting()
    {
        return self::checkEnv(__FUNCTION__, ['testing']);
    }

    /**
     * 开发环境、本地、测试用例
     * @return bool
     */
    public static function isDev()
    {
        return self::checkEnv(__FUNCTION__, ['dev', 'local', 'testing']);
    }

    /**
     * 内部封装方法
     * @param string $name
     * @param array|string $env
     * @return bool
     */
    private static function checkEnv($name, $env): bool
    {
        if (!isset(self::$cache[$name])) {
            self::$cache[$name] = app()->environment($env);
        }
        return self::$cache[$name];
    }

    /**
     * 测试环境
     * @return bool
     */
    public static function isTest()
    {
        return self::checkEnv(__FUNCTION__, ['test']);
    }

    /**
     * 生产环境
     * @return bool
     */
    public static function isProd()
    {
        return self::checkEnv(__FUNCTION__, ['production', 'prod']);
    }

    /**
     * 预发布
     * @return bool
     */
    public static function isPre()
    {
        return self::checkEnv(__FUNCTION__, ['pre', 'staging']);
    }

    /**
     * 清空缓存
     * @return void
     */
    public static function clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * 获取当前环境
     * @return string
     */
    public static function env()
    {
        return app()->environment();
    }
}
