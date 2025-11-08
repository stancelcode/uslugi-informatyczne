<?php
require_once __DIR__ . '/auth.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $pdo;

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = "Podaj adres e-mail i hasło.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Nieprawidłowy e-mail lub hasło.";
        } elseif ((int)$user['is_active'] !== 1) {
            $errors[] = "Konto użytkownika jest nieaktywne.";
        } elseif (!password_verify($password, $user['password_hash'])) {
            $errors[] = "Nieprawidłowy e-mail lub hasło.";
        } else {
            // SUKCES – logujemy
            login_user((int)$user['id']);

            // przekierowanie zależnie od roli
            if ($user['role'] === 'admin') {
                header('Location: /admin/dashboard.php');
            } elseif ($user['role'] === 'client') {
                header('Location: /client/dashboard.php');
            } else {
                header('Location: /');
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <title>Logowanie – panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <style>
    /* trochę zawężamy formularz */
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }
    .auth-card {
      width: 100%;
      max-width: 420px;
    }
    .auth-footer-link {
      font-size: 0.85rem;
      margin-top: 0.75rem;
      color: var(--text-muted);
    }
    .auth-footer-link a {
      color: var(--primary);
      text-decoration: underline;
      text-decoration-thickness: 1px;
    }
    /* dopilnujmy, że kropki hasła są jasne w trybie dark */
    input[type="password"] {
      color: var(--text);
    }
  </style>
</head>
<body>
  <!-- Pływający przycisk trybu dzień/noc -->
  <button
    class="btn btn-outline theme-toggle theme-toggle-floating"
    type="button"
    aria-label="Przełącz tryb jasny/ciemny"
    title="Przełącz tryb jasny/ciemny"
  >
    <span class="theme-icon">☀️</span>
  </button>

  <div class="auth-wrapper">
    <div class="auth-card card">
      <h2 class="section-title" style="margin-bottom:0.8rem;">
        <i class="fa-solid fa-right-to-bracket icon-left"></i>
        Logowanie
      </h2>
      <p class="section-description" style="max-width:none;margin-bottom:1rem;">
        Zaloguj się, aby przejść do panelu administratora lub strefy klienta.
      </p>

      <?php if ($errors): ?>
        <div class="error visible" style="margin-bottom:0.7rem;">
          <?php foreach ($errors as $e): ?>
            <div><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="field">
          <label for="email">
            <i class="fa-solid fa-envelope icon-left" aria-hidden="true"></i>
            Adres e-mail
          </label>
          <input
            type="email"
            id="email"
            name="email"
            required
            value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
          />
        </div>

        <div class="field">
          <label for="password">
            <i class="fa-solid fa-key icon-left" aria-hidden="true"></i>
            Hasło
          </label>
          <input
            type="password"
            id="password"
            name="password"
            required
          />
        </div>

        <div class="form-footer" style="margin-top:0.8rem;">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-right-to-bracket icon-left"></i>
            Zaloguj się
          </button>
          <a href="/" class="btn btn-outline">
            <i class="fa-solid fa-house icon-left"></i>
            Strona główna
          </a>
        </div>
      </form>

      <p class="auth-footer-link">
        Nie masz jeszcze konta?
        <a href="/register.php">Zarejestruj się</a>
      </p>
    </div>
  </div>

  <script src="/script.js"></script>
</body>
</html>
