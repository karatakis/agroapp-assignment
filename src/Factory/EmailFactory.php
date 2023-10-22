<?php

declare(strict_types=1);

namespace App\Factory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Psr\Log\LoggerInterface;

final class EmailFactory
{
    private array $settings;

    private LoggerInterface $logger;

    public function __construct(array $settings, LoggerInterface $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    public function createEmail(bool $exceptions = true): PHPMailer
    {
        $settings = $this->settings;
        $mail = new PHPMailer($exceptions);

        // configure
        if (isset($settings['debug']) && $settings['debug'] == true) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = $this->logger;
        }

        // configure
        $mail->isSMTP();
        $mail->setFrom($settings['from'], $settings['from_name']);
        $mail->Host = $settings['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['user'];
        $mail->Password = $settings['pass'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $settings['port'];

        return $mail;
    }
}
