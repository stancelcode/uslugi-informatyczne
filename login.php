<?php
require_once __DIR__ . '/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Podaj adres e-mail i hasło.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Podaj poprawny adres e-mail.';
    } else {
        if (login_user($email, $password)) {
            $user = current_user();
            if ($user['role'] === 'admin') {
                header('Location: /admin/dashboard.php');
            } else {
                header('Location: /client/dashboard.php');
            }
            exit;
        } else {
            $error = 'Nieprawidłowy e-mail lub hasło.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <title>Logowanie – Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
</head>
<body>
  <main>
    <section class="hero" style="padding-top:3rem;">
      <div class="container" style="max-width:480px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
          <h1 style="font-size:1.4rem;">Logowanie do panelu</h1>
          <a href="/" class="btn btn-outline" style="white-space:nowrap;">
            <i class="fa-solid fa-house icon-left"></i>
            Strona główna
          </a>
        </div>

        <div class="card">
          <p style="font-size:0.9rem;color:var(--text-muted);margin-bottom:1rem;">
            Zaloguj się, aby przejść do panelu administracyjnego lub strefy klienta.
          </p>

          <?php if ($error): ?>
            <div style="color:#f97316;font-size:0.85rem;margin-bottom:0.75rem;">
              <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>

          <form method="post" novalidate>
            <div class="field">
              <label for="email">
                <i class="fa-solid fa-envelope icon-left"></i>
                Adres e-mail
              </label>
              <input type="email" id="email" name="email" required>
            </div>

            <div class="field">
              <label for="password">
                <i class="fa-solid fa-key icon-left"></i>
                Hasło
              </label>
              <input type="password" id="password" name="password" required>
            </div>

            <div class="form-footer" style="margin-top:0.8rem;">
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-right-to-bracket icon-left"></i>
                Zaloguj się
              </button>
              <p>Jeśli nie masz konta, skontaktuj się z administratorem.</p>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
