<?php
// register.php
require_once __DIR__ . '/auth.php';   // auth.php wczyta też db.php i session_start()

global $pdo;

$errors = [];
$values = [
    'full_name' => '',
    'email'     => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['full_name'] = trim($_POST['full_name'] ?? '');
    $values['email']     = trim($_POST['email'] ?? '');
    $password            = $_POST['password'] ?? '';
    $password_confirm    = $_POST['password_confirm'] ?? '';

    // Walidacja podstawowa
    if ($values['full_name'] === '') {
        $errors['full_name'] = 'Podaj swoje imię i nazwisko.';
    }

    if ($values['email'] === '' || !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Podaj poprawny adres e-mail.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Hasło musi mieć co najmniej 8 znaków.';
    }

    if ($password !== $password_confirm) {
        $errors['password_confirm'] = 'Hasła muszą być identyczne.';
    }

    // Sprawdź, czy e-mail nie jest już zajęty
    if (!isset($errors['email'])) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $values['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Konto z takim adresem e-mail już istnieje.';
        }
    }

    // Jeśli brak błędów – tworzymy użytkownika
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, password_hash, role, is_active, created_at)
            VALUES (:full_name, :email, :password_hash, 'client', 1, NOW())
        ");

        $stmt->execute([
            'full_name'     => $values['full_name'],
            'email'         => $values['email'],
            'password_hash' => $hash,
        ]);

        $userId = (int) $pdo->lastInsertId();

        // Auto-logowanie po rejestracji
        $_SESSION['user_id'] = $userId;

        // Przekierowanie do panelu klienta (lub na stronę główną – jak wolisz)
        header('Location: /client/dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <title>Rejestracja – Kamil Kaczmarczyk IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <style>
    html, body { overflow-x: hidden; }
    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .auth-card {
      max-width: 420px;
      width: 100%;
    }
    .auth-card h1 {
      font-size: 1.4rem;
      margin-bottom: 0.4rem;
    }
    .auth-card p {
      font-size: 0.88rem;
      color: var(--text-muted);
      margin-bottom: 1rem;
    }
    .auth-error {
      color: var(--accent);
      font-size: 0.8rem;
      margin-top: 0.2rem;
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
    <article class="card auth-card">
      <h1>
        <i class="fa-solid fa-user-plus icon-left"></i>
        Załóż konto klienta
      </h1>
      <p>
        Po rejestracji otrzymasz dostęp do panelu klienta, gdzie będziesz mógł
        przeglądać swoje dokumenty, oferty i materiały przygotowane dla Twojej firmy.
      </p>

      <form method="post" novalidate>
        <div class="field">
          <label for="full_name">
            <i class="fa-solid fa-user icon-left" aria-hidden="true"></i>
            Imię i nazwisko<span class="required">*</span>
          </label>
          <input
            type="text"
            id="full_name"
            name="full_name"
            required
            value="<?php echo htmlspecialchars($values['full_name'], ENT_QUOTES, 'UTF-8'); ?>"
          >
          <?php if (isset($errors['full_name'])): ?>
            <div class="auth-error"><?php echo htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="email">
            <i class="fa-solid fa-envelope icon-left" aria-hidden="true"></i>
            Adres e-mail<span class="required">*</span>
          </label>
          <input
            type="email"
            id="email"
            name="email"
            required
            value="<?php echo htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8'); ?>"
          >
          <?php if (isset($errors['email'])): ?>
            <div class="auth-error"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="password">
            <i class="fa-solid fa-lock icon-left" aria-hidden="true"></i>
            Hasło<span class="required">*</span>
          </label>
          <input
            type="password"
            id="password"
            name="password"
            required
            minlength="8"
            placeholder="min. 8 znaków"
          >
          <?php if (isset($errors['password'])): ?>
            <div class="auth-error"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="password_confirm">
            <i class="fa-solid fa-lock icon-left" aria-hidden="true"></i>
            Powtórz hasło<span class="required">*</span>
          </label>
          <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            required
          >
          <?php if (isset($errors['password_confirm'])): ?>
            <div class="auth-error"><?php echo htmlspecialchars($errors['password_confirm'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>

        <div class="form-footer" style="margin-top:0.8rem;">
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-user-plus icon-left"></i>
            Zarejestruj
          </button>
          <p>
            Rejestrując konto, akceptujesz kontakt w sprawach związanych ze
            współpracą i usługami IT.
          </p>
        </div>
      </form>

      <p style="margin-top:1rem;font-size:0.85rem;color:var(--text-muted);">
        Masz już konto?
        <a href="/login.php" style="text-decoration:underline;">Zaloguj się</a>
      </p>
    </article>
  </div>

  <script src="/script.js"></script>
</body>
</html>
