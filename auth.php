<?php
// auth.php
require_once __DIR__ . '/db.php';

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function login_user(string $email, string $password): bool
{
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    // Zapisz minimum informacji w sesji
    $_SESSION['user'] = [
        'id'        => $user['id'],
        'email'     => $user['email'],
        'full_name' => $user['full_name'],
        'role'      => $user['role'],
    ];

    return true;
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}

function require_role(string $role): void
{
    $user = current_user();
    if (!$user || $user['role'] !== $role) {
        http_response_code(403);
        echo 'Brak dostępu.';
        exit;
    }
}
