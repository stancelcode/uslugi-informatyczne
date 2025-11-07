<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Prosta walidacja
if ($name === '' || $email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /?contact_error=1');
    exit;
}

// Zapis do bazy
$stmt = $pdo->prepare("
    INSERT INTO contact_messages (name, email, phone, message)
    VALUES (:name, :email, :phone, :message)
");
$stmt->execute([
    'name'    => $name,
    'email'   => $email,
    'phone'   => $phone !== '' ? $phone : null,
    'message' => $message,
]);

// (Opcjonalnie) wysyłka maila do Ciebie
// Uzupełnij swój adres e-mail, albo zakomentuj cały blok jeśli nie chcesz e-maili.
$to      = 'kaczmarczyk.k@wp.pl'; 
$subject = 'Nowa wiadomość z formularza kontaktowego';
$body    = "Imię i nazwisko: {$name}\n"
         . "Email: {$email}\n"
         . "Telefon: {$phone}\n\n"
         . "Wiadomość:\n{$message}\n";
$headers = 'From: noreply@twojadomena.pl' . "\r\n"
         . 'Reply-To: ' . $email . "\r\n"
         . 'X-Mailer: PHP/' . phpversion();

// Jeśli nie chcesz wysyłki maili, możesz zakomentować poniższą linię:
@mail($to, $subject, $body, $headers);

// Po wysłaniu – powrót na stronę główną
header('Location: /?sent=1');
exit;
