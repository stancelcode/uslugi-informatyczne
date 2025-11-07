<?php
require_once __DIR__ . '/../auth.php';
require_login();
$user = current_user();
global $pdo;

$stmt = $pdo->prepare("SELECT * FROM client_documents WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user['id']]);
$docs = $stmt->fetchAll();
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
</head>
<body>
  <header>
    <div class="container nav">
      <div class="logo">
        <div class="logo-mark"></div>
        <span>Panel klienta</span>
      </div>
      <div class="nav-actions">
        <span style="font-size:0.85rem;color:var(--text-muted);">
          Zalogowany: <?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <a href="/" class="btn btn-outline">Strona główna</a>
        <a href="/logout.php" class="btn btn-primary">Wyloguj</a>
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
            W tym miejscu możesz pobrać dokumentację, raporty oraz inne materiały
            przygotowane dla Twojej firmy.
          </p>
        </div>

        <article class="card">
          <h3><i class="fa-solid fa-file-lines icon-left"></i>Twoje dokumenty</h3>
          <?php if (!$docs): ?>
            <p>Na razie nie dodano żadnych dokumentów do Twojego konta.</p>
          <?php else: ?>
            <ul style="list-style:none;margin-top:0.8rem;padding-left:0;">
              <?php foreach ($docs as $doc): ?>
                <li style="margin-bottom:0.6rem;border-bottom:1px solid rgba(148,163,184,0.3);padding-bottom:0.4rem;">
                  <strong><?php echo htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                  <small>Dodano: <?php echo htmlspecialchars($doc['created_at'], ENT_QUOTES, 'UTF-8'); ?></small><br>
                  <a href="<?php echo htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    Pobierz / otwórz
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </article>

        <div class="cta-strip" style="margin-top:1.5rem;">
          <div>
            <strong>Potrzebujesz zmian w konfiguracji lub nowego projektu?</strong><br>
            Skorzystaj z formularza kontaktowego na stronie głównej i zaznacz w treści,
            że piszesz jako stały klient.
          </div>
          <a href="/#kontakt" class="btn">
            <i class="fa-solid fa-comments icon-left"></i>
            Przejdź do formularza
          </a>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
