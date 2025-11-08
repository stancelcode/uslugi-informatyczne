<?php
// my_offers.php – lista ofert dostępnych dla zalogowanego użytkownika

require_once __DIR__ . '/auth.php';

global $pdo;

$currentUser = current_user();
if (!$currentUser) {
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/my_offers.php');
    header("Location: /login.php?redirect={$redirect}");
    exit;
}

if ($currentUser['role'] === 'admin') {
    // admin widzi wszystkie oferty
    $stmt = $pdo->query("SELECT * FROM offers ORDER BY created_at DESC");
} else {
    // klient widzi tylko oferty, do których ma dostęp
    $stmt = $pdo->prepare("
        SELECT o.*
        FROM offers o
        JOIN offer_access oa ON oa.offer_id = o.id
        WHERE oa.user_id = :uid
        ORDER BY o.created_at DESC
    ");
    $stmt->execute(['uid' => $currentUser['id']]);
}

$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Moje oferty</title>
  <link rel="stylesheet" href="/style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
</head>
<body>

<header>
  <div class="container nav">
    <div class="logo">
      <div class="logo-mark" aria-hidden="true"></div>
      <span>Kamil Kaczmarczyk</span>
    </div>

    <nav aria-label="Główna nawigacja">
      <ul>
        <li><a href="/index.php#oferta">Usługi</a></li>
        <li><a href="/index.php#kontakt">Kontakt</a></li>
      </ul>
    </nav>

    <div class="nav-actions">
      <?php if ($currentUser['role'] === 'admin'): ?>
        <a href="/admin/dashboard.php" class="btn btn-outline">
          <i class="fa-solid fa-gauge-high icon-left"></i>
          Panel admina
        </a>
      <?php else: ?>
        <a href="/client/dashboard.php" class="btn btn-outline">
          <i class="fa-solid fa-folder-open icon-left"></i>
          Panel klienta
        </a>
      <?php endif; ?>

      <a href="/index.php" class="btn btn-primary">Strona główna</a>
    </div>
  </div>
</header>

<button
  class="btn btn-outline theme-toggle theme-toggle-floating"
  type="button"
  aria-label="Przełącz tryb jasny/ciemny"
  title="Przełącz tryb jasny/ciemny"
>
  <span class="theme-icon">☀️</span>
</button>

<main>
  <section>
    <div class="container">
      <div class="section-header">
        <div>
          <div class="section-kicker">Oferty</div>
          <h1 class="section-title">
            <i class="fa-solid fa-file-lines icon-left"></i>
            Twoje indywidualne oferty
          </h1>
        </div>
      </div>

      <div class="content">
        <?php if (empty($offers)): ?>
          <div class="card">
            <p>Nie masz aktualnie przypisanych ofert.</p>
          </div>
        <?php else: ?>
          <div class="values-grid">
            <?php foreach ($offers as $offer): ?>
              <div>
                <strong><?= htmlspecialchars($offer['title']) ?></strong><br>
                <a
                  href="/offer.php?slug=<?= urlencode($offer['slug']) ?>"
                  class="btn btn-outline"
                  style="margin-top:0.4rem;"
                >
                  <i class="fa-solid fa-eye icon-left"></i>
                  Zobacz ofertę
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="container footer-inner">
    <div>© <span id="year"></span> Kamil Kaczmarczyk. Wszystkie prawa zastrzeżone.</div>
  </div>
</footer>

<script src="/script.js"></script>
</body>
</html>
