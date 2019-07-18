<?php
/**
 * Created by PhpStorm.
 * User: Windy
 * Date: 2019/1/16
 * Time: 9:32
 */

namespace Common\Traits\Response;


trait Send
{
    private static $successCode = 18000;
    private static $failCode = 13000;

    /**
     * 接口统一成功输出
     * @param array $data
     * @param string $msg
     * @return array
     */
    public function resultSuccess($data = [], $msg = '')
    {
        return $this->result(self::$successCode, $msg, $data);
    }

    /**
     * 接口统一输出
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return array
     */
    public function result($code, $msg = '', $data = [])
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
    }

    /**
     * 接口统一失败输出
     * @param string $msg
     * @param array $data
     * @return array
     */
    public function resultFail($msg = '', $data = [])
    {
        return $this->result(self::$failCode, $msg, $data);
    }

    /**
     * 判断结果 并 对应输出
     * @param $condition
     * @param string $trueMessage
     * @param string $falseMessage
     * @param array $data
     * @return array
     */
    public function resultJudge($condition, $trueMessage = '', $falseMessage = '', $data = [])
    {
        if ($condition === true) {
            $result = $this->resultSuccess($data, $trueMessage);
        } else {
            $result = $this->resultFail($falseMessage, $data);
        }
        return $result;
    }
}
