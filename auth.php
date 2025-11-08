<?php
// auth.php – obsługa sesji, logowania i ról

require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Zwraca aktualnie zalogowanego użytkownika (tablica z bazy)
 * lub null, jeśli nikt nie jest zalogowany.
 */
function current_user(): ?array {
    static $cached = null;

    if ($cached !== null) {
        return $cached;
    }

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    global $pdo;
    $stmt = $pdo->prepare("SELECT id, full_name, email, role, is_active FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || (int)$user['is_active'] !== 1) {
        // konto nieaktywne lub usunięte – wyloguj
        logout_user();
        return null;
    }

    $cached = $user;
    return $user;
}

/**
 * Zaloguj użytkownika po ID.
 */
function login_user(int $userId): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

/**
 * Wylogowanie.
 */
function logout_user(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Wymaga zalogowania – w przeciwnym wypadku przekierowuje do logowania.
 */
function require_login(): void {
    if (!current_user()) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Wymaga określonej roli (np. admin).
 */
function require_role(string $role): void {
    $user = current_user();
    if (!$user || $user['role'] !== $role) {
        header('HTTP/1.1 403 Forbidden');
        echo "Brak uprawnień.";
        exit;
    }
}
