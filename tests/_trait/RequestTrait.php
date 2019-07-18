<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/20
 * Time: 15:55
 * author: soliang
 */

namespace Tests\_trait;

use Common\Helpers\Utils\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait RequestTrait
{
    /**
     * Visit the given URI with a GET request.
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return $this
     * @throws \Exception
     */
    public function get($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        $this->call('GET', $uri, $data, [], [], $server);

        return $this;
    }

    /**
     * Visit the given URI with a JSON request.
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return $this|\Laravel\Lumen\Testing\TestCase
     * @throws \Exception
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        if ($this->isLocal()) {
            return parent::json($method, $uri, $data, $headers);
        }

        $this->call($method, $uri, $data, $this->transformHeadersToServerVars($headers));

        return $this;
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        if ($this->isLocal()) {
            return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
        }

        if (!filter_var($uri, FILTER_VALIDATE_URL)) {
            $uri = rtrim($this->baseUrl, '/') . str_start($uri, '/');
        }

        $fileParams = [];
        if ($files) {
            /** @var UploadedFile $file */
            foreach ($files as $k => $file) {
                $fileParams[$k] = $file->getPathname();
            }
        }

        // json 需要更换 contentType
        $contentType = null;
        $backtrace = debug_backtrace();
        array_shift($backtrace);
        $lastTrace = array_shift($backtrace);
        if (isset($lastTrace['function']) && $lastTrace['function'] == 'json') {
            $contentType = 'json';
        }

        $configs = [
            'headers' => $this->transformServerVarsToHeaders($server),
        ];

        $result = Client::request($uri, $parameters, $method, $configs, false, $fileParams, $contentType);

        if (!$result->isSuccess()) {
            throw new \Exception('request error');
        }

        return $this->response = response($result->getData(), 200);
    }

    /**
     * 将 $server 数组转为 header 键值数组
     * transformHeadersToServerVars 方法的逆过程
     * @param array $servers
     * @return array
     */
    protected function transformServerVarsToHeaders(array $servers)
    {
        $headers = [];
        $prefix = 'HTTP_';

        foreach ($servers as $name => $value) {
            if (Str::startsWith($name, $prefix)) {
                $name = ltrim($name, $prefix);
            }
            $name = strtr(strtolower($name), '_', '-');
            $headers[$name] = $value;
        }

        return $headers;
    }

    protected function isLocal()
    {
        return $this->baseUrl == 'http://localhost';
    }
}
