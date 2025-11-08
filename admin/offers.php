<?php
// admin/offers.php – zarządzanie ofertami i dostępem

require_once __DIR__ . '/../auth.php';

global $pdo;

$currentUser = current_user();
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

$errors = [];
$info   = [];

/* --- Dodawanie nowej oferty --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_offer') {
    $title   = trim($_POST['title'] ?? '');
    $slug    = trim($_POST['slug'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $slug === '' || $content === '') {
        $errors[] = 'Uzupełnij tytuł, slug i treść oferty.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO offers (title, slug, content)
                VALUES (:title, :slug, :content)
            ");
            $stmt->execute([
                'title'   => $title,
                'slug'    => $slug,
                'content' => $content,
            ]);
            $info[] = 'Oferta została utworzona.';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Slug musi być unikalny (taka oferta już istnieje).';
            } else {
                $errors[] = 'Błąd podczas zapisu oferty.';
            }
        }
    }
}

/* --- Nadawanie dostępu użytkownikowi --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'grant_access') {
    $userId  = (int) ($_POST['user_id'] ?? 0);
    $offerId = (int) ($_POST['offer_id'] ?? 0);

    if ($userId <= 0 || $offerId <= 0) {
        $errors[] = 'Wybierz użytkownika i ofertę.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT IGNORE INTO offer_access (user_id, offer_id)
                VALUES (:uid, :oid)
            ");
            $stmt->execute([
                'uid' => $userId,
                'oid' => $offerId,
            ]);
            $info[] = 'Dostęp został nadany (lub już istniał).';
        } catch (PDOException $e) {
            $errors[] = 'Błąd podczas nadawania dostępu.';
        }
    }
}

/* --- Usuwanie dostępu --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'revoke_access') {
    $accessId = (int) ($_POST['access_id'] ?? 0);
    if ($accessId > 0) {
        $stmt = $pdo->prepare("DELETE FROM offer_access WHERE id = :id");
        $stmt->execute(['id' => $accessId]);
        $info[] = 'Dostęp został usunięty.';
    }
}

/* Pobranie ofert i użytkowników do formularzy */
$offers = $pdo->query("SELECT * FROM offers ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$users  = $pdo->query("
    SELECT id, email, role, full_name
    FROM users 
    ORDER BY role DESC, email ASC
")->fetchAll(PDO::FETCH_ASSOC);

/* --- Filtr użytkownika dla listy dostępów --- */
$filterUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

/* Lista aktualnych dostępów (do podglądu) */
if ($filterUserId > 0) {
    $stmt = $pdo->prepare("
        SELECT oa.id,
               u.email,
               u.full_name,
               u.role,
               o.title,
               o.slug,
               oa.created_at
        FROM offer_access oa
        JOIN users u  ON u.id = oa.user_id
        JOIN offers o ON o.id = oa.offer_id
        WHERE oa.user_id = :uid
        ORDER BY oa.created_at DESC
    ");
    $stmt->execute(['uid' => $filterUserId]);
    $accessList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $accessList = $pdo->query("
        SELECT oa.id,
               u.email,
               u.full_name,
               u.role,
               o.title,
               o.slug,
               oa.created_at
        FROM offer_access oa
        JOIN users u  ON u.id = oa.user_id
        JOIN offers o ON o.id = oa.offer_id
        ORDER BY oa.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Oferty – panel admina</title>
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
      <span>Panel admina</span>
    </div>
    <nav>
      <ul>
        <li><a href="/admin/dashboard.php">Dashboard</a></li>
        <li><a href="/admin/offers.php">Oferty</a></li>
      </ul>
    </nav>
    <div class="nav-actions">
      <a href="/index.php" class="btn btn-outline">Strona główna</a>
      <a href="/logout.php" class="btn btn-primary">Wyloguj</a>
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
            <i class="fa-solid fa-file-signature icon-left"></i>
            Zarządzanie ofertami i dostępem
          </h1>
        </div>
      </div>

      <?php if ($errors): ?>
        <div class="card" style="border-color:#f97316;color:#fecaca;margin-bottom:0.8rem;">
          <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($info): ?>
        <div class="card" style="border-color:#22c55e;color:#bbf7d0;margin-bottom:0.8rem;">
          <?php foreach ($info as $m): ?>
            <p><?= htmlspecialchars($m) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="content" style="display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1.1fr);gap:1.5rem;">
        <!-- Tworzenie nowej oferty -->
        <article class="card">
          <h2 style="margin-bottom:0.6rem;font-size:1.05rem;">
            <i class="fa-solid fa-plus icon-left"></i>
            Nowa oferta
          </h2>
          <form method="post" style="display:grid;gap:0.6rem;">
            <input type="hidden" name="action" value="create_offer">

            <div class="field">
              <label for="title">Tytuł oferty</label>
              <input type="text" id="title" name="title" required>
            </div>

            <div class="field">
              <label for="slug">Slug (do URL, np. oferta-firma-xyz)</label>
              <input type="text" id="slug" name="slug" required>
            </div>

            <div class="field">
              <label for="content">Treść (HTML lub tekst)</label>
              <textarea id="content" name="content" rows="8" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-floppy-disk icon-left"></i>
              Zapisz ofertę
            </button>
          </form>
        </article>

        <!-- Nadawanie dostępu -->
        <article class="card">
          <h2 style="margin-bottom:0.6rem;font-size:1.05rem;">
            <i class="fa-solid fa-user-lock icon-left"></i>
            Nadawanie dostępu
          </h2>
          <form method="post" style="display:grid;gap:0.6rem;">
            <input type="hidden" name="action" value="grant_access">

            <div class="field">
              <label for="user_id">Użytkownik</label>
              <select id="user_id" name="user_id" class="status-select">
                <option value="">-- wybierz --</option>
                <?php foreach ($users as $u): ?>
                  <option value="<?= $u['id'] ?>">
                    (<?= htmlspecialchars($u['role']) ?>)
                    <?= htmlspecialchars($u['full_name'] ?: $u['email']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="field">
              <label for="offer_id">Oferta</label>
              <select id="offer_id" name="offer_id" class="status-select">
                <option value="">-- wybierz --</option>
                <?php foreach ($offers as $o): ?>
                  <option value="<?= $o['id'] ?>">
                    <?= htmlspecialchars($o['title']) ?> (<?= htmlspecialchars($o['slug']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-key icon-left"></i>
              Nadaj dostęp
            </button>
          </form>
        </article>
      </div>

      <!-- Aktualne dostępy -->
      <section style="margin-top:1.8rem;">
        <div class="card">
          <h2 style="margin-bottom:0.8rem;font-size:1.02rem;">
            <i class="fa-solid fa-list-check icon-left"></i>
            Aktualne dostępy
          </h2>

          <!-- FILTR PO UŻYTKOWNIKU -->
          <form method="get" style="margin-bottom:0.8rem;display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
            <label style="font-size:0.85rem;">
              Pokaż dostępy dla:
              <select name="user_id" class="status-select" onchange="this.form.submit()">
                <option value="0">wszyscy użytkownicy</option>
                <?php foreach ($users as $u): ?>
                  <option value="<?= $u['id'] ?>" <?= $filterUserId === (int)$u['id'] ? 'selected' : '' ?>>
                    (<?= htmlspecialchars($u['role']) ?>)
                    <?= htmlspecialchars($u['full_name'] ?: $u['email']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>

            <?php if ($filterUserId > 0): ?>
              <a href="/admin/offers.php"
                 class="btn btn-outline"
                 style="padding:0.25rem 0.7rem;font-size:0.78rem;">
                Wyczyść filtr
              </a>
            <?php endif; ?>
          </form>

          <?php if (!$accessList): ?>
            <p style="font-size:0.9rem;">Brak przypisanych dostępów dla wybranego filtra.</p>
          <?php else: ?>
            <div style="overflow-x:auto;">
              <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                <thead>
                  <tr>
                    <th style="text-align:left;padding:0.4rem;">Użytkownik</th>
                    <th style="text-align:left;padding:0.4rem;">Rola</th>
                    <th style="text-align:left;padding:0.4rem;">Oferta</th>
                    <th style="text-align:left;padding:0.4rem;">Slug</th>
                    <th style="text-align:left;padding:0.4rem;">Data</th>
                    <th style="text-align:left;padding:0.4rem;">Akcje</th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($accessList as $row): ?>
                  <tr>
                    <td style="padding:0.35rem 0.4rem;">
                      <?= htmlspecialchars(($row['full_name'] ?: $row['email']), ENT_QUOTES, 'UTF-8') ?><br>
                      <small><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></small>
                    </td>
                    <td style="padding:0.35rem 0.4rem;"><?= htmlspecialchars($row['role']) ?></td>
                    <td style="padding:0.35rem 0.4rem;"><?= htmlspecialchars($row['title']) ?></td>
                    <td style="padding:0.35rem 0.4rem;"><?= htmlspecialchars($row['slug']) ?></td>
                    <td style="padding:0.35rem 0.4rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                    <td style="padding:0.35rem 0.4rem;">
                      <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="revoke_access">
                        <input type="hidden" name="access_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.6rem;font-size:0.75rem;">
                          Usuń
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </section>

    </div>
  </section>
</main>

<script src="/script.js"></script>
</body>
</html>
