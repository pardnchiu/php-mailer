# PHP Mailer

> 輕量級 PHP 郵件發送客戶端，支援 SMTP 發送、自動配置管理和完整的郵件功能。<br>
> 基於 PHPMailer 建構，提供穩定可靠的郵件發送體驗。

[![packagist](https://img.shields.io/packagist/v/pardnchiu/mailer)](https://packagist.org/packages/pardnchiu/mailer)
[![version](https://img.shields.io/github/v/tag/pardnchiu/php-mailer?label=release)](https://github.com/pardnchiu/php-mailer/releases)
[![license](https://img.shields.io/github/license/pardnchiu/php-mailer)](LICENSE)<br>
[![readme](https://img.shields.io/badge/readme-EN-white)](README.md)
[![readme](https://img.shields.io/badge/readme-ZH-white)](README.zh.md)

- [三大核心特色](#三大核心特色)
  - [自動配置管理](#自動配置管理)
  - [批量發送支援](#批量發送支援)
  - [穩定連接](#穩定連接)
- [功能特性](#功能特性)
- [使用方法](#使用方法)
  - [安裝](#安裝)
  - [環境變數設定](#環境變數設定)
  - [基本使用](#基本使用)
- [API 參考](#api-參考)
  - [基本發送](#基本發送)
  - [批量發送](#批量發送)
- [錯誤處理](#錯誤處理)
- [授權協議](#授權協議)
- [作者](#作者)

## 三大核心特色

### 自動配置管理
自動根據環境變數設定連接參數，支援多種 SMTP 服務

### 批量發送支援
支援批量郵件發送功能，適合系統通知等大量發送需求

### 穩定連接
內建錯誤處理和自動清理機制，確保郵件發送的可靠性

## 功能特性

- **環境變數配置**: 靈活的環境變數設定，支援多環境部署
- **SMTP 支援**: 支援多種 SMTP 服務，自動配置加密方式
- **HTML/純文字**: 支援 HTML 和純文字郵件格式
- **多收件人**: 支援多收件人、副本和密件副本
- **優先級設定**: 支援高、中、低優先級郵件發送
- **批量發送**: 內建批量發送功能，提升發送效率
- **自動清理**: 發送後自動清理收件人列表

## 使用方法

### 安裝

```shell
composer require pardnchiu/mailer
```

### 環境變數設定

```env
MAIL_SERVICE=smtp.gmail.com          # SMTP 伺服器
MAIL_SERVICE_USER=your@email.com     # 發送帳號
MAIL_SERVICE_PASSWORD=your_password  # 發送密碼或應用程式密碼
MAIL_SERVICE_PORT=587                # SMTP 連接埠 (465/587/25)
MAIL_SERVICE_CHARSET=UTF-8           # 字符編碼
```

### 基本使用

**方法一：靜態調用（推薦）**

```php
<?php

use pardnchiu\Mailer;

// 直接靜態調用
$result = Mailer::send([
  "email"     => "recipient@example.com",
  "subject"   => "測試郵件",
  "body"      => "<h1>這是一封測試郵件</h1>",
  "isHtml"    => true
]);

if ($result) {
  echo "郵件發送成功";
} else {
  echo "郵件發送失敗";
}
```

**方法二：實例化調用**

```php
<?php

use pardnchiu\Mailer;

// 初始化郵件客戶端
$mailer = new Mailer();

// 基本郵件發送（透過實例調用靜態方法）
$result = $mailer::send([
  "email"     => "recipient@example.com",
  "subject"   => "測試郵件",
  "body"      => "<h1>這是一封測試郵件</h1>",
  "isHtml"    => true
]);

if ($result) {
  echo "郵件發送成功";
} else {
  echo "郵件發送失敗";
}
```

## API 參考

### 基本發送

**Mailer::send($config)** - 發送單封郵件（靜態調用）

```php
$result = Mailer::send([
  "email"     => "recipient@example.com",          // 收件人 (必填)
  "subject"   => "郵件主旨",                        // 主旨 (必填)
  "body"      => "郵件內容",                        // 內容 (必填)
  "altBody"   => "純文字內容",                      // 純文字版本 (選填)
  "fromEmail" => "sender@example.com",            // 發件人信箱 (選填，默認為設定帳號)
  "fromName"  => "發件人名稱",                      // 發件人姓名 (選填)
  "cc"        => ["cc@example.com"],              // 副本 (選填)
  "bcc"       => ["bcc@example.com"],             // 密件副本 (選填)
  "priority"  => "high",                          // 優先級: high/normal/low (選填)
  "isHtml"    => true                             // 是否為HTML格式 (選填，默認false)
]);
```

**多收件人發送**

```php
$config = [
  "email" => [
    "user1@example.com" => "用戶一",
    "user2@example.com" => "用戶二",
    "user3@example.com"
  ],
  "subject" => "群組通知",
  "body"    => "這是群組通知郵件"
];

$result = Mailer::send($config);
```

**副本和密件副本**

```php
$config = [
  "email"   => "primary@example.com",
  "cc"      => [
    "cc1@example.com" => "CC用戶一",
    "cc2@example.com"
  ],
  "bcc"     => [
    "bcc1@example.com" => "BCC用戶一",
    "bcc2@example.com"
  ],
  "subject" => "重要通知",
  "body"    => "郵件內容"
];

$result = Mailer::send($config);
```

### 批量發送

**Mailer::sendBulk($recipients, $subject, $body, $options)** - 批量發送郵件（靜態調用）

> 註：批量發送會在每封郵件之間自動添加 1-3 秒的隨機間隔，以避免觸發 Gmail、iCloud 等郵件服務商的 rate limit 限制。

```php
$results = Mailer::sendBulk(
  [
    "user1@example.com" => "用戶一",
    "user2@example.com" => "用戶二",
    "user3@example.com" => "用戶三"
  ,
  "批量通知郵件",
  "<h1>這是批量發送的郵件</h1>",
  [
    "isHtml"    => true,
    "priority"  => "normal",
    "fromName"  => "系統管理員"
  ]
);

// 檢查發送結果
foreach ($results as $email => $success) {
  if ($success) {
    echo "發送到 {$email} 成功\n";
  } else {
    echo "發送到 {$email} 失敗\n";
  }
}
```

## 錯誤處理

```php
try {
  $mailer = new Mailer();
  
  $result = Mailer::send([
    "email"   => "test@example.com",
    "subject" => "測試郵件",
    "body"    => "測試內容"
  ]);
  
  if ($result) {
    echo "郵件發送成功";
  } else {
    echo "郵件發送失敗";
  }
    
} catch (\Exception $e) {
  error_log("郵件發送錯誤: " . $e->getMessage());
  
  if (strpos($e->getMessage(), "SMTP connect() failed") !== false) {
    echo "SMTP 連接失敗，請檢查伺服器設定";
  } elseif (strpos($e->getMessage(), "Authentication") !== false) {
    echo "SMTP 認證失敗，請檢查帳號密碼";
  } else {
    echo "郵件發送異常: " . $e->getMessage();
  }
}
```

### 連接測試

```php
// 測試 SMTP 連接
try {
  $mailer = new Mailer();
  
  // 發送測試郵件
  $result = Mailer::send([
      "email"   => "test@example.com",
      "subject" => "SMTP 連接測試",
      "body"    => "如果您收到此郵件，表示 SMTP 設定正確。"
  ]);
  
  echo $result ? "SMTP 設定正確" : "SMTP 設定有誤";
  
} catch (\Exception $e) {
  echo "SMTP 測試失敗: " . $e->getMessage();
}
```

### 批量發送監控

```php
$results = Mailer::sendBulk([
    "user1@example.com" => "用戶一",
    "user2@example.com" => "用戶二",
    "user3@example.com" => "用戶三"
], "通知", "內容");

$successCount = array_sum($results);
$totalCount = count($results);
$failureCount = $totalCount - $successCount;

echo "批量發送完成 - 成功: {$successCount}, 失敗: {$failureCount}, 總數: {$totalCount}";
```

## 授權協議

本原始碼專案採用 [MIT](LICENSE) 授權。

## 作者

<img src="https://avatars.githubusercontent.com/u/25631760" align="left" width="96" height="96" style="margin-right: 0.5rem;">

<h4 style="padding-top: 0">邱敬幃 Pardn Chiu</h4>

<a href="mailto:dev@pardn.io" target="_blank">
    <img src="https://pardn.io/image/email.svg" width="48" height="48">
</a> <a href="https://linkedin.com/in/pardnchiu" target="_blank">
    <img src="https://pardn.io/image/linkedin.svg" width="48" height="48">
</a>

***

©️ 2025 [邱敬幃 Pardn Chiu](https://pardn.io)