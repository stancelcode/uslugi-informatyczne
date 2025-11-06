document.addEventListener("DOMContentLoaded", () => {
  const root = document.documentElement;
  const themeToggle = document.querySelector(".theme-toggle");
  const themeIcon = themeToggle ? themeToggle.querySelector(".theme-icon") : null;

  // MOTYW: odczyt z localStorage + prefers-color-scheme
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

  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const current = root.getAttribute("data-theme") === "light" ? "dark" : "light";
      root.setAttribute("data-theme", current);
      localStorage.setItem("theme", current);
      updateThemeIcon();
    });
  }

  function updateThemeIcon() {
    if (!themeIcon || !themeToggle) return;
    const current = root.getAttribute("data-theme");
    if (current === "light") {
      themeIcon.textContent = "🌙";
      themeToggle.title = "Włącz tryb nocny";
    } else {
      themeIcon.textContent = "☀️";
      themeToggle.title = "Włącz tryb dzienny";
    }
  }

  // MOBILE NAV
  const navToggle = document.querySelector(".nav-toggle");
  const navMobile = document.getElementById("navMobile");

  if (navToggle && navMobile) {
    navToggle.addEventListener("click", () => {
      const isOpen = navMobile.classList.toggle("open");
      navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  }

  // SMOOTH SCROLL
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

      if (navMobile && navMobile.classList.contains("open")) {
        navMobile.classList.remove("open");
        if (navToggle) navToggle.setAttribute("aria-expanded", "false");
      }
    });
  });

  // HERO SLIDER
  const slider = document.querySelector(".hero-slider");
  if (slider) {
    const slides = Array.from(slider.querySelectorAll(".hero-slide"));
    const dots = Array.from(slider.querySelectorAll(".hero-slider-dot"));
    const prevBtn = slider.querySelector(".hero-slider-prev");
    const nextBtn = slider.querySelector(".hero-slider-next");

    let current = 0;
    let autoTimer = null;

    function showSlide(index) {
      if (!slides.length) return;
      if (index < 0) index = slides.length - 1;
      if (index >= slides.length) index = 0;
      current = index;

      slides.forEach((slide, i) => {
        const isActive = i === current;
        slide.classList.toggle("active", isActive);
        slide.setAttribute("aria-hidden", isActive ? "false" : "true");
      });

      dots.forEach((dot, i) => {
        dot.classList.toggle("active", i === current);
      });
    }

    function nextSlide() {
      showSlide(current + 1);
    }

    function prevSlide() {
      showSlide(current - 1);
    }

    function startAuto() {
      stopAuto();
      autoTimer = setInterval(nextSlide, 7000);
    }

    function stopAuto() {
      if (autoTimer) {
        clearInterval(autoTimer);
        autoTimer = null;
      }
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        nextSlide();
        startAuto();
      });
    }

    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        prevSlide();
        startAuto();
      });
    }

    dots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        showSlide(index);
        startAuto();
      });
    });

    showSlide(0);
    startAuto();
  }

  // FORMULARZ – prosta walidacja front-end
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

      if (!valid) {
        e.preventDefault();
        return;
      }

      // jeśli chcesz zostawić pełne wysyłanie do send_form.php, zostaw e.preventDefault() zakomentowane
      // e.preventDefault();

      if (success) {
        success.classList.add("visible");
      }
    });
  }

  function validateEmail(value) {
    return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value);
  }

  // ROK W STOPCE
  const yearSpan = document.getElementById("year");
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
});
