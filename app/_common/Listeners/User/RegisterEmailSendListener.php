<?php

namespace Common\Listeners\User;

use Api\Services\User\LoginService;
use Common\Events\User\RegisterEvent;
use Common\Helpers\Email\EmailHelper;
use Common\Helpers\Utils\DateHelper;
use Common\Models\Send\SendLog;
use Common\Models\User\User;
use Common\Redis\User\RegisterRedis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

//use Illuminate\Queue\InteractsWithQueue;

class RegisterEmailSendListener implements ShouldQueue
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var int 最多邮件推送限制
     */
    protected $sendLimit = 3;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param RegisterEvent $event
     * @return void
     */
    public function handle(RegisterEvent $event)
    {
        $this->user = User::getBy($event->userId, false);

        // 用户已存在且状态正常，不进行处理
        if (!$this->user || !$this->user->isNotActivated()) {
            return false;
        }

        // 每天推送限制
        if (RegisterRedis::redis()->sendCount($this->user->email) > $this->sendLimit) {
            return false;
        }

        $registerUrl = LoginService::server()->generateRegisterUrl($this->user);

        $sendRes = $this->sendRegisterEmail($registerUrl);

        return $sendRes;
    }

    public function sendRegisterEmail($registerUrl)
    {
        $registerCodeExpiry = floor(LoginService::REGISTER_CODE_EXPIRY / 3600);
        $receiver = $this->user->email;

        $content = "
你好，欢迎注册 Sheet Soocoo！
<br/>
<br/>
请在{$registerCodeExpiry}小时内点击下面的链接激活帐号:
<br/>
{$registerUrl}
<br/>
<br/>
如果不是您本人操作，请忽略此邮件。
";

        $sendRes = EmailHelper::send($content, '完成注册', $receiver, false);

        // 记录send log
        DB::transaction(function () use ($receiver, $sendRes) {
            $sendLog = SendLog::model()->add([
                'type' => SendLog::TYPE_EMAIL,
                'channel' => SendLog::getEmailChannelBySender(config('mail.from.address')),
                'receiver' => $receiver,
                'send_time' => DateHelper::dateTime(),
            ]);

            $sendRes ? $sendLog->toSuccess(null, '发送成功') : $sendLog->toFailure(null, '发送失败');
        });

        return $sendRes;
    }
}
