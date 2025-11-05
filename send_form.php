<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = strip_tags(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = strip_tags(trim($_POST['message']));
    $captcha = trim($_POST['captcha']);

    if (!isset($_SESSION['captcha_result']) || $captcha != $_SESSION['captcha_result']) {
        die('<div class="error">Błędny wynik CAPTCHA. Spróbuj ponownie.</div>');
    }

    if (empty($name) || empty($email) || empty($message)) {
        die('<div class="error">Wszystkie pola są wymagane.</div>');
    }

    $to = 'kaczmarczyk.k@wp.pl';
    $subject = "Wiadomość z formularza kontaktowego";
    $body = "Imię i nazwisko: $name\nE-mail: $email\n\nWiadomość:\n$message";
    $headers = "From: $email\r\nReply-To: $email\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo '<div class="success">✅ Dziękujemy! Wiadomość została wysłana.</div>';
    } else {
        echo '<div class="error">❌ Wystąpił problem z wysyłką wiadomości.</div>';
    }
}
?>