<?php

namespace pardnchiu;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
  private $mailer;

  public function __construct()
  {
    $this->mailer = new PHPMailer(true);

    $this->mailer->isSMTP();
    $this->mailer->Timeout    = 30;
    $this->mailer->SMTPAuth   = true;
    $this->mailer->Host       = (string)  ($_ENV["MAIL_SERVICE"]            ?? "");
    $this->mailer->Username   = (string)  ($_ENV["MAIL_SERVICE_USER"]       ?? "");
    $this->mailer->Password   = (string)  ($_ENV["MAIL_SERVICE_PASSWORD"]   ?? "");
    $this->mailer->Port       = (int)     ($_ENV["MAIL_SERVICE_PORT"]       ?? 465);
    $this->mailer->CharSet    = (string)  ($_ENV["MAIL_SERVICE_CHARSET"]    ?? "UTF-8");

    switch (true) {
      case $this->mailer->Port === 465:
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        break;
      case in_array($this->mailer->Port, [587, 25]):
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        break;
      default:
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        break;
    };
  }

  public static function send($config)
  {
    $instance = new self();
    return $instance->_send($config);
  }

  private function _send($config)
  {
    try {
      $email      = ($config["email"]     ?? null);
      $subject    = ($config["subject"]   ?? null);
      $body       = ($config["body"]      ?? null);
      $altBody    = ($config["altBody"]   ?? null);
      $fromEmail  = ($config["fromEmail"] ?? $this->mailer->Username);
      $fromName   = ($config["fromName"]  ?? null);
      $cc         = ($config["cc"]        ?? []);
      $bcc        = ($config["bcc"]       ?? []);
      $priority   = ($config["priority"]  ?? null);
      $isHtml     = ($config["isHtml"]    ?? false);

      $this->mailer->setFrom($fromEmail, $fromName);

      if (is_array($email)) {
        foreach ($email as $addr => $name) {
          if (is_numeric($addr)) {
            $this->mailer->addAddress($name);
          } else {
            $this->mailer->addAddress($addr, $name);
          }
        }
      } else {
        $this->mailer->addAddress($email);
      }

      if (!empty($cc) && is_array($cc)) {
        foreach ($cc as $addr => $name) {
          if (is_numeric($addr)) {
            $this->mailer->addCC($name);
          } else {
            $this->mailer->addCC($addr, $name);
          }
        }
      } elseif (!empty($cc) && is_string($cc)) {
        $this->mailer->addCC($cc);
      }

      if (!empty($bcc) && is_array($bcc)) {
        foreach ($bcc as $addr => $name) {
          if (is_numeric($addr)) {
            $this->mailer->addBCC($name);
          } else {
            $this->mailer->addBCC($addr, $name);
          }
        }
      } elseif (!empty($bcc) && is_string($bcc)) {
        $this->mailer->addBCC($bcc);
      }

      $this->mailer->isHTML($isHtml);

      $this->mailer->Subject = $subject;
      $this->mailer->Body = $body;

      if ($altBody) {
        $this->mailer->AltBody = $altBody;
      }

      if ($priority) {
        switch (strtolower($priority)) {
          case 'high':
            $this->mailer->Priority = 1;
            break;
          case 'normal':
            $this->mailer->Priority = 3;
            break;
          case 'low':
            $this->mailer->Priority = 5;
            break;
        }
      }

      return $this->mailer->send();
    } catch (\Exception $e) {
      throw new \Exception("郵件發送失敗: " . $this->mailer->ErrorInfo . " - " . $e->getMessage());
    } finally {
      $this->mailer->clearAddresses();
      $this->mailer->clearCCs();
      $this->mailer->clearBCCs();
      $this->mailer->clearReplyTos();
    }
  }

  public static function sendBulk($recipients, $subject, $body, $options = [])
  {
    $results = [];
    $count = 0;
    $totalRecipients = count($recipients);

    foreach ($recipients as $email => $name) {
      $config = array_merge($options, [
        "email"   => is_numeric($email) ? $name : [$email => $name],
        "subject" => $subject,
        "body"    => $body
      ]);

      $results[$email] = self::send($config);
      $count++;

      // * 添加隨機間隔時間，避免觸發 rate limit
      if ($count < $totalRecipients) {
        $delay = rand(1, 3);
        sleep($delay);
      }
    };

    return $results;
  }
}
