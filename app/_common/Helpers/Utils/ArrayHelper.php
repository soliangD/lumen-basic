<?php

namespace Common\Helpers\Utils;

use Common\Helpers\BaseHelpers;

class ArrayHelper extends BaseHelpers
{
    /**
     * 多维键值转前端二维
     * @param $arrs
     * @param string $keyName
     * @param string $valName
     * @return array
     */
    public static function arrsToOption($arrs, $keyName = 'value', $valName = 'label')
    {
        $data = [];
        foreach ($arrs as $key => $arr) {
            $data[$key] = self::arrToOption($arr, $keyName, $valName);
        }
        return $data;
    }

    /**
     * 键值转前端二维
     * @param $arr
     * @param string $keyName
     * @param string $valName
     * @return array
     */
    public static function arrToOption($arr, $keyName = 'value', $valName = 'label')
    {
        $data = [];
        foreach ($arr as $key => $val) {
            $data[] = [
                $keyName => $key,
                $valName => $val,
            ];
        }
        return $data;
    }

    /**
     * 把数组指定值作为数组key
     * @param $arr
     * @param $key
     * @return array
     */
    public static function arrayChangeKey($arr, $key)
    {
        $processedArr = array();
        if (is_array($arr) && !empty($arr)) {
            foreach ($arr as $item) {
                $processedArr[$item[$key]] = $item;
            }
        }
        return $processedArr;
    }

    /**
     * 值改为键值（键为值）
     * @param $arr
     * @return array
     */
    public static function valToKeyVal($arr)
    {
        $data = [];
        foreach ($arr as $val) {
            $data[$val] = $val;
        }
        return $data;
    }

    /**
     * 判断数组每个键对应的值都不为空
     * @param array $arr
     * @return bool
     */
    static function allValuesNotEmpty(array $arr)
    {
        return count($arr) == count(array_filter($arr));
    }

    /**
     * 将数组转字符串
     * @param $data
     * @return false|string
     * @suppress PhanTypeMismatchReturn
     */
    public static function arrayToJson($data)
    {
        if (is_array($data)) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        return $data;
    }

    /**
     * 将数组转字符串
     * @param $str
     * @return mixed
     */
    public static function jsonToArray($str)
    {
        if (is_array($str)) {
            return $str;
        }
        if (is_null(json_decode((string)$str))) {
            return $str;
        }
        return json_decode((string)$str, true);
    }
}
