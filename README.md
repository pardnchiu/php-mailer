# PHP Mailer

> Lightweight PHP mail client with SMTP support, automatic configuration management, and complete email functionality.<br>
> Built on PHPMailer, providing stable and reliable email delivery experience.

[![packagist](https://img.shields.io/packagist/v/pardnchiu/mailer)](https://packagist.org/packages/pardnchiu/mailer)
[![version](https://img.shields.io/github/v/tag/pardnchiu/php-mailer?label=release)](https://github.com/pardnchiu/php-mailer/releases)
[![license](https://img.shields.io/github/license/pardnchiu/php-mailer)](LICENSE)<br>
[![readme](https://img.shields.io/badge/readme-EN-white)](README.md)
[![readme](https://img.shields.io/badge/readme-ZH-white)](README.zh.md)

- [Three Core Features](#three-core-features)
  - [Automatic Configuration Management](#automatic-configuration-management)
  - [Bulk Sending Support](#bulk-sending-support)
  - [Stable Connection](#stable-connection)
- [Features](#features)
- [Usage](#usage)
  - [Installation](#installation)
  - [Environment Variables Setup](#environment-variables-setup)
  - [Basic Usage](#basic-usage)
- [API Reference](#api-reference)
  - [Basic Sending](#basic-sending)
  - [Bulk Sending](#bulk-sending)
- [Error Handling](#error-handling)
- [License](#license)
- [Author](#author)

## Three Core Features

### Automatic Configuration Management
Automatically configure connection parameters based on environment variables, supporting multiple SMTP services

### Bulk Sending Support
Support bulk email sending functionality, suitable for system notifications and high-volume sending needs

### Stable Connection
Built-in error handling and automatic cleanup mechanisms to ensure reliable email delivery

## Features

- **Environment Variable Configuration**: Flexible environment variable setup supporting multi-environment deployment
- **SMTP Support**: Support for multiple SMTP services with automatic encryption configuration
- **HTML/Plain Text**: Support for both HTML and plain text email formats
- **Multiple Recipients**: Support for multiple recipients, CC, and BCC
- **Priority Settings**: Support for high, normal, and low priority email sending
- **Bulk Sending**: Built-in bulk sending functionality for improved sending efficiency
- **Automatic Cleanup**: Automatic cleanup of recipient lists after sending

## Usage

### Installation

```shell
composer require pardnchiu/mailer
```

### Environment Variables Setup

```env
MAIL_SERVICE=smtp.gmail.com          # SMTP server
MAIL_SERVICE_USER=your@email.com     # Sender account
MAIL_SERVICE_PASSWORD=your_password  # Password or app password
MAIL_SERVICE_PORT=587                # SMTP port (465/587/25)
MAIL_SERVICE_CHARSET=UTF-8           # Character encoding
```

### Basic Usage

**Method 1: Static Call (Recommended)**

```php
<?php

use pardnchiu\Mailer;

// Direct static call
$result = Mailer::send([
  "email"     => "recipient@example.com",
  "subject"   => "Test Email",
  "body"      => "<h1>This is a test email</h1>",
  "isHtml"    => true
]);

if ($result) {
  echo "Email sent successfully";
} else {
  echo "Email sending failed";
}
```

**Method 2: Instance Call**

```php
<?php

use pardnchiu\Mailer;

// Initialize mail client
$mailer = new Mailer();

// Basic email sending (calling static method through instance)
$result = $mailer::send([
  "email"     => "recipient@example.com",
  "subject"   => "Test Email",
  "body"      => "<h1>This is a test email</h1>",
  "isHtml"    => true
]);

if ($result) {
  echo "Email sent successfully";
} else {
  echo "Email sending failed";
}
```

## API Reference

### Basic Sending

**Mailer::send($config)** - Send single email (static call)

```php
$result = Mailer::send([
  "email"     => "recipient@example.com",          // Recipient (required)
  "subject"   => "Email Subject",                  // Subject (required)
  "body"      => "Email Content",                  // Content (required)
  "altBody"   => "Plain text content",             // Plain text version (optional)
  "fromEmail" => "sender@example.com",            // Sender email (optional, defaults to configured account)
  "fromName"  => "Sender Name",                   // Sender name (optional)
  "cc"        => ["cc@example.com"],              // CC (optional)
  "bcc"       => ["bcc@example.com"],             // BCC (optional)
  "priority"  => "high",                          // Priority: high/normal/low (optional)
  "isHtml"    => true                             // HTML format (optional, default false)
]);
```

**Multiple Recipients**

```php
$config = [
  "email" => [
    "user1@example.com" => "User One",
    "user2@example.com" => "User Two",
    "user3@example.com"
  ],
  "subject" => "Group Notification",
  "body"    => "This is a group notification email"
];

$result = Mailer::send($config);
```

**CC and BCC**

```php
$config = [
  "email"   => "primary@example.com",
  "cc"      => [
    "cc1@example.com" => "CC User One",
    "cc2@example.com"
  ],
  "bcc"     => [
    "bcc1@example.com" => "BCC User One",
    "bcc2@example.com"
  ],
  "subject" => "Important Notice",
  "body"    => "Email content"
];

$result = Mailer::send($config);
```

### Bulk Sending

**Mailer::sendBulk($recipients, $subject, $body, $options)** - Bulk email sending (static call)

> Note: Bulk sending automatically adds a random interval of 1-3 seconds between each email to avoid triggering rate limits from email providers like Gmail and iCloud.

```php
$results = Mailer::sendBulk(
  [
    "user1@example.com" => "User One",
    "user2@example.com" => "User Two",
    "user3@example.com" => "User Three"
  ],
  "Bulk Notification Email",
  "<h1>This is a bulk sent email</h1>",
  [
    "isHtml"    => true,
    "priority"  => "normal",
    "fromName"  => "System Administrator"
  ]
);

// Check sending results
foreach ($results as $email => $success) {
  if ($success) {
    echo "Successfully sent to {$email}\n";
  } else {
    echo "Failed to send to {$email}\n";
  }
}
```

## Error Handling

```php
try {
  $mailer = new Mailer();
  
  $result = Mailer::send([
    "email"   => "test@example.com",
    "subject" => "Test Email",
    "body"    => "Test content"
  ]);
  
  if ($result) {
    echo "Email sent successfully";
  } else {
    echo "Email sending failed";
  }
    
} catch (\Exception $e) {
  error_log("Email sending error: " . $e->getMessage());
  
  if (strpos($e->getMessage(), "SMTP connect() failed") !== false) {
    echo "SMTP connection failed, please check server settings";
  } elseif (strpos($e->getMessage(), "Authentication") !== false) {
    echo "SMTP authentication failed, please check username and password";
  } else {
    echo "Email sending exception: " . $e->getMessage();
  }
}
```

### Connection Test

```php
// Test SMTP connection
try {
  $mailer = new Mailer();
  
  // Send test email
  $result = Mailer::send([
      "email"   => "test@example.com",
      "subject" => "SMTP Connection Test",
      "body"    => "If you receive this email, SMTP configuration is correct."
  ]);
  
  echo $result ? "SMTP configuration is correct" : "SMTP configuration error";
  
} catch (\Exception $e) {
  echo "SMTP test failed: " . $e->getMessage();
}
```

### Bulk Sending Monitoring

```php
$results = Mailer::sendBulk([
    "user1@example.com" => "User One",
    "user2@example.com" => "User Two",
    "user3@example.com" => "User Three"
], "Notification", "Content");

$successCount = array_sum($results);
$totalCount = count($results);
$failureCount = $totalCount - $successCount;

echo "Bulk sending completed - Success: {$successCount}, Failed: {$failureCount}, Total: {$totalCount}";
```

## License

This source code project is licensed under [MIT](LICENSE).

## Author

<img src="https://avatars.githubusercontent.com/u/25631760" align="left" width="96" height="96" style="margin-right: 0.5rem;">

<h4 style="padding-top: 0">邱敬幃 Pardn Chiu</h4>

<a href="mailto:dev@pardn.io" target="_blank">
    <img src="https://pardn.io/image/email.svg" width="48" height="48">
</a> <a href="https://linkedin.com/in/pardnchiu" target="_blank">
    <img src="https://pardn.io/image/linkedin.svg" width="48" height="48">
</a>

***

©️ 2025 [邱敬幃 Pardn Chiu](https://pardn.io)