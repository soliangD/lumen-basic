<?php

namespace Common\Events\User;

use Common\Events\Event;

class RegisterEvent extends Event
{
    public $userId;

    /**
     * @var bool 只处理邮件发送 监听器
     */
    public $onlyEmailSend;

    public function __construct($userId, bool $onlyEmailSend = false)
    {
        $this->userId = $userId;
        $this->onlyEmailSend = $onlyEmailSend;
    }
}
