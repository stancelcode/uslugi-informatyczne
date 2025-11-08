<?php
// offer.php – wyświetlanie pojedynczej oferty po zalogowaniu i z uprawnieniem

require_once __DIR__ . '/auth.php';   // ładuje też db.php

global $pdo;

// użytkownik musi być zalogowany
$currentUser = current_user();
if (!$currentUser) {
    // opcjonalnie przekazujemy redirect, żeby po zalogowaniu wrócić do tej oferty
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/');
    header("Location: /login.php?redirect={$redirect}");
    exit;
}

// slug z URL, np. offer.php?slug=oferta-firma-xyz
$slug = $_GET['slug'] ?? '';
$slug = trim($slug);

if ($slug === '') {
    http_response_code(400);
    echo "Brak parametru 'slug'.";
    exit;
}

// pobierz ofertę po slug
$stmt = $pdo->prepare("SELECT * FROM offers WHERE slug = :slug LIMIT 1");
$stmt->execute(['slug' => $slug]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    http_response_code(404);
    echo "Taka oferta nie istnieje.";
    exit;
}

// sprawdzenie uprawnień:
// admin widzi wszystko, zwykły user tylko jeśli ma wpis w offer_access
$hasAccess = false;

if ($currentUser['role'] === 'admin') {
    $hasAccess = true;
} else {
    $stmt = $pdo->prepare("
        SELECT 1 
        FROM offer_access 
        WHERE user_id = :uid AND offer_id = :oid
        LIMIT 1
    ");
    $stmt->execute([
        'uid' => $currentUser['id'],
        'oid' => $offer['id'],
    ]);
    $hasAccess = (bool) $stmt->fetchColumn();
}

if (!$hasAccess) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="pl" data-theme="dark">
    <head>
        <meta charset="UTF-8">
        <title>Brak dostępu do oferty</title>
        <link rel="stylesheet" href="/style.css">
    </head>
    <body>
      <main style="min-height:100vh;display:flex;align-items:center;justify-content:center;">
        <div class="card" style="max-width:540px;text-align:center;">
          <h1 style="margin-bottom:0.6rem;">Brak dostępu</h1>
          <p style="font-size:0.9rem;">
            Nie masz uprawnień do wyświetlenia tej oferty.
          </p>
          <div style="margin-top:1rem;display:flex;justify-content:center;gap:0.75rem;flex-wrap:wrap;">
            <a href="/index.php" class="btn btn-outline">Powrót na stronę główną</a>
            <?php if ($currentUser['role'] === 'client'): ?>
              <a href="/client/dashboard.php" class="btn btn-primary">Panel klienta</a>
            <?php elseif ($currentUser['role'] === 'admin'): ?>
              <a href="/admin/dashboard.php" class="btn btn-primary">Panel admina</a>
            <?php endif; ?>
          </div>
        </div>
      </main>
    </body>
    </html>
    <?php
    exit;
}

/* --- NOWOŚĆ: jeśli oferta ma zewnętrzny URL, przekieruj tam --- */
if (!empty($offer['external_url'])) {
    header('Location: ' . $offer['external_url']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Oferta – <?= htmlspecialchars($offer['title']) ?></title>
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
        <li><a href="/index.php#onas">O nas</a></li>
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

      <a href="/index.php" class="btn btn-primary">
        Strona główna
      </a>
    </div>
  </div>
</header>

<!-- pływający przełącznik D/N, jak w index.php -->
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
          <div class="section-kicker">Oferta indywidualna</div>
          <h1 class="section-title">
            <i class="fa-solid fa-file-signature icon-left"></i>
            <?= htmlspecialchars($offer['title']) ?>
          </h1>
        </div>
      </div>

      <article class="card">
        <!-- Możesz w polu content trzymać HTML – wtedy nie używaj htmlspecialchars -->
        <?= $offer['content'] ?>
      </article>
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
