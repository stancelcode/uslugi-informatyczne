<?php
require_once __DIR__ . '/../auth.php';

require_login();
$user = current_user();
global $pdo;

$stmt = $pdo->prepare("
  SELECT *
  FROM client_documents
  WHERE user_id = :uid
  ORDER BY created_at DESC
");
$stmt->execute(['uid' => $user['id']]);
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <title>Panel klienta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <!-- na wszelki wypadek wyłącz poziomy scroll -->
  <style>
    html, body { overflow-x: hidden; }
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
        <span>Panel klienta</span>
      </div>

      <nav aria-label="Nawigacja panelu klienta">
        <ul>
          <li><a href="/my_offers.php">Moje oferty</a></li>
          <li><a href="/index.php#kontakt">Kontakt</a></li>
        </ul>
      </nav>

      <div class="nav-actions">
        <span style="font-size:0.85rem;color:var(--text-muted);">
          Zalogowany:
          <?php echo htmlspecialchars($user['full_name'] ?? $user['email'], ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <a href="/index.php" class="btn btn-outline">
          <i class="fa-solid fa-house icon-left"></i> Strona główna
        </a>
        <a href="/logout.php" class="btn btn-primary">
          <i class="fa-solid fa-right-from-bracket icon-left"></i> Wyloguj
        </a>
      </div>
    </div>
  </header>

  <main>
    <section>
      <div class="container">
        <div class="section-header">
          <div>
            <div class="section-kicker">Strefa klienta</div>
            <h2 class="section-title">
              <i class="fa-solid fa-folder-shield icon-left"></i>
              Twoje materiały i dokumentacja
            </h2>
          </div>
          <p class="section-description">
            Tutaj znajdziesz dokumentację, raporty oraz inne materiały przygotowane
            indywidualnie dla Twojej firmy.
          </p>
        </div>

        <article class="card">
          <h3>
            <i class="fa-solid fa-file-lines icon-left"></i>
            Twoje dokumenty
          </h3>

          <?php if (!$docs): ?>
            <p style="margin-top:0.6rem;font-size:0.9rem;color:var(--text-muted);">
              Na razie nie dodano żadnych dokumentów do Twojego konta.
            </p>
          <?php else: ?>
            <ul style="list-style:none;margin-top:0.9rem;padding-left:0;display:grid;gap:0.6rem;">
              <?php foreach ($docs as $doc): ?>
                <li
                  style="
                    border-radius:0.75rem;
                    border:1px solid var(--border-subtle);
                    padding:0.6rem 0.7rem;
                    font-size:0.86rem;
                  "
                >
                  <strong>
                    <?php echo htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?>
                  </strong><br>
                  <small style="color:var(--text-muted);">
                    Dodano:
                    <?php echo htmlspecialchars($doc['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                  </small><br>
                  <a
                    href="<?php echo htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8'); ?>"
                    target="_blank"
                    class="btn btn-outline"
                    style="margin-top:0.35rem;padding:0.25rem 0.7rem;font-size:0.8rem;"
                  >
                    <i class="fa-solid fa-download icon-left"></i>
                    Pobierz / otwórz
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </article>

        <div class="cta-strip" style="margin-top:1.5rem;">
          <div>
            <strong>Masz dedykowaną ofertę lub materiały?</strong><br>
            Sprawdź także zakładkę „Moje oferty” – tam znajdziesz przygotowane specjalnie
            dla Ciebie propozycje współpracy.
          </div>
          <div style="display:flex;gap:0.6rem;flex-wrap:wrap;">
            <a href="/my_offers.php" class="btn">
              <i class="fa-solid fa-file-signature icon-left"></i>
              Przejdź do ofert
            </a>
            <a href="/index.php#kontakt" class="btn">
              <i class="fa-solid fa-comments icon-left"></i>
              Formularz kontaktowy
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="/script.js"></script>
</body>
</html>
