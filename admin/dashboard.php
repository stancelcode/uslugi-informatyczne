<?php
require_once __DIR__ . '/../auth.php';
require_login();
require_role('admin');
global $pdo;

$user = current_user();

// --- obsługa statusów wiadomości ---
$msg_error = '';
$msg_success = '';

// --- obsługa dokumentów dla klientów ---
$doc_error = '';
$doc_success = '';

// pobieramy listę klientów (role='client')
$clients_stmt = $pdo->query("SELECT id, full_name, email FROM users WHERE role = 'client' ORDER BY full_name");
$clients = $clients_stmt->fetchAll();

// funkcja pomocnicza do etykiet statusu
function message_status_label(string $status): string {
    switch ($status) {
        case 'new':
            return 'Oczekujące';
        case 'in_progress':
            return 'Do realizacji';
        case 'done':
            return 'Zamknięte';
        case 'trash':
            return 'Kosz';
        default:
            return $status;
    }
}

// obsługa POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ZMIANA STATUSU WIADOMOŚCI
    if ($action === 'update_msg_status') {
        $msg_id     = (int)($_POST['msg_id'] ?? 0);
        $new_status = $_POST['new_status'] ?? '';

        $allowed_statuses = ['new','in_progress','done','trash'];

        if ($msg_id <= 0 || !in_array($new_status, $allowed_statuses, true)) {
            $msg_error = 'Nieprawidłowe dane statusu wiadomości.';
        } else {
            $stmt = $pdo->prepare("
                UPDATE contact_messages
                SET status = :status
                WHERE id = :id
            ");
            $stmt->execute([
                'status' => $new_status,
                'id'     => $msg_id,
            ]);
            $msg_success = 'Zaktualizowano status wiadomości.';
        }
    }

    // DODAWANIE DOKUMENTU DLA KLIENTA
    if ($action === 'add_document') {
        $client_id = (int)($_POST['client_id'] ?? 0);
        $title     = trim($_POST['title'] ?? '');

        if ($client_id <= 0 || $title === '') {
            $doc_error = 'Wybierz klienta i podaj tytuł dokumentu.';
        } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $doc_error = 'Wybierz plik do przesłania.';
        } else {
            $file      = $_FILES['file'];
            $file_name = $file['name'];
            $tmp_path  = $file['tmp_name'];

            $allowed_ext = ['pdf','doc','docx','xls','xlsx','txt','zip','tar','gz','png','jpg','jpeg'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_ext, true)) {
                $doc_error = 'Niedozwolony typ pliku. Dozwolone: ' . implode(', ', $allowed_ext);
            } else {
                $uploadDir = __DIR__ . '/../uploads/clients/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $newFileName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $destPath    = $uploadDir . $newFileName;
                $webPath     = '/uploads/clients/' . $newFileName;

                if (move_uploaded_file($tmp_path, $destPath)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO client_documents (user_id, title, file_path)
                        VALUES (:uid, :title, :path)
                    ");
                    $stmt->execute([
                        'uid'   => $client_id,
                        'title' => $title,
                        'path'  => $webPath,
                    ]);
                    $doc_success = 'Dokument został dodany.';
                } else {
                    $doc_error = 'Nie udało się zapisać pliku na serwerze.';
                }
            }
        }
    }
}

// Statystyki
$total_users_stmt    = $pdo->query("SELECT COUNT(*) AS cnt FROM users");
$total_users         = $total_users_stmt->fetch()['cnt'] ?? 0;

$total_msgs_stmt     = $pdo->query("SELECT COUNT(*) AS cnt FROM contact_messages");
$total_messages      = $total_msgs_stmt->fetch()['cnt'] ?? 0;

$total_views_stmt    = $pdo->query("SELECT COUNT(*) AS cnt FROM page_views");
$total_views         = $total_views_stmt->fetch()['cnt'] ?? 0;

// Ostatnie wiadomości – pokazujemy wszystkie statusy, najnowsze pierwsze
$last_messages_stmt  = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 20");
$last_messages       = $last_messages_stmt->fetchAll();

