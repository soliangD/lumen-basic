<?php

namespace Common\Helpers\Email;

use Common\Helpers\BaseHelpers;
use Common\Helpers\Utils\ArrayHelper;
use Common\Jobs\SendEmailJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailHelper extends BaseHelpers
{
    /**
     * @param $content
     * @param $title
     * @param $receiver
     * @param bool $queue
     * @param null $attachments
     * @return bool|mixed
     */
    public static function send($content, $title, $receiver = [], $queue = true, $attachments = null)
    {
        if ($queue) {
            return static::addQueueSend($content, $title, $receiver, $attachments);
        }
        return static::mailer($content, $title, $receiver, $attachments);
    }

    /**
     * Email队列
     * @param $content
     * @param $title
     * @param $receiver
     * @param $attachments
     * @param $isInner
     * @return mixed
     */
    private static function addQueueSend(
        $content,
        $title = null,
        $receiver = null,
        $attachments = null,
        $isInner = false
    )
    {
        return dispatch(new SendEmailJob($content, $title, $receiver, $attachments, $isInner));
    }

    /**
     * 发送邮件
     * @param string $content 邮件内容
     * @param string $title 邮件标题
     * @param array|string $receiver 邮件接收者
     * @param $attachments
     * @param bool $isInner
     * @return bool
     */
    public static function mailer($content, $title = null, $receiver = [], $attachments = null, $isInner = false)
    {
        $title = self::getTitle($title, $isInner);
        $content = ArrayHelper::arrayToJson($content);
        $receiver = self::getMail($receiver);
        try {
            Mail::html($content, function ($message) use ($title, $receiver, $attachments) {
                $message->setSubject($title);
                $message->to($receiver);
                if (!empty($attachments)) {
                    $attachments = (array)$attachments;
                    array_map(function ($attachment) use ($message) {
                        $message->attach($attachment);
                    }, $attachments);
                }
            });
        } catch (\Exception $e) {
            Log::error(
                'Email error:' . $e->getCode() . "\r\n" .
                json_encode(['message' => $e->getMessage(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()])
            );
            return false;
        }
        return true;
    }

    protected static function getTitle($title = null, $isInner = true)
    {
        if (is_null($title)) {
            $title = '系统邮件';
        }
        if ($isInner) {
            $projectName = env('APP_NAME', 'sheet');
            $title = "{$projectName}|" . $title . ' - ' . app()->environment();
        }

        return $title;
    }

    /**
     * @param array $develops
     * @return mixed|null
     */
    public static function getMail($develops = [])
    {
        if (empty($develops)) {
            $develops = config('config.develops');
        }
        return $develops;
    }

    /**
     * @param $content
     * @param $title
     * @param $receiver
     * @param bool $queue
     * @param null $attachments
     * @return bool|mixed
     */
    public static function sendInner($content, $title, $receiver = [], $queue = true, $attachments = null)
    {
        if ($queue) {
            return static::addQueueSend($content, $title, $receiver, $attachments, true);
        }
        return static::mailer($content, $title, $receiver, $attachments, true);
    }

    /**
     * 发送异常邮件
     * @param Exception $exception
     * @param $title
     * @param array $receiver
     * @param bool $queue
     * @param null $attachments
     * @return bool|mixed
     */
    public static function sendException(
        Exception $exception,
        $title = null,
        $receiver = [],
        $queue = true,
        $attachments = null
    )
    {
        $content = self::warpException($exception);
        if ($queue) {
            return static::addQueueSend($content, $title, $receiver, $attachments, true);
        }
        return static::mailer($content, $title, $receiver, $attachments, true);
    }

    /**
     * @param Exception $exception
     * @return string
     */
    private static function warpException(Exception $exception)
    {
        $request = Request::capture();

        $requestMethod = $request->getRealMethod();
        $messages = [
            'Referer' => $request->headers->get('referer'),
            'Url' => $request->url(),
            'Method' => $requestMethod,
            'Message' => $exception->getMessage(),
            'File' => $exception->getFile() . ':' . $exception->getLine(),
            'Get' => ArrayHelper::arrayToJson($request->query()),
            'Post' => ArrayHelper::arrayToJson($request->post()),
            'Trace' => PHP_EOL . $exception->getTraceAsString(),
        ];
        if ($requestMethod === 'GET') {
            unset($messages['Post']);
        }
        foreach ($messages as $key => $message) {
            $messages[$key] = '[' . $key . ']: ' . $message;
        }

        return implode(PHP_EOL, array_values($messages));
    }
}
