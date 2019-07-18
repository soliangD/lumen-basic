<?php

namespace Common\Helpers\Utils;

use Carbon\Carbon;

class DateHelper
{
    /**
     * 获取当前时间 Y-m-d H:i:s
     * @return string
     */
    public static function dateTime()
    {
        return Carbon::now()->toDateTimeString();
    }

    /**
     * 将时间戳格式化为指定 日期 格式
     * @param $timestamp
     * @param string $format
     * @return string|null
     */
    public static function formatTo($timestamp, $format = 'Y/m/d H:i:s')
    {
        return Carbon::parse($timestamp)->format($format);
    }

    /**
     * 取两个时间的最小值
     * @param $dt1
     * @param $dt2
     * @return Carbon
     */
    public static function min($dt1, $dt2)
    {
        $dt1 = Carbon::parse($dt1);
        return $dt1->min($dt2);
    }

    /**
     * 获取两时间相差天数
     * @param $date1
     * @param string $date2
     * @return int
     */
    public static function diffInDays($date1, $date2 = null)
    {
        if (is_null($date2)) {
            $date2 = self::date();
        }
        $dt1 = Carbon::parse($date1);
        return $dt1->diffInDays($date2);
    }

    /**
     * 获取当前日期
     * @return string
     */
    public static function date()
    {
        return Carbon::now()->toDateString();
    }

    /**
     * 获取 $date1 相对于 $date2 的时间差语义化
     * @param $date1
     * @param $date2
     * @param int $parts 语义化展示的层数 如：1 展示为 2天前 2月前； 2 展示为 2天15小时前 2月5天前
     * @return string
     */
    public static function diffForHumans($date1, $date2 = null, $parts = 1)
    {
        return Carbon::parse($date1)->diffForHumans($date2, null, true, $parts);
    }

    /**
     * 时间 差 语义化，支持时间戳整形传值
     * @param $startTime
     * @param $endTime
     * @param int $parts 语义化展示的层数
     * @return string
     */
    public static function simpleDiffForHumans($startTime, $endTime = null, $parts = 1)
    {
        if (!is_int($startTime)) {
            $startTime = strtotime($startTime);
        }

        if ($endTime === null) {
            $endTime = time();
        } elseif (!is_int($endTime)) {
            $endTime = strtotime($endTime);
        }
        $diffTime = $endTime - $startTime;

        $formatter = function ($data) {
            return sprintf('%02d', (string)$data);
        };

        if ($diffTime == 0) {
            return '';
        }

        $consume = 0;
        $consumeLevel = ['d' => 86400, 'h' => 3600, 'm' => 60, 's' => 1];
        $zhCn = ['d' => '天', 'h' => '小时', 'm' => '分钟', 's' => '秒'];
        $diff = [];
        foreach ($consumeLevel as $k => $v) {
            $diff[$k] = (int)(($diffTime - $consume) / $v);
            $consume += $diff[$k] * $v;
        }

        $result = '';
        foreach ($diff as $k => $v) {
            if ($v > 0 && $parts-- > 0) {
                $result .= $formatter($v) . $k;
            }
        }

        if (config('app.locale') == 'zh-CN') {
            $result = str_replace(array_keys($zhCn), array_values($zhCn), $result);
        }

        return $result;
    }
}
