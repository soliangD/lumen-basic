<?php

namespace Common\Helpers\Utils;

class UrlHelper
{
    public static function getFullUrl($url, array $extraParams = [], $coverPath = null)
    {
        $parseUrl = parse_url($url);
        // 兼容 http:// or https:// 不存在的情况
        if (!isset($parseUrl['host']) && isset($parseUrl['path'])) {
            $explode = explode('/', $parseUrl['path'], 2);
            $parseUrl['host'] = $explode[0];
            $parseUrl['path'] = isset($explode[1]) ? '/' . $explode[1] : '';
        }
        $host = $parseUrl['host'];

        if ($coverPath) {
            $parseUrl['path'] = str_start($coverPath, '/');
        }

        if (isset($parseUrl['scheme']) && strtolower($parseUrl['scheme']) == 'https') {
            $host = str_start($host, 'https://');
        } else {
            $host = str_start($host, 'http://');
        }

        $oldParams = [];
        if (isset($parseUrl['path'])) {
            $host .= $parseUrl['path'];
        }
        if (isset($parseUrl['query'])) {
            $oldParams = UrlHelper::convertUrlQuery($parseUrl['query']);
        }
        $params = array_merge($oldParams, $extraParams);
        $params && $host .= '?' . http_build_query($params);

        return $host;
    }

    public static function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);

        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }

        return $params;
    }
}
