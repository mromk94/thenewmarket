<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\HttpException;
use App\Core\Logger;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function send(string $to, string $subject, string $body, string $altBody = ''): void
    {
        $mailer = (string) setting('mail', 'mailer', env('MAIL_MAILER', 'log'));

        if ($mailer === 'log') {
            self::logMail($to, $subject, $body);
            return;
        }

        if ($mailer === 'smtp') {
            self::sendSmtp($to, $subject, $body, $altBody);
            return;
        }

        // Fallback to PHP mail()
        $from = (string) setting('mail', 'from_address', env('MAIL_FROM_ADDRESS', 'noreply@thenewage.local'));
        $headers = "From: " . $from . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        mail($to, $subject, $body, $headers);
    }

    private static function sendSmtp(string $to, string $subject, string $body, string $altBody): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = (string) setting('mail', 'host', env('MAIL_HOST', ''));
            $mail->SMTPAuth = true;
            $mail->Username = (string) setting('mail', 'username', env('MAIL_USERNAME', ''));
            $mail->Password = (string) setting('mail', 'password', env('MAIL_PASSWORD', ''));
            $encryption = (string) setting('mail', 'encryption', env('MAIL_ENCRYPTION', 'tls'));
            $mail->SMTPSecure = in_array($encryption, ['tls', 'ssl'], true) ? $encryption : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) setting('mail', 'port', env('MAIL_PORT', 587));

            $fromAddress = (string) setting('mail', 'from_address', env('MAIL_FROM_ADDRESS', 'noreply@thenewage.local'));
            $fromName = (string) setting('mail', 'from_name', env('MAIL_FROM_NAME', config('app.name')));
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody ?: strip_tags($body);

            $mail->send();
        } catch (\Throwable $e) {
            Logger::error('SMTP send failed: ' . $e->getMessage());
            throw new HttpException('Could not send email. Please try again later.', 500);
        }
    }

    private static function logMail(string $to, string $subject, string $body): void
    {
        $logFile = BASE_PATH . '/storage/logs/mail.log';
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $entry = "[" . date('Y-m-d H:i:s') . "]\n";
        $entry .= "To: {$to}\n";
        $entry .= "Subject: {$subject}\n";
        $entry .= "Body:\n{$body}\n";
        $entry .= str_repeat('-', 60) . "\n";

        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}
