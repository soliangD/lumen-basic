<?php

namespace Common\Services;

use Carbon\Carbon;
use Common\Exceptions\Exception\ApiException;

class BaseService
{
    const OUTPUT_SUCCESS = 18000;
    const OUTPUT_ERROR = 13000;
    protected static $_classServer;
    public $code = self::OUTPUT_SUCCESS;
    public $msg;
    public $data;

    /**
     * @return static
     */
    public static function server()
    {
        $params = func_get_args();
        if ($params) {
            return new static(...$params);
        } else {
            return new static();
        }
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        if ($this->code == self::OUTPUT_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        if ($this->code != self::OUTPUT_SUCCESS) {
            return true;
        }
        return false;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * 当前时间戳
     * @return int
     */
    public function getTime()
    {
        return Carbon::now()->timestamp;
    }

    /**
     * 当前毫秒时间戳
     * @return float|int
     */
    public function getTimeMs()
    {
        return Carbon::now()->timestamp * 1000;
    }

    /**
     * 当前时间
     * 2019-1-21 09:25:18
     * @return string
     */
    public function getDate()
    {
        return Carbon::now()->toDateTimeString();
    }

    /**
     * 逻辑层成功输出
     * @param $msg
     * @param $data
     * @return $this
     */
    protected function outputSuccess($msg = '', $data = '')
    {
        return $this->output(self::OUTPUT_SUCCESS, $msg, $data);
    }

    /**
     * @param $code
     * @param string $msg
     * @param string $data
     * @return $this
     */
    protected function output($code, $msg = '', $data = '')
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
        return $this;
    }

    /**
     * 逻辑层错误输出
     * @param string $msg
     * @param string $data
     * @return $this
     */
    protected function outputError($msg = '', $data = '')
    {
        return $this->output(self::OUTPUT_ERROR, $msg, $data);
    }

    /**
     * return false
     * @param string $msg
     * @return bool
     */
    protected function returnFalse($msg = '')
    {
        $msg && $this->setMsg($msg);
        return false;
    }

    /**
     * 用于方便逻辑层直接抛到客户端
     * 减少控制器层对错误逻辑处理
     * @param string $msg
     * @param int $code
     * @throws ApiException
     */
    protected function outputException($msg = '', $code = self::OUTPUT_ERROR)
    {
        throw new ApiException($msg, $code);
    }

    /**
     * 判断是否导出
     *
     * @return bool
     */
    protected function getExport()
    {
        return (bool)array_get(request()->all(), 'export', 0);
    }
}
