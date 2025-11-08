<?php
require_once __DIR__ . '/auth.php';   // auth.php wczyta też db.php

$path = $_SERVER['REQUEST_URI'] ?? '/';
$ip   = $_SERVER['REMOTE_ADDR'] ?? null;
$ua   = $_SERVER['HTTP_USER_AGENT'] ?? null;

global $pdo;
$stmt = $pdo->prepare("
  INSERT INTO page_views (path, ip, user_agent)
  VALUES (:path, :ip, :ua)
");
$stmt->execute([
  'path' => $path,
  'ip'   => $ip,
  'ua'   => $ua,
]);

$currentUser = current_user();
?>
<!DOCTYPE html>
<html lang="pl" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Usługi Informatyczne Kamil Kaczmarczyk – Specjalista IT</title>
  <meta
    name="description"
    content="Specjalista IT w administracji systemów Linux, automatyzacji procesów (DevOps), monitoringu i bezpieczeństwie infrastruktury dla firm."
  />
  <link rel="stylesheet" href="style.css" />
  <!-- Font Awesome do ikon -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <!-- Blokada poziomego scrolla, gdy coś minimalnie wystaje poza viewport -->
  <style>
    html, body {
      overflow-x: hidden;
    }
  </style>
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
          <li><a href="#onas">O nas</a></li>
          <li><a href="#oferta">Usługi</a></li>
          <li><a href="#referencje">Realizacje</a></li>
          <li><a href="#opinie">Opinie</a></li>
          <li><a href="#technologie">Technologie</a></li>
          <li><a href="#zespol">Zespół</a></li>
          <li><a href="#kontakt">Kontakt</a></li>
          <li><a href="/login.php">Logowanie</a></li>
        </ul>
      </nav>

      <div class="nav-actions">
        <?php if ($currentUser && $currentUser['role'] === 'admin'): ?>
          <a href="/admin/dashboard.php" class="btn btn-outline">
            <i class="fa-solid fa-gauge-high icon-left"></i>
            Panel admina
          </a>
        <?php elseif ($currentUser && $currentUser['role'] === 'client'): ?>
          <a href="/client/dashboard.php" class="btn btn-outline">
            <i class="fa-solid fa-folder-shield icon-left"></i>
            Panel klienta
          </a>
        <?php endif; ?>

        <button class="btn btn-outline" data-scroll-to="#oferta">Oferta</button>
        <button class="btn btn-primary" data-scroll-to="#kontakt">
          Wyceń projekt <span class="chevron">→</span>
        </button>
      </div>

      <button class="nav-toggle" aria-label="Otwórz menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>

    <div class="container nav-mobile" id="navMobile">
      <a href="#onas">O nas</a>
      <a href="#oferta">Usługi</a>
      <a href="#referencje">Realizacje</a>
      <a href="#opinie">Opinie</a>
      <a href="#technologie">Technologie</a>
      <a href="#zespol">Zespół</a>
      <a href="#kontakt">Kontakt</a>

      <button class="btn btn-primary" data-scroll-to="#kontakt" style="width:max-content;margin-top:0.2rem;">
        Bezpłatna konsultacja
      </button>
    </div>
  </header>

  <!-- PŁYWAJĄCY PRZYCISK TRYBU D/N W PRAWYM GÓRNYM ROGU -->
  <button
    class="btn btn-outline theme-toggle theme-toggle-floating"
    type="button"
    aria-label="Przełącz tryb jasny/ciemny"
    title="Przełącz tryb jasny/ciemny"
  >
    <span class="theme-icon">☀️</span>
  </button>

  <main>
    <!-- HERO -->
    <section class="hero">
      <!-- TŁA DO PARALLAXU -->
      <div class="hero-bg-grid" aria-hidden="true"></div>
      <div class="hero-bg-blob hero-bg-blob-left" aria-hidden="true"></div>
      <div class="hero-bg-blob hero-bg-blob-right" aria-hidden="true"></div>

      <div class="container">
        <div class="badge reveal">
          <span class="badge-dot"></span>
          <span>Nowoczesne rozwiązania IT dla firm</span>
        </div>

        <h1 class="hero-title reveal" data-reveal-delay="80">
          <span class="hero-title-main">Usługi informatyczne dla biznesu</span>
          <span class="hero-title-name">Kamil Kaczmarczyk</span>
        </h1>

        <div class="hero-grid">
          <!-- LEWA KOLUMNA -->
          <div>
            <p class="hero-subtitle reveal" data-reveal-delay="140">
              Specjalizuję się w administracji systemami Linux, automatyzacji procesów (DevOps) oraz bezpieczeństwie
              infrastruktury. Pomagam firmom budować stable, skalowalne i monitorowane środowiska IT.
            </p>
            <div class="hero-actions reveal" data-reveal-delay="200">
              <button class="btn btn-primary" data-scroll-to="#kontakt">
                <i class="fa-solid fa-comments icon-left" aria-hidden="true"></i>
                Porozmawiajmy o Twojej infrastrukturze
              </button>
              <button class="btn btn-outline" data-scroll-to="#oferta">
                <i class="fa-solid fa-list-check icon-left" aria-hidden="true"></i>
                Zobacz usługi
              </button>
            </div>
            <div class="hero-meta reveal" data-reveal-delay="260">
              <div>
                <strong>
                  <i class="fa-solid fa-briefcase icon-left" aria-hidden="true"></i>
                  10+ lat doświadczenia
                </strong>
                w projektach dla różnych branż
              </div>
              <div>
                <strong>
                  <i class="fa-solid fa-shield-heart icon-left" aria-hidden="true"></i>
                  24/7
                </strong>
                podejście nastawione na dostępność i bezpieczeństwo
              </div>
              <div>
                <strong>
                  <i class="fa-solid fa-chart-line icon-left" aria-hidden="true"></i>
                  Monitoring i automatyzacja
                </strong>
                środowiska, aby szybciej wykrywać problemy i wdrażać zmiany
              </div>
            </div>
          </div>

          <!-- PRAWA KOLUMNA / KARTA -->
          <aside class="hero-card reveal" data-reveal-delay="320" aria-label="Podgląd współpracy IT">
            <div class="hero-card-blur"></div>
            <div class="hero-card-inner">
              <div>
                <div class="hero-card-header">Obszary specjalizacji</div>
                <div class="hero-card-title">
                  <i class="fa-solid fa-network-wired icon-left" aria-hidden="true"></i>
                  Stabilna infrastruktura IT
                </div>
                <p class="hero-card-desc">
                  Projektuję i utrzymuję środowiska serwerowe, automatyzuję powtarzalne zadania oraz dbam o bezpieczeństwo
                  kluczowych systemów w Twojej organizacji.
                </p>
                <div class="hero-card-tags">
                  <span class="pill"><i class="fa-brands fa-linux"></i>&nbsp;Administracja Linux</span>
                  <span class="pill"><i class="fa-solid fa-gears"></i>&nbsp;Automatyzacja / DevOps</span>
                  <span class="pill"><i class="fa-solid fa-chart-line"></i>&nbsp;Monitoring i logi</span>
                  <span class="pill"><i class="fa-solid fa-shield-halved"></i>&nbsp;Bezpieczeństwo</span>
                </div>
              </div>

              <!-- MINI ILUSTRACJA – PIPELINE DEVOPS -->
              <div class="hero-illustration">
                <div class="hero-ill-step hero-ill-step--code">Code</div>
                <div class="hero-ill-arrow">➜</div>
                <div class="hero-ill-step hero-ill-step--pipeline">CI/CD</div>
                <div class="hero-ill-arrow">➜</div>
                <div class="hero-ill-step hero-ill-step--prod">Prod</div>
              </div>

              <div class="hero-stats">
                <div class="stat">
                  <strong><i class="fa-solid fa-chart-pie icon-left"></i> Monitoring</strong>
                  ELK / Prometheus / Grafana dopasowane do Twoich systemów
                </div>
                <div class="stat">
                  <strong><i class="fa-solid fa-rocket icon-left"></i> Automatyzacja</strong>
                  Ansible oraz CI/CD do powtarzalnych wdrożeń
                </div>
                <div class="stat">
                  <strong><i class="fa-solid fa-lock icon-left"></i> Bezpieczeństwo</strong>
                  audyty, hardening, kopie zapasowe i procedury
                </div>
                <div class="stat">
                  <strong><i class="fa-solid fa-handshake-angle icon-left"></i> Wsparcie</strong>
                  doradztwo przy rozwoju i migracjach środowiska
                </div>
              </div>
            </div>
          </aside>
        </div>

        <!-- LEKKI MOCKUP 3D LAPTOPA / SERWERA -->
        <div class="hero-mockup" aria-hidden="true">
          <div class="hero-mockup-base">
            <div class="hero-mockup-screen">
              <div class="hero-mockup-screen-inner"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- O NAS -->
    <section id="onas">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <div class="section-kicker">O nas</div>
            <h2 class="section-title">
              <i class="fa-solid fa-users-gear icon-left" aria-hidden="true"></i>
              Łączymy doświadczenie z pasją do technologii
            </h2>
          </div>
          <p class="section-description">
            Pracujemy blisko biznesu – nie tylko opiekujemy się serwerami, ale projektujemy całe środowisko tak,
            aby wspierało codzienną pracę Twojej organizacji.
          </p>
        </div>
        <div class="content">
          <article class="card reveal">
            <h3>
              <i class="fa-solid fa-circle-info icon-left" aria-hidden="true"></i>
              Kim jesteśmy?
            </h3>
            <p>
              Jesteśmy zespołem specjalistów IT, którzy łączą praktyczne doświadczenie z nowoczesnym podejściem do
              infrastruktury. Nasz cel to stabilne, bezpieczne i wydajne środowiska IT, które nie przeszkadzają
              w pracy – tylko ją przyspieszają.
            </p>
            <div class="values-grid">
              <div>
                <strong>Środowiska produkcyjne</strong><br />
                Doświadczenie w utrzymaniu systemów działających 24/7.
              </div>
              <div>
                <strong>Pełna obserwowalność</strong><br />
                Monitoring, logi i alerty zamiast zgadywania, co się stało.
              </div>
              <div>
                <strong>Praca zdalna i on-site</strong><br />
                Możliwość wsparcia zdalnego oraz pracy na miejscu u klienta.
              </div>
              <div>
                <strong>Język biznesu</strong><br />
                Tłumaczymy techniczne szczegóły na konkretne korzyści dla firmy.
              </div>
            </div>
          </article>

          <article class="card reveal" data-reveal-delay="100">
            <h3>
              <i class="fa-solid fa-star-half-stroke icon-left" aria-hidden="true"></i>
              Dlaczego warto z nami współpracować?
            </h3>
            <p>
              Łączymy kompetencje z obszarów administracji, DevOps i bezpieczeństwa. Nie sprzedajemy jednego narzędzia –
              dobieramy rozwiązania do faktycznych potrzeb Twojej organizacji.
            </p>
            <div class="values-grid">
              <div>
                <strong>Doświadczenie</strong><br />
                Ponad 10 lat w branży IT i projekty w różnych sektorach.
              </div>
              <div>
                <strong>Kompetencje</strong><br />
                Linux, automatyzacja, CI/CD, kontenery, monitoring, chmura.
              </div>
              <div>
                <strong>Bezpieczeństwo</strong><br />
                Audyty, hardening oraz zgodność z dobrymi praktykami i RODO.
              </div>
              <div>
                <strong>Partnerstwo</strong><br />
                Budujemy długofalowe relacje zamiast jednorazowych wdrożeń.
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- TECHNOLOGIE – NAD USŁUGAMI -->
    <section id="technologie">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <div class="section-kicker">Technologie</div>
            <h2 class="section-title">Technologie, z którymi pracujemy</h2>
          </div>
          <p class="section-description">
            Na co dzień pracujemy z systemami Linux oraz narzędziami wykorzystywanymi w automatyzacji, konteneryzacji
            i tworzeniu oprogramowania. Dobieramy je do potrzeb konkretnego projektu.
          </p>
        </div>

        <div class="tech-icons-row reveal">
          <img src="images/linux-original.svg" alt="Linux" />
          <img src="images/ansible-original.svg" alt="Ansible" />
          <img src="images/docker-original.svg" alt="Docker" />
          <img src="images/python-original.svg" alt="Python" />
          <img src="images/git-original.svg" alt="Git" />
        </div>

        <div class="content">
          <article class="card reveal">
            <h3>
              <i class="fa-solid fa-laptop-code icon-left" aria-hidden="true"></i>
              Środowisko pracy
            </h3>

            <p>
              W zależności od projektu dobieramy narzędzia tak, aby ułatwić rozwój, utrzymanie i skalowanie systemów.
            </p>

            <!-- Mini ilustracja infrastruktury -->
<!-- Mini schemat przepływu: serwery -> automatyzacja -> monitoring -->
<!-- Mini schemat przepływu: plan -> serwery -> automatyzacja -> monitoring -> bezpieczeństwo -> wsparcie -->
<div class="infra-flow">
  <div class="infra-flow-step">
    <span class="infra-flow-icon">
      <i class="fa-solid fa-diagram-project" aria-hidden="true"></i>
    </span>
    <span class="infra-flow-label">Plan</span>
  </div>

  <span class="infra-flow-arrow">➜</span>

  <div class="infra-flow-step">
    <span class="infra-flow-icon">
      <i class="fa-solid fa-server" aria-hidden="true"></i>
    </span>
    <span class="infra-flow-label">Serwery</span>
  </div>

  <span class="infra-flow-arrow">➜</span>

  <div class="infra-flow-step">
    <span class="infra-flow-icon">
      <i class="fa-solid fa-gears" aria-hidden="true"></i>
    </span>
    <span class="infra-flow-label">Automatyzacja</span>
  </div>

  <span class="infra-flow-arrow">➜</span>

  <div class="infra-flow-step">
    <span class="infra-flow-icon">
      <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
    </span>
    <span class="infra-flow-label">Monitoring</span>
  </div>

  <span class="infra-flow-arrow">➜</span>

  <div class="infra-flow-step">
    <span class="infra-flow-icon">
      <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
    </span>
    <span class="infra-flow-label">Bezpieczeństwo</span>
  </div>

  <span class="infra-flow-arrow">➜</span>

  <div class="infra-flow-step">
    <span class="infra-flow-icon">
      <i class="fa-solid fa-handshake-angle" aria-hidden="true"></i>
    </span>
    <span class="infra-flow-label">Wsparcie</span>
  </div>
</div>



            <div class="values-grid">
              <div>
                <strong>Linux</strong><br />
                dystrybucje serwerowe dopasowane do środowiska produkcyjnego.
              </div>
              <div>
                <strong>Ansible</strong><br />
                automatyzacja konfiguracji i powtarzalnych zadań.
              </div>
              <div>
                <strong>Docker / kontenery</strong><br />
                izolacja usług i łatwiejsze wdrażanie aplikacji.
              </div>
              <div>
                <strong>Monitoring</strong><br />
                ELK, Prometheus, Grafana – monitoring, logi i dashboardy.
              </div>
              <div>
                <strong>Git</strong><br />
                kontrola wersji i przejrzyste procesy zmian.
              </div>
              <div>
                <strong>Języki i narzędzia</strong><br />
                m.in. Python oraz narzędzia do integracji i automatyzacji procesów.
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- USŁUGI -->
    <section id="oferta">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <div class="section-kicker">Usługi</div>
            <h2 class="section-title">
              <i class="fa-solid fa-layer-group icon-left" aria-hidden="true"></i>
              Usługi IT dopasowane do Twojej infrastruktury
            </h2>
          </div>
          <p class="section-description">
            Zajmujemy się projektowaniem, wdrażaniem i utrzymaniem środowisk IT. Poniżej przykładowe obszary, w których
            możemy pomóc Twojej organizacji.
          </p>
        </div>

        <div class="services-grid">
          <article class="service-card reveal">
            <div class="service-tag">Infrastruktura</div>
            <h3 class="service-title">
              <i class="fa-solid fa-server service-icon" aria-hidden="true"></i>
              Administracja serwerami i systemami
            </h3>
            <p>
              Kompleksowa opieka nad serwerami Linux i usługami krytycznymi dla działania firmy.
            </p>
            <ul class="service-list">
              <li><span class="bullet">•</span> konfiguracja i utrzymanie serwerów Linux</li>
              <li><span class="bullet">•</span> kopie zapasowe i procedury odtwarzania</li>
              <li><span class="bullet">•</span> aktualizacje i monitorowanie stanu usług</li>
              <li><span class="bullet">•</span> wsparcie przy migracjach i zmianach architektury</li>
            </ul>
            <div class="service-price">
              Współpraca <strong>długoterminowa lub projektowa</strong> – w zależności od potrzeb.
            </div>
          </article>

          <article class="service-card reveal" data-reveal-delay="60">
            <div class="service-tag">DevOps</div>
            <h3 class="service-title">
              <i class="fa-solid fa-code-branch service-icon" aria-hidden="true"></i>
              Automatyzacja i CI/CD
            </h3>
            <p>
              Budowa i utrzymanie procesów automatycznych wdrożeń oraz zarządzania konfiguracją.
            </p>
            <ul class="service-list">
              <li><span class="bullet">•</span> projektowanie pipeline CI/CD</li>
              <li><span class="bullet">•</span> Ansible i Infrastructure as Code</li>
              <li><span class="bullet">•</span> automatyczne wdrożenia aplikacji i usług</li>
              <li><span class="bullet">•</span> integracja z istniejącymi narzędziami zespołu</li>
            </ul>
            <div class="service-price">
              Zakres <strong>dostosowany do aktualnego procesu developmentu</strong> w Twojej firmie.
            </div>
          </article>

          <article class="service-card reveal" data-reveal-delay="120">
            <div class="service-tag">Monitoring</div>
            <h3 class="service-title">
              <i class="fa-solid fa-chart-simple service-icon" aria-hidden="true"></i>
              Monitoring i logowanie
            </h3>
            <p>
              Tworzenie systemów obserwowalności opartych m.in. o ELK Stack, Prometheus i Grafanę.
            </p>
            <ul class="service-list">
              <li><span class="bullet">•</span> centralne zbieranie logów z systemów i aplikacji</li>
              <li><span class="bullet">•</span> czytelne dashboardy dla zespołów IT i biznesu</li>
              <li><span class="bullet">•</span> alerting w czasie rzeczywistym</li>
              <li><span class="bullet">•</span> integracje z komunikatorami i systemami ticketowymi</li>
            </ul>
            <div class="service-price">
              Projekt <strong>pod konkretne środowisko</strong> – od kilku serwerów po rozbudowaną infrastrukturę.
            </div>
          </article>

          <article class="service-card reveal">
            <div class="service-tag">Security</div>
            <h3 class="service-title">
              <i class="fa-solid fa-shield-halved service-icon" aria-hidden="true"></i>
              Bezpieczeństwo i audyty
            </h3>
            <p>
              Analiza obecnego stanu bezpieczeństwa oraz wdrożenie praktycznych zabezpieczeń.
            </p>
            <ul class="service-list">
              <li><span class="bullet">•</span> audyty konfiguracji systemów i usług</li>
              <li><span class="bullet">•</span> hardening serwerów i usług sieciowych</li>
              <li><span class="bullet">•</span> rekomendacje zgodne z dobrymi praktykami</li>
              <li><span class="bullet">•</span> wsparcie przy incydentach bezpieczeństwa</li>
            </ul>
            <div class="service-price">
              Zakres prac <strong>ustalany indywidualnie</strong> po krótkiej konsultacji.
            </div>
          </article>

          <article class="service-card reveal" data-reveal-delay="60">
            <div class="service-tag">Chmura</div>
            <h3 class="service-title">
              <i class="fa-solid fa-cloud service-icon" aria-hidden="true"></i>
              Infrastruktura chmurowa i wirtualizacja
            </h3>
            <p>
              Projektowanie środowisk w chmurze i ich integracja z istniejącą infrastrukturą.
            </p>
            <ul class="service-list">
              <li><span class="bullet">•</span> migracje do AWS, Azure lub środowisk hybrydowych</li>
              <li><span class="bullet">•</span> konteneryzacja wybranych usług</li>
              <li><span class="bullet">•</span> optymalizacja kosztów infrastruktury</li>
              <li><span class="bullet">•</span> architektura odporna na awarie</li>
            </ul>
            <div class="service-price">
              Wycena <strong>w oparciu o skalę środowiska</strong> i wymagania biznesowe.
            </div>
          </article>

          <!-- ZMIENIONA KARTA: PROJEKTOWANIE STRON I PORTALI -->
          <article class="service-card reveal" data-reveal-delay="120">
            <div class="service-tag">Web / Portale</div>
            <h3 class="service-title">
              <i class="fa-solid fa-globe service-icon" aria-hidden="true"></i>
              Projektowanie stron i portali internetowych
            </h3>
            <p>
              Pomagam zaprojektować i wdrożyć nowoczesne strony firmowe, portale wewnętrzne oraz lekkie aplikacje webowe,
              które dobrze współpracują z Twoją infrastrukturą.
            </p>
            <ul class="service-list">
              <li><span class="bullet">•</span> projekt i wdrożenie responsywnych stron WWW</li>
              <li><span class="bullet">•</span> integracja z istniejącą infrastrukturą i monitoringiem</li>
              <li><span class="bullet">•</span> optymalizacja wydajności i bezpieczeństwa serwisu</li>
              <li><span class="bullet">•</span> landing page’e pod kampanie i rekrutację</li>
            </ul>
            <div class="service-price">
              Zakres <strong>od prostych wizytówek po rozbudowane portale</strong> – w zależności od potrzeb biznesu.
            </div>
          </article>
        </div>

        <div class="cta-strip reveal">
          <div>
            <strong>Nie wiesz, od czego zacząć?</strong><br />
            Opisz w kilku zdaniach swoją obecną infrastrukturę – przygotuję propozycję pierwszych kroków oraz możliwych
            usprawnień.
          </div>
          <button class="btn" data-scroll-to="#kontakt">
            <i class="fa-solid fa-lightbulb icon-left" aria-hidden="true"></i>
            Zapytaj o rekomendacje
          </button>
        </div>
      </div>
    </section>

    <!-- REALIZACJE -->
    <section id="referencje">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <div class="section-kicker">Realizacje</div>
            <h2 class="section-title">
              <i class="fa-solid fa-diagram-project icon-left" aria-hidden="true"></i>
              Przykładowe projekty i wdrożenia
            </h2>
          </div>
          <p class="section-description">
            Przykłady projektów zrealizowanych dla klientów z różnych branż – od monitoringu infrastruktury
            po automatyzację wdrożeń.
          </p>
        </div>

        <div class="testimonials-grid">
          <article class="testimonial reveal">
            <p>
              System monitorowania serwerów i usług oparty o ELK Stack oraz alerting w czasie rzeczywistym. Klient zyskał
              jednolity podgląd kondycji infrastruktury i szybszą reakcję na incydenty.
            </p>
            <div class="testimonial-footer">
              <div>
                <div class="testimonial-name">Firma produkcyjna</div>
                <div class="testimonial-role">Monitoring i logowanie</div>
              </div>
              <div class="testimonial-badge">
                <i class="fa-solid fa-magnifying-glass-chart icon-left" aria-hidden="true"></i>
                ELK Stack
              </div>
            </div>
          </article>

          <article class="testimonial reveal" data-reveal-delay="80">
            <p>
              Projekt i wdrożenie pipeline CI/CD dla międzynarodowej organizacji, obejmujący automatyczne testy,
              budowanie oraz wdrożenia aplikacji na środowiska testowe i produkcyjne.
            </p>
            <div class="testimonial-footer">
              <div>
                <div class="testimonial-name">Sektor finansowy</div>
                <div class="testimonial-role">Automatyzacja wdrożeń</div>
              </div>
              <div class="testimonial-badge">
                <i class="fa-solid fa-code-compare icon-left" aria-hidden="true"></i>
                CI/CD
              </div>
            </div>
          </article>

          <article class="testimonial reveal" data-reveal-delay="160">
            <p>
              Zaprojektowanie i uruchomienie bezpiecznej infrastruktury chmurowej wraz z pełnym monitoringiem, kopiami
              zapasowymi oraz optymalizacją kosztów korzystania z zasobów.
            </p>
            <div class="testimonial-footer">
              <div>
                <div class="testimonial-name">Firma usługowa</div>
                <div class="testimonial-role">Infrastruktura chmurowa</div>
              </div>
              <div class="testimonial-badge">
                <i class="fa-solid fa-cloud-arrow-up icon-left" aria-hidden="true"></i>
                Chmura
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- OPINIE KLIENTÓW – PRZESUWAJĄCE SIĘ OKIENKA -->
    <section id="opinie">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <div class="section-kicker">Opinie</div>
            <h2 class="section-title">
              <i class="fa-solid fa-comment-dots icon-left" aria-hidden="true"></i>
              Co mówią klienci o współpracy
            </h2>
          </div>
          <p class="section-description">
            Kilka przykładowych opinii od klientów, z którymi realizowaliśmy projekty związane z infrastrukturą,
            monitoringiem i automatyzacją.
          </p>
        </div>

        <div class="opinions-marquee reveal" data-reveal-delay="80">
          <div class="opinions-track">
            <!-- ZESTAW 1 -->
            <article class="opinion-card">
              <p>
                „Po wdrożeniu monitoringu i automatyzacji mamy pełen obraz tego, co dzieje się w naszych systemach.
                Reakcja na problemy jest zdecydowanie szybsza.” 😊📊
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Anna K.</span>
                <span class="opinion-role">IT Manager, firma produkcyjna</span>
              </div>
            </article>

            <article class="opinion-card">
              <p>
                „Kamil pomógł nam zaprojektować i wdrożyć pipeline CI/CD. Zespół developmentu wreszcie może skupić się
                na kodzie, a wdrożenia są przewidywalne.” 🚀👨‍💻
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Marek P.</span>
                <span class="opinion-role">Head of Development, fintech</span>
              </div>
            </article>

            <article class="opinion-card">
              <p>
                „Migracja do chmury była dla nas dużym wyzwaniem. Dzięki dobrze zaplanowanej architekturze obyło się bez
                przestojów i zaskoczeń kosztowych.” ☁️✅
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Joanna L.</span>
                <span class="opinion-role">COO, firma usługowa</span>
              </div>
            </article>

            <article class="opinion-card">
              <p>
                „Audyt bezpieczeństwa pokazał nam, jakie mamy słabe punkty. Po wdrożeniu zaleceń śpimy spokojniej –
                szczególnie dział finansowy.” 🔒😌
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Tomasz R.</span>
                <span class="opinion-role">CFO, sektor finansowy</span>
              </div>
            </article>

            <!-- ZESTAW 2 – duplikat do płynnej pętli -->
            <article class="opinion-card">
              <p>
                „Po wdrożeniu monitoringu i automatyzacji mamy pełen obraz tego, co dzieje się w naszych systemach.
                Reakcja na problemy jest zdecydowanie szybsza.” 😊📊
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Anna K.</span>
                <span class="opinion-role">IT Manager, firma produkcyjna</span>
              </div>
            </article>

            <article class="opinion-card">
              <p>
                „Kamil pomógł nam zaprojektować i wdrożyć pipeline CI/CD. Zespół developmentu wreszcie może skupić się
                na kodzie, a wdrożenia są przewidywalne.” 🚀👨‍💻
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Marek P.</span>
                <span class="opinion-role">Head of Development, fintech</span>
              </div>
            </article>

            <article class="opinion-card">
              <p>
                „Migracja do chmury była dla nas dużym wyzwaniem. Dzięki dobrze zaplanowanej architekturze obyło się bez
                przestojów i zaskoczeń kosztowych.” ☁️✅
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Joanna L.</span>
                <span class="opinion-role">COO, firma usługowa</span>
              </div>
            </article>

            <article class="opinion-card">
              <p>
                „Audyt bezpieczeństwa pokazał nam, jakie mamy słabe punkty. Po wdrożeniu zaleceń śpimy spokojniej –
                szczególnie dział finansowy.” 🔒😌
              </p>
              <div class="opinion-meta">
                <span class="opinion-name">Tomasz R.</span>
                <span class="opinion-role">CFO, sektor finansowy</span>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- ZESPÓŁ -->
    <section id="zespol">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <div class="section-kicker">Zespół</div>
            <h2 class="section-title">
              <i class="fa-solid fa-people-group icon-left" aria-hidden="true"></i>
              Ludzie stojący za projektami
            </h2>
          </div>
          <p class="section-description">
            W projekty angażujemy specjalistów z różnych obszarów – od administracji i DevOps, przez bezpieczeństwo,
            aż po analizę danych.
          </p>
        </div>

        <!-- 4 karty w układzie 2×2 z avatarami -->
        <div class="team-grid">
          <article class="service-card team-card reveal">
            <div class="team-header">
              <img
                src="images/kadra_mez1.png"
                alt="Kamil Kaczmarczyk – założyciel"
                class="team-avatar"
              />
              <div>
                <div class="service-tag">Founder</div>
                <h3 class="service-title">
                  <i class="fa-solid fa-user-gear service-icon" aria-hidden="true"></i>
                  Kamil Kaczmarczyk
                </h3>
              </div>
            </div>
            <p>
              Założyciel i główny specjalista. Odpowiada za architekturę rozwiązań, administrację systemami Linux oraz
              nadzór nad projektami automatyzacji.
            </p>
          </article>

          <article class="service-card team-card reveal" data-reveal-delay="60">
            <div class="team-header">
              <img
                src="images/kadra_mez2.png"
                alt="Specjalista ds. bezpieczeństwa"
                class="team-avatar"
              />
              <div>
                <div class="service-tag">Security</div>
                <h3 class="service-title">
                  <i class="fa-solid fa-user-shield service-icon" aria-hidden="true"></i>
                  Specjalista ds. bezpieczeństwa
                </h3>
              </div>
            </div>
            <p>
              Zajmuje się audytami bezpieczeństwa, analizą konfiguracji oraz rekomendacjami zmian zgodnych z dobrymi
              praktykami branżowymi.
            </p>
          </article>

          <article class="service-card team-card reveal" data-reveal-delay="120">
            <div class="team-header">
              <img
                src="images/kadra_kob1.png"
                alt="Inżynier DevOps"
                class="team-avatar"
              />
              <div>
                <div class="service-tag">DevOps</div>
                <h3 class="service-title">
                  <i class="fa-solid fa-screwdriver-wrench service-icon" aria-hidden="true"></i>
                  Inżynier DevOps
                </h3>
              </div>
            </div>
            <p>
              Projektuje i utrzymuje pipeline CI/CD, automatyzuje wdrożenia i integruje narzędzia zespołów
              developerskich z infrastrukturą.
            </p>
          </article>

          <article class="service-card team-card reveal" data-reveal-delay="180">
            <div class="team-header">
              <img
                src="images/kadra_kob2.png"
                alt="Analityk danych i logów"
                class="team-avatar"
              />
              <div>
                <div class="service-tag">Monitoring</div>
                <h3 class="service-title">
                  <i class="fa-solid fa-chart-column service-icon" aria-hidden="true"></i>
                  Analityk danych i logów
                </h3>
              </div>
            </div>
            <p>
              Odpowiada za konfigurację systemów monitoringu oraz przygotowywanie dashboardów i raportów dla biznesu.
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- KONTAKT -->
    <section id="kontakt">
      <div class="container">
        <div class="section-header reveal">
          <div>
            <div class="section-kicker">Kontakt</div>
            <h2 class="section-title">
              <i class="fa-solid fa-envelope-open-text icon-left" aria-hidden="true"></i>
              Porozmawiajmy o Twoim środowisku IT
            </h2>
          </div>
          <p class="section-description">
            Napisz kilka zdań o swojej infrastrukturze – ilu użytkowników obsługujesz, jakie systemy są kluczowe i z
            jakimi wyzwaniami się mierzysz. Odpowiem z propozycją dalszych kroków.
          </p>
        </div>

        <div class="contact-grid">
          <aside class="contact-card reveal">
            <h3>
              <i class="fa-solid fa-circle-question icon-left" aria-hidden="true"></i>
              Jak przygotować zapytanie?
            </h3>
            <p>
              Aby szybciej wrócić z konkretną propozycją, możesz od razu uwzględnić w wiadomości kilka informacji o
              środowisku i oczekiwaniach.
            </p>
            <div class="contact-items">
              <div>
                <span class="label">
                  <i class="fa-solid fa-arrows-left-right icon-left" aria-hidden="true"></i> Skala
                </span>
                <span>liczba serwerów / usług, kluczowe aplikacje, liczba użytkowników</span>
              </div>
              <div>
                <span class="label">
                  <i class="fa-solid fa-layer-group icon-left" aria-hidden="true"></i> Obszar
                </span>
                <span>administracja, automatyzacja, monitoring, bezpieczeństwo itp.</span>
              </div>
              <div>
                <span class="label">
                  <i class="fa-solid fa-bullseye icon-left" aria-hidden="true"></i> Cel
                </span>
                <span>np. stabilizacja, migracja, redukcja kosztów, wdrożenie monitoringu</span>
              </div>
              <div>
                <span class="label">
                  <i class="fa-solid fa-fire icon-left" aria-hidden="true"></i> Pilność
                </span>
                <span>projekt planowany czy problem wymagający szybkiej reakcji</span>
              </div>
            </div>
            <p class="contact-hint">
              <i class="fa-solid fa-circle-exclamation icon-left" aria-hidden="true"></i>
              Tutaj możesz dodać swoje dane kontaktowe (e-mail, telefon, adres), jeśli chcesz je wyświetlać klientom.
            </p>
          </aside>

          <div>
            <form id="contactForm" action="send_form.php" method="post" novalidate class="reveal" data-reveal-delay="80">
              <div class="field">
                <label for="name">
                  <i class="fa-solid fa-user icon-left" aria-hidden="true"></i>
                  Imię i nazwisko<span class="required">*</span>
                </label>
                <input type="text" id="name" name="name" placeholder="np. Jan Kowalski" required />
                <div class="error" id="errorName">Podaj swoje imię i nazwisko.</div>
              </div>

              <div class="field">
                <label for="email">
                  <i class="fa-solid fa-envelope icon-left" aria-hidden="true"></i>
                  Adres e-mail<span class="required">*</span>
                </label>
                <input type="email" id="email" name="email" placeholder="np. kontakt@firma.pl" required />
                <div class="error" id="errorEmail">Podaj poprawny adres e-mail.</div>
              </div>

              <div class="field">
                <label for="phone">
                  <i class="fa-solid fa-phone icon-left" aria-hidden="true"></i>
                  Telefon
                </label>
                <input type="tel" id="phone" name="phone" placeholder="np. +48 600 000 000" />
                <small>Opcjonalnie – jeśli wolisz, byśmy zadzwonili.</small>
              </div>

              <div class="field">
                <label for="message">
                  <i class="fa-solid fa-note-sticky icon-left" aria-hidden="true"></i>
                  Opisz krótko swoje środowisko IT<span class="required">*</span>
                </label>
                <textarea
                  id="message"
                  name="message"
                  placeholder="Np. kilka serwerów Linux, brak monitoringu, potrzebne wdrożenie kopii zapasowych i alertingu."
                  required
                ></textarea>
                <div class="error" id="errorMessage">Napisz kilka zdań o swojej infrastrukturze.</div>
              </div>

              <div class="form-footer">
                <button type="submit" class="btn btn-primary">
                  <i class="fa-solid fa-paper-plane icon-left" aria-hidden="true"></i>
                  Wyślij zapytanie
                </button>
                <p>
                  Wysyłając formularz, wyrażasz zgodę na kontakt w celu omówienia szczegółów współpracy.
                </p>
              </div>
              <div class="form-success" id="formSuccess">
                <span class="form-success-icon">✅</span>
                <span>
                  Dziękujemy! Twoja wiadomość została wysłana. Jeśli formularz ma działać z CAPTCHA, możesz tu podpiąć
                  istniejące rozwiązanie z send_form.php / captcha.php.
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="container footer-inner">
      <div>© <span id="year"></span> Kamil Kaczmarczyk. Wszystkie prawa zastrzeżone.</div>
      <div class="footer-links">
        <a href="#onas">O nas</a>
        <a href="#oferta">Usługi</a>
        <a href="#kontakt">Kontakt</a>
      </div>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
