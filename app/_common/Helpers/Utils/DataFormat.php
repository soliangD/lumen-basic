<?php

namespace Common\Helpers\Utils;

class DataFormat
{
    const RESULT_SUCCESS = 18000;

    const RESULT_ERROR = 13000;

    /**
     * @var int $code
     */
    protected $code;

    /**
     * @var string $msg
     */
    protected $msg = '';

    /**
     * @var mixed $data
     */
    protected $data;

    /**
     * DataFormat constructor.
     * @param $code
     * @param string $msg
     * @param mixed $data
     */
    public function __construct(int $code, string $msg = '', $data = [])
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }

    /**
     * result success
     * @param mixed $data
     * @param string $msg
     * @return DataFormat
     */
    public static function resultSuccess($data = [], string $msg = '')
    {
        return self::result(self::RESULT_SUCCESS, $msg, $data);
    }

    /**
     * result
     * @param $code
     * @param string $msg
     * @param mixed $data
     * @return DataFormat
     */
    public static function result(int $code, string $msg = '', $data = [])
    {
        return new static($code, $msg, $data);
    }

    /**
     * result fail
     * @param string $msg
     * @param mixed $data
     * @return DataFormat
     */
    public static function resultFail(string $msg = '', $data = [])
    {
        return self::result(self::RESULT_ERROR, $msg, $data);
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getCode() == self::RESULT_SUCCESS;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code ?? self::RESULT_ERROR;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->getCode() == self::RESULT_ERROR;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return array|mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * result format as array
     * @return array
     */
    public function formatArray()
    {
        return [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data,
        ];
    }

    /**
     * @param $field
     * @return mixed|string
     */
    public function getDataField($field)
    {
        $data = $this->data;

        if (!is_array($data)) {
            return '';
        }

        if (!array_key_exists($field, $data)) {
            return '';
        }

        return $data[$field];
    }
}
