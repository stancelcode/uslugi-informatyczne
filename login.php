<?php
require_once __DIR__ . '/auth.php';
global $pdo;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Podaj adres e-mail i hasło.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Nieprawidłowy adres e-mail lub hasło.';
        } elseif (isset($user['is_active']) && !$user['is_active']) {
            $errors[] = 'Konto jest nieaktywne. Skontaktuj się z administratorem.';
        } else {
            // logowanie OK
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_role'] = $user['role'];

            // przekierowanie wg roli
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
    >
    <style>
      html, body { overflow-x: hidden; }
      .auth-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .auth-card {
        max-width: 420px;
        width: 100%;
      }
      .auth-errors {
        margin-bottom: 0.8rem;
        padding: 0.6rem 0.8rem;
        border-radius: 0.75rem;
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.55);
        font-size: 0.85rem;
        color: #fecaca;
      }
      :root[data-theme="light"] .auth-errors {
        background: #fee2e2;
        color: #7f1d1d;
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

  <header>
    <div class="container nav">
      <div class="logo">
        <div class="logo-mark" aria-hidden="true"></div>
        <span>Logowanie</span>
      </div>
      <div class="nav-actions">
        <a href="/" class="btn btn-outline">
          <i class="fa-solid fa-house icon-left"></i> Strona główna
        </a>
      </div>
    </div>
  </header>

  <main class="auth-wrapper">
    <div class="container auth-card">
      <article class="card">
        <h2 class="section-title" style="margin-bottom:0.75rem;">
          <i class="fa-solid fa-right-to-bracket icon-left"></i>
          Zaloguj się
        </h2>
        <p class="section-description" style="margin-bottom:1rem;max-width:none;">
          Podaj swój adres e-mail i hasło, aby przejść do panelu klienta lub panelu administratora.
        </p>

        <?php if ($errors): ?>
          <div class="auth-errors">
            <?php foreach ($errors as $err): ?>
              <div><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
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
              value="<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>"
            >
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
            >
          </div>

          <div class="form-footer" style="margin-top:0.8rem;">
            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-right-to-bracket icon-left"></i>
              Zaloguj się
            </button>
          </div>
        </form>

        <p style="margin-top:1rem;font-size:0.85rem;color:var(--text-muted);">
          Nie masz jeszcze konta?
          <a href="/register.php" style="color:var(--primary);text-decoration:underline;">
            Zarejestruj się
          </a>
        </p>
      </article>
    </div>
  </main>

  <script src="/script.js"></script>
</body>
</html>
