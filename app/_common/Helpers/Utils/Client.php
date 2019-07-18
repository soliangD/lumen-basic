<?php

namespace Common\Helpers\Utils;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_PATCH = 'PATCH';
    const METHOD_PUT = 'PUT';

    const CONTENT_TYPE_FORM = 'form_params';
    const CONTENT_TYPE_JSON = 'json';
    const CONTENT_TYPE_MULTIPART = 'multipart';
    const CONTENT_TYPE_QUERY = 'query';

    /**
     *
     * @param string $url
     * @param array $params
     * @param string $method get|delete|head|options|patch|post|put
     * @param array $configs client config
     * @param bool $decode 结果是否进行 json_decode
     * @param string $contentType 报文头类型
     * @param array $fileParams 附带文件
     *
     * @return DataFormat
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function request(string $url, $params = [], string $method = self::METHOD_POST, array $configs = [], $decode = true, array $fileParams = [], string $contentType = null)
    {
        $configs['timeout'] = $configs['timeout'] ?? 60;
        $client = new BaseClient($configs);
        $options = isset($contentType) ? [$contentType => $params]
            : (strtoupper($method) == self::METHOD_GET ? [self::CONTENT_TYPE_QUERY => $params] : [self::CONTENT_TYPE_FORM => $params]);

        // 禁用SSL证书验证，暂不需要
//        if (strpos($url, 'https://') === 0) {
//            $options['verify'] = false;
//        }

        if ($fileParams) {
            $options = [];
            foreach ($fileParams as $k => $v) {
                if (!file_exists($v)) {
                    return DataFormat::resultFail("File does not exist({$v})");
                }
                $options[self::CONTENT_TYPE_MULTIPART][] = ['name' => $k, 'contents' => fopen($v, 'r'), 'filename' => basename($v)];
            }
            foreach ($params as $k => $v) {
                $options[self::CONTENT_TYPE_MULTIPART][] = ['name' => $k, 'contents' => $v];
            }
        }

        try {
            $request = $client->request($method, $url, $options);
        } catch (RequestException $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            return DataFormat::result($errorCode, $errorMessage);
        }

        $httpCode = $request->getStatusCode();
        $response = $request->getBody()->getContents();
        $decode && $response = json_decode($response, true);

        if ($httpCode != 200) {
            return DataFormat::result($httpCode);
        }

        return DataFormat::resultSuccess($response, '请求成功');
    }

    /**
     * send get request
     * @param $url
     * @param $params
     * @param array $headers
     * @param array $configs
     * @param bool $decode
     * @return DataFormat
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function get($url, $params, $headers = [], $configs = [], $decode = true)
    {
        $configs = array_merge($configs, [
            'headers' => $headers,
        ]);
        return self::request($url, $params, self::METHOD_GET, $configs, $decode);
    }

    /**
     * send post request
     * @param $url
     * @param $params
     * @param array $headers
     * @param array $configs
     * @param bool $decode
     * @return DataFormat
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function post($url, $params, $headers = [], $configs = [], $decode = true)
    {
        $configs = array_merge($configs, [
            'headers' => $headers,
        ]);
        return self::request($url, $params, self::METHOD_POST, $configs, $decode);
    }

    /**
     * 使用 json 数据当主体
     * Content-Type 设置为 application/json
     * @param $url
     * @param $params
     * @param string $method
     * @param array $headers
     * @param array $fileParams
     * @param array $configs
     * @param bool $decode
     * @return DataFormat
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function jsonRequest($url, $params, $method = self::METHOD_POST, $headers = [], array $fileParams = [], $configs = [], $decode = true)
    {
        $configs = array_merge($configs, [
            'headers' => $headers,
        ]);
        return self::request($url, $params, $method, $configs, $decode, $fileParams, self::CONTENT_TYPE_JSON);
    }

    /**
     * 附带文件
     * @param $url
     * @param array $fileParams
     * @param array $params
     * @param array $headers
     * @param array $configs
     * @param bool $decode
     * @return DataFormat
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function attachFile($url, $fileParams = [], $params = [], $headers = [], $configs = [], $decode = true)
    {
        $configs = array_merge($configs, [
            'headers' => $headers,
        ]);
        return self::request($url, $params, self::METHOD_POST, $configs, $decode, $fileParams);
    }

//    /**
//     * curl post
//     * @param $url
//     * @param $params
//     * @param array $fileParams
//     * @param array $header
//     * @param int $timeout
//     * @return mixed
//     */
//    public static function curlPost($url, $params, $fileParams = [], $header = [], $timeout = 60)
//    {
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_HEADER, false);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POST, true);
//        foreach ($fileParams as $key => $path) {
//            $params[$key] = new \CURLFile($path);
//        }
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//        if ($header) {
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//        }
//        $output = curl_exec($ch);
//        curl_close($ch);
//        return $output;
//    }

//    /**
//     * @param string $method
//     * @param array $parameters
//     * @return mixed
//     */
//    public function __call($method, $parameters)
//    {
//        switch ($method) {
//            case self::METHOD_POST:
//            case self::METHOD_GET:
//            case self::METHOD_DELETE:
//            case self::METHOD_HEAD:
//            case self::METHOD_OPTIONS:
//            case self::METHOD_PATCH:
//            case self::METHOD_PUT:
//        }
//
//        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $method . '()');
//    }
//
//    /**
//     * @param string $method
//     * @param array $parameters
//     * @return mixed
//     */
//    public static function __callStatic($method, $parameters)
//    {
//        return (new static())->
//
//        //throw new \BadMethodCallException('Call to undefined method ' . static::class . '::' . $method . '()');
//    }
}
