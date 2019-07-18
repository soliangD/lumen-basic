<?php

namespace Common\Validators;

use Illuminate\Validation\Validator;

class Validation extends Validator
{
//    //自定义 replacers 文案占位符替换
//    public function __construct(Translator $translator, array $data, array $rules, array $messages = [], array $customAttributes = [])
//    {
//        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
//        $this->replacers = array_merge($this->replacers, $this->setReplacers());
//    }
//
//    public function setReplacers()
//    {
//        return [
//            'mobile' => function($message, $attribute, $rule, $parameters) {
//                return str_replace([':f', ':q'], $parameters, $message);
//            },
//        ];
//    }


    /**
     * Validate mobile
     * 验证手机号
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return false|int
     */
    public static function validateMobile($attribute, $value, $parameters = [])
    {
        return preg_match('/^1[3456789]{1}\d{9}$/', $value);
    }

    /**
     * Validate id_card
     * 身份证
     * @param $data mixed 数字或者字符串
     * @return bool
     **/
    public static function validateIdCard($data = null)
    {
        $_pattern = "/^(^\d{15}$)|(^\d{17}([0-9]|X)$)$/i";
        return self::_regex($_pattern, $data);
    }

//    public function validateCaptcha($attribute, $value, $parameters)
//    {
//        /** 正式环境校验验证码 */
//        if (SmsCaptchaHelper::helper()->hasSmsOn()) {
//            $telephone = current($parameters);
//            return CaptchaServer::server()->validateSmsCode($value, $telephone);
//        }
//        return true;
//    }

    /**
     * 匹配正则公共方法
     * @param $pattern string 匹配模式
     * @param $subject string 对象
     * @return bool
     */
    private static function _regex($pattern, $subject = null)
    {
        if ($subject === null) {
            return false;
        }
        if (preg_match($pattern, $subject)) {
            return true;
        }
        return false;
    }

    /**
     * Validate zh
     * 判断是否为全中文
     * @param $data mixed 数字或者字符串
     * @return bool
     **/
    public static function validateZh($data = null)
    {
        $_pattern = "/^[\x{4e00}-\x{9fa5}]+$/u";
        return self::_regex($_pattern, $data);
    }

    /**
     * Validate extension
     * 验证文件后缀名
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateExtension($attribute, $value, $parameters)
    {
        if (!$this->isValidFileInstance($value)) {
            return false;
        }
        if ($this->shouldBlockPhpUpload($value, $parameters)) {
            return false;
        }
        return $value->getPath() !== '' && in_array($value->getClientOriginalExtension(), $parameters);
    }

    /**
     * Validate decimal
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return false|int
     */
    public function validateDecimal($attribute, $value, $parameters)
    {
        $scale = $parameters[0] ?? 1;
        return preg_match('/^[0-9]+(.[0-9]{1,' . $scale . '})?$/', $value);
    }

    /**
     * Validate array_unique
     * 判断数组元素是否有重复值
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateArrayUnique($attribute, $value, $parameters)
    {
        if (!$this->validateArray($attribute, $value)) {
            return false;
        }

        foreach ($value as &$item) {
            if (is_string($item)) {
                $item = trim($item);
            } elseif (is_array($item)) {
                foreach ($item as &$i) {
                    if (is_string($i)) {
                        $i = trim($i);
                    }
                }
            }
        }

        while (count($value) > 0) {
            if (in_array(array_shift($value), $value)) {
                return false;
            }
        }

        return true;
    }
}
