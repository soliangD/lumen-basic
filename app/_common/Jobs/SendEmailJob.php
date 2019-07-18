<?php

namespace Common\Jobs;

use Common\Helpers\Email\EmailHelper;

class SendEmailJob extends Job
{
    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 3;
    /**
     * @var string|null
     */
    public $title;
    /**
     * @var string
     */
    public $content;
    /**
     * @var string|array
     */
    public $mail;
    /**
     * @var string|array
     */
    public $isInner;

    public $attachments;

    /**
     * SendEmailJob constructor.
     * @param $content
     * @param $title
     * @param $mail
     * @param $attachments
     * @param $isInner
     */
    public function __construct($content, $title = null, $mail = null, $attachments = '', $isInner = false)
    {
        $this->title = $title;
        $this->content = $content;
        $this->mail = $mail;
        $this->attachments = $attachments;
        $this->isInner = $isInner;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        if (app()->environment() == 'local') {
            return;
        }
        try {
            EmailHelper::mailer($this->content, $this->title, $this->mail, $this->attachments, $this->isInner);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
