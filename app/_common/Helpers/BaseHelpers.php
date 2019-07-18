<?php

namespace Common\Helpers;

class BaseHelpers
{
    /**
     * @return static
     */
    public static function helper()
    {
        $params = func_get_args();
        if ($params) {
            return new static(...$params);
        } else {
            return new static();
        }
    }
}
