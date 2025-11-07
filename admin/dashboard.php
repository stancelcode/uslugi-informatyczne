<?php
require_once __DIR__ . '/../auth.php';
require_login();
require_role('admin');
global $pdo;

// Statystyki
$total_users_stmt    = $pdo->query("SELECT COUNT(*) AS cnt FROM users");
$total_users         = $total_users_stmt->fetch()['cnt'] ?? 0;

$total_msgs_stmt     = $pdo->query("SELECT COUNT(*) AS cnt FROM contact_messages");
$total_messages      = $total_msgs_stmt->fetch()['cnt'] ?? 0;

$total_views_stmt    = $pdo->query("SELECT COUNT(*) AS cnt FROM page_views");
$total_views         = $total_views_stmt->fetch()['cnt'] ?? 0;

$last_messages_stmt  = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$last_messages       = $last_messages_stmt->fetchAll();

$docs_stmt           = $pdo->query("SELECT d.*, u.full_name FROM client_documents d JOIN users u ON d.user_id = u.id ORDER BY d.created_at DESC LIMIT 5");
$client_docs         = $docs_stmt->fetchAll();

$user = current_user();
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <title>Panel administratora</title>
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
        <span>Panel admina</span>
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
            <div class="section-kicker">Panel administracyjny</div>
            <h2 class="section-title">
              <i class="fa-solid fa-gauge-high icon-left"></i>
              Podsumowanie
            </h2>
          </div>
          <p class="section-description">
            Podstawowe statystyki i ostatnie aktywności z Twojej strony: użytkownicy, wiadomości kontaktowe oraz odsłony.
          </p>
        </div>

        <div class="hero-stats" style="margin-bottom:1.5rem;">
          <div class="stat">
            <strong><i class="fa-solid fa-users icon-left"></i><?php echo (int)$total_users; ?></strong>
            Zarejestrowanych użytkowników (admin + klienci)
          </div>
          <div class="stat">
            <strong><i class="fa-solid fa-envelope icon-left"></i><?php echo (int)$total_messages; ?></strong>
            Wiadomości z formularza kontaktowego
          </div>
          <div class="stat">
            <strong><i class="fa-solid fa-eye icon-left"></i><?php echo (int)$total_views; ?></strong>
            Zarejestrowanych odsłon strony
          </div>
          <div class="stat">
            <strong><i class="fa-solid fa-file-arrow-down icon-left"></i><?php echo count($client_docs); ?></strong>
            Ostatnie dodane dokumenty dla klientów
          </div>
        </div>

        <div class="content" style="display:grid;grid-template-columns:minmax(0,1.2fr) minmax(0,1fr);gap:1.5rem;">
          <article class="card">
            <h3><i class="fa-solid fa-inbox icon-left"></i>Ostatnie wiadomości kontaktowe</h3>
            <?php if (!$last_messages): ?>
              <p>Brak wiadomości w bazie.</p>
            <?php else: ?>
              <ul style="list-style:none;margin-top:0.8rem;padding-left:0;">
                <?php foreach ($last_messages as $msg): ?>
                  <li style="margin-bottom:0.75rem;border-bottom:1px solid rgba(148,163,184,0.3);padding-bottom:0.5rem;">
                    <strong><?php echo htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    (<?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>)
                    <br>
                    <small><?php echo htmlspecialchars($msg['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <br>
                    <span style="font-size:0.85rem;">
                      <?php echo nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')); ?>
                    </span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </article>

          <aside class="card">
            <h3><i class="fa-solid fa-folder-open icon-left"></i>Ostatnie dokumenty klientów</h3>
            <?php if (!$client_docs): ?>
              <p>Brak dokumentów w bazie.</p>
            <?php else: ?>
              <ul style="list-style:none;margin-top:0.8rem;padding-left:0;">
                <?php foreach ($client_docs as $doc): ?>
                  <li style="margin-bottom:0.6rem;">
                    <strong><?php echo htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <br>
                    <small>Klient: <?php echo htmlspecialchars($doc['full_name'], ENT_QUOTES, 'UTF-8'); ?></small><br>
                    <small>Dodano: <?php echo htmlspecialchars($doc['created_at'], ENT_QUOTES, 'UTF-8'); ?></small><br>
                    <a href="<?php echo htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                      Pobierz / otwórz
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </aside>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