$docs_stmt           = $pdo->query("
    SELECT d.*, u.full_name
    FROM client_documents d
    JOIN users u ON d.user_id = u.id
    ORDER BY d.created_at DESC
    LIMIT 5
");
$client_docs         = $docs_stmt->fetchAll();
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
        <div class="logo-mark"></div>
        <span>Panel admina</span>
      </div>
      <div class="nav-actions">
        <span style="font-size:0.85rem;color:var(--text-muted);">
          Zalogowany: <?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <a href="/" class="btn btn-outline">
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
            <div class="section-kicker">Panel administracyjny</div>
            <h2 class="section-title">
              <i class="fa-solid fa-gauge-high icon-left"></i>
              Podsumowanie
            </h2>
          </div>
          <p class="section-description">
            Podstawowe statystyki i ostatnie aktywności na Twojej stronie: użytkownicy, wiadomości kontaktowe,
            odsłony i dokumenty klientów.
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

        <div class="content" style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1.1fr);gap:1.5rem;">
          <article class="card">
            <h3><i class="fa-solid fa-inbox icon-left"></i>Ostatnie wiadomości kontaktowe</h3>

            <?php if ($msg_error): ?>
              <p style="color:#f97316;font-size:0.85rem;margin-top:0.5rem;">
                <?php echo htmlspecialchars($msg_error, ENT_QUOTES, 'UTF-8'); ?>
              </p>
            <?php endif; ?>
            <?php if ($msg_success): ?>
              <p style="color:#22c55e;font-size:0.85rem;margin-top:0.5rem;">
                <?php echo htmlspecialchars($msg_success, ENT_QUOTES, 'UTF-8'); ?>
              </p>
            <?php endif; ?>

            <?php if (!$last_messages): ?>
              <p>Brak wiadomości w bazie.</p>
            <?php else: ?>
              <ul style="list-style:none;margin-top:0.8rem;padding-left:0;">
                <?php foreach ($last_messages as $msg): ?>
                  <li style="margin-bottom:0.75rem;border-bottom:1px solid rgba(148,163,184,0.3);padding-bottom:0.5rem;">
                    <strong><?php echo htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    (<?php echo htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8'); ?>)
                    <?php if (!empty($msg['phone'])): ?>
                      <br><small>Tel: <?php echo htmlspecialchars($msg['phone'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <?php endif; ?>
                    <br>
                    <small><?php echo htmlspecialchars($msg['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <br>
                    <span style="font-size:0.85rem;display:block;margin-top:0.25rem;">
                      <?php echo nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')); ?>
                    </span>

                    <div style="margin-top:0.35rem;font-size:0.8rem;">
                      Status:
                      <strong>
                        <?php echo htmlspecialchars(message_status_label($msg['status'] ?? 'new'), ENT_QUOTES, 'UTF-8'); ?>
                      </strong>
                    </div>

                    <form method="post"
                          style="margin-top:0.35rem;display:flex;gap:0.4rem;flex-wrap:wrap;align-items:center;">
                      <input type="hidden" name="action" value="update_msg_status">
                      <input type="hidden" name="msg_id" value="<?php echo (int)$msg['id']; ?>">

                      <select name="new_status"
                              style="border-radius:999px;border:1px solid rgba(148,163,184,0.6);padding:0.25rem 0.6rem;background:rgba(15,23,42,0.9);color:var(--text);font-size:0.78rem;">
                        <option value="new"        <?php echo ($msg['status']==='new'?'selected':''); ?>>Oczekujące</option>
                        <option value="in_progress"<?php echo ($msg['status']==='in_progress'?'selected':''); ?>>Do realizacji</option>
                        <option value="done"       <?php echo ($msg['status']==='done'?'selected':''); ?>>Zamknięte</option>
                        <option value="trash"      <?php echo ($msg['status']==='trash'?'selected':''); ?>>Kosz</option>
                      </select>

                      <button type="submit"
                              class="btn btn-outline"
                              style="padding:0.25rem 0.7rem;font-size:0.78rem;">
                        Zapisz
                      </button>
                    </form>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </article>

          <aside style="display:grid;gap:1.2rem;align-content:flex-start;">
            <div class="card">
              <h3><i class="fa-solid fa-folder-open icon-left"></i>Ostatnie dokumenty klientów</h3>
              <?php if (!$client_docs): ?>
                <p>Brak dokumentów w bazie.</p>
              <?php else: ?>
                <ul style="list-style:none;margin-top:0.8rem;padding-left:0;">
                  <?php foreach ($client_docs as $doc): ?>
                    <li style="margin-bottom:0.6rem;">
                      <strong><?php echo htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                      <small>Klient: <?php echo htmlspecialchars($doc['full_name'], ENT_QUOTES, 'UTF-8'); ?></small><br>
                      <small>Dodano: <?php echo htmlspecialchars($doc['created_at'], ENT_QUOTES, 'UTF-8'); ?></small><br>
                      <a href="<?php echo htmlspecialchars($doc['file_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                        Pobierz / otwórz
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>

            <div class="card">
              <h3><i class="fa-solid fa-file-circle-plus icon-left"></i>Dodaj dokument dla klienta</h3>
              <?php if ($doc_error): ?>
                <p style="color:#f97316;font-size:0.85rem;"><?php echo htmlspecialchars($doc_error, ENT_QUOTES, 'UTF-8'); ?></p>
              <?php endif; ?>
              <?php if ($doc_success): ?>
                <p style="color:#22c55e;font-size:0.85rem;"><?php echo htmlspecialchars($doc_success, ENT_QUOTES, 'UTF-8'); ?></p>
              <?php endif; ?>

              <?php if (!$clients): ?>
                <p>Brak klientów w bazie (role = client). Dodaj najpierw użytkowników-klientów w tabeli <code>users</code>.</p>
              <?php else: ?>
                <form method="post" enctype="multipart/form-data" style="display:grid;gap:0.6rem;margin-top:0.6rem;">
                  <input type="hidden" name="action" value="add_document">
                  <div class="field">
                    <label for="client_id">Klient</label>
                    <select id="client_id" name="client_id" required
                            style="border-radius:0.75rem;border:1px solid rgba(148,163,184,0.4);padding:0.45rem 0.6rem;background:rgba(15,23,42,0.9);color:var(--text);">
                      <option value="">– wybierz klienta –</option>
                      <?php foreach ($clients as $c): ?>
                        <option value="<?php echo (int)$c['id']; ?>">
                          <?php echo htmlspecialchars($c['full_name'] . ' (' . $c['email'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="field">
                    <label for="title">Tytuł dokumentu</label>
                    <input type="text" id="title" name="title" required placeholder="np. Raport z audytu bezpieczeństwa">
                  </div>

                  <div class="field">
                    <label for="file">Plik</label>
                    <input type="file" id="file" name="file" required>
                    <small>Dozwolone: pdf, doc, docx, xls, xlsx, txt, zip, tar, gz, png, jpg, jpeg.</small>
                  </div>

                  <div style="margin-top:0.4rem;">
                    <button type="submit" class="btn btn-primary">
                      <i class="fa-solid fa-upload icon-left"></i>
                      Dodaj dokument
                    </button>
                  </div>
                </form>
              <?php endif; ?>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </main>

  <script src="/script.js"></script>
</body>
</html>
