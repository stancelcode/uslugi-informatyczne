document.addEventListener("DOMContentLoaded", () => {
  const root = document.documentElement;
  const themeToggle = document.querySelector(".theme-toggle");
  const themeIcon = themeToggle ? themeToggle.querySelector(".theme-icon") : null;

  // Ustawienie motywu przy starcie
  const storedTheme = localStorage.getItem("theme");
  if (storedTheme === "light" || storedTheme === "dark") {
    root.setAttribute("data-theme", storedTheme);
  } else {
    const prefersLight =
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: light)").matches;
    root.setAttribute("data-theme", prefersLight ? "light" : "dark");
  }
  updateThemeIcon();

  // Przełącznik dzień/noc
  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const current = root.getAttribute("data-theme") === "light" ? "dark" : "light";
      root.setAttribute("data-theme", current);
      localStorage.setItem("theme", current);
      updateThemeIcon();
    });
  }

  function updateThemeIcon() {
    if (!themeIcon) return;
    const current = root.getAttribute("data-theme");
    if (current === "light") {
      themeIcon.textContent = "🌙";
      themeToggle.title = "Włącz tryb nocny";
    } else {
      themeIcon.textContent = "☀️";
      themeToggle.title = "Włącz tryb dzienny";
    }
  }

  // Mobile nav
  const navToggle = document.querySelector(".nav-toggle");
  const navMobile = document.getElementById("navMobile");

  if (navToggle && navMobile) {
    navToggle.addEventListener("click", () => {
      const isOpen = navMobile.classList.toggle("open");
      navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  }

  // Smooth scroll (linki z data-scroll-to i menu)
  const scrollLinks = [
    ...document.querySelectorAll("[data-scroll-to]"),
    ...document.querySelectorAll("header nav a"),
    ...document.querySelectorAll("#navMobile a")
  ];

  scrollLinks.forEach((el) => {
    el.addEventListener("click", (e) => {
      const targetSelector = el.dataset.scrollTo || el.getAttribute("href");
      if (!targetSelector || !targetSelector.startsWith("#")) return;

      e.preventDefault();
      const target = document.querySelector(targetSelector);
      if (!target) return;

      target.scrollIntoView({ behavior: "smooth", block: "start" });

      // zamknij mobilne menu po kliknięciu
      if (navMobile && navMobile.classList.contains("open")) {
        navMobile.classList.remove("open");
        if (navToggle) navToggle.setAttribute("aria-expanded", "false");
      }
    });
  });

  // Walidacja formularza (prosta, front-end)
  const form = document.getElementById("contactForm");
  if (form) {
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const messageInput = document.getElementById("message");
    const errorName = document.getElementById("errorName");
    const errorEmail = document.getElementById("errorEmail");
    const errorMessage = document.getElementById("errorMessage");
    const success = document.getElementById("formSuccess");

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      let valid = true;

      if (!nameInput.value.trim()) {
        errorName.classList.add("visible");
        valid = false;
      } else {
        errorName.classList.remove("visible");
      }

      if (!validateEmail(emailInput.value)) {
        errorEmail.classList.add("visible");
        valid = false;
      } else {
        errorEmail.classList.remove("visible");
      }

      if (!messageInput.value.trim()) {
        errorMessage.classList.add("visible");
        valid = false;
      } else {
        errorMessage.classList.remove("visible");
      }

      if (!valid) return;

      // demo: tylko pokazujemy komunikat, bez backendu
      if (success) {
        success.classList.add("visible");
        form.reset();
      }
    });
  }

  function validateEmail(value) {
    return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value);
  }

  // Dynamiczny rok w stopce
  const yearSpan = document.getElementById("year");
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
});
