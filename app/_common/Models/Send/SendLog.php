<?php

namespace Common\Models\Send;

use Common\Helpers\Utils\DateHelper;
use Common\Models\BaseModel;

class SendLog extends BaseModel
{
    protected $table = 'send_log';
    protected $fillable = [];
    protected $hidden = [];

    /** @var int 发送类型：邮件 */
    const TYPE_EMAIL = 1;
    /** @var int 发送类型：短信 */
    const TYPE_SMS = 2;
    /** @var array 发送类型 */
    const TYPE = [
        self::TYPE_EMAIL => '邮件',
        self::TYPE_SMS => '短信',
    ];

    /** TODO channel 最后改到配置内 */
    /** @var string 邮件渠道：ali-soliang */
    const CHANNEL_EMAIL_ALIYUN_SOLIANG = 'aliyun_soliang';
    /** @var string 邮件渠道：163-soliangZ */
    const CHANNEL_EMAIL_163_SOLIANGZ = '163_soliangz';
    /** @var string 邮件渠道：其他 */
    const CHANNEL_EMAIL_OTHER = 'other';

    /** @var int 状态：创建 */
    const STATUS_CREATE = 1;
    /** @var int 状态：发送中 */
    const STATUS_SENDING = 2;
    /** @var int 状态：发送成功 */
    const STATUS_SUCCESS = 3;
    /** @var int 状态：发送失败 */
    const STATUS_FAILURE = 4;
    /** @var int 状态：获取结果超时 */
    const STATUS_NOT_CALL = 5;
    /** @var array 状态 */
    const STATUS = [
        self::STATUS_CREATE => '创建',
        self::STATUS_SENDING => '发送中',
        self::STATUS_SUCCESS => '发送成功',
        self::STATUS_FAILURE => '发送失败',
        self::STATUS_NOT_CALL => '获取结果超时',
    ];

    const SCENARIO_CREATE = 'create';
    const SCENARIO_CALL = 'call';

    public function safes()
    {
        return [
            self::SCENARIO_CREATE => [
                'type',
                'channel',
                'receiver',
                'desc',
                'status',
                'send_time',
                'call_time',
            ],
            self::SCENARIO_CALL => [
                'desc',
                'status',
                'call_time',
            ],
        ];
    }

    public function textRules()
    {
        return [
            'array' => [
            ],
            'function' => [
            ],
        ];
    }

    /**
     *
     * @param array $params
     * @return bool|SendLog
     */
    public function add(array $params)
    {
        $att = [
            'type' => $params['type'],
            'channel' => $params['channel'],
            'receiver' => $params['receiver'],
            'desc' => $params['desc'] ?? '',
            'status' => self::STATUS_CREATE,
        ];
        if (isset($params['send_time'])) {
            $att['send_time'] = $params['send_time'];
        }
        if (isset($params['call_time'])) {
            $att['call_time'] = $params['call_time'];
        }
        return self::model(self::SCENARIO_CREATE)->saveModel($att);
    }

    public function toSending($desc = '')
    {
        $this->send_time = DateHelper::dateTime();
        $this->status = self::STATUS_SENDING;
        $this->desc = $desc;
        return $this->save();
    }

    public function toSuccess($callTime = null, $desc = '')
    {
        $this->call_time = $callTime ?? DateHelper::dateTime();
        $this->status = self::STATUS_SUCCESS;
        $this->desc = $desc;
        return $this->save();
    }

    public function toFailure($callTime = null, $desc = '')
    {
        $this->call_time = $callTime ?? DateHelper::dateTime();
        $this->status = self::STATUS_FAILURE;
        $this->desc = $desc;
        return $this->save();
    }

    /***************************************** 分割线 ☝ base ☟ relations **********************************************/
    /***************************************** 分割线 ☝ relations ☟ append ********************************************/

    /**
     * 根据发送人获取邮件渠道
     * TODO 之后根据配置设置
     * @param $sender
     * @return string
     */
    public static function getEmailChannelBySender($sender)
    {
        switch ($sender) {
            case 'soliang@aliyun.com':
                return self::CHANNEL_EMAIL_ALIYUN_SOLIANG;
            case 'soliangz@163.com':
                return self::CHANNEL_EMAIL_163_SOLIANGZ;
            default:
                return self::CHANNEL_EMAIL_OTHER;
        }
    }
}
