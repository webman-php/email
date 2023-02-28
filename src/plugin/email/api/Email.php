<?php

namespace plugin\email\api;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use plugin\admin\app\model\Option;
use support\exception\BusinessException;

class Email
{

    /**
     * Option 表的name字段值
     */
    const OPTION_NAME = 'email_setting';

    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $content
     * @return void
     * @throws Exception|BusinessException
     */
    public static function send($from, $to, $subject, $content)
    {
        $mailer = static::getMailer();
        call_user_func_array([$mailer, 'setFrom'], (array)$from);
        call_user_func_array([$mailer, 'addAddress'], (array)$to);
        $mailer->Subject = $subject;
        $mailer->isHTML(true);
        $mailer->Body = $content;
        $mailer->send();
    }

    /**
     * Get Mailer
     * @return PHPMailer
     * @throws BusinessException
     */
    public static function getMailer(): PHPMailer
    {
        if (!class_exists(PHPMailer::class)) {
            throw new BusinessException('请执行 composer require phpmailer/phpmailer 并重启');
        }
        $config = Option::where('name', static::OPTION_NAME)->value('value');
        $config = $config ? json_decode($config, true) : false;
        if (!$config) {
            throw new BusinessException('未设置邮件配置');
        }
        $mailer = new PHPMailer();
        $mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        $mailer->isSMTP();
        $mailer->Host = $config['Host'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $config['Username'];
        $mailer->Password = $config['Password'];
        $map = [
            'ssl' => PHPMailer::ENCRYPTION_SMTPS,
            'tls' => PHPMailer::ENCRYPTION_STARTTLS,
        ];
        $mailer->SMTPSecure = $map[$config['SMTPSecure']] ?? '';
        $mailer->Port = $config['Port'];
        return $mailer;
    }

}