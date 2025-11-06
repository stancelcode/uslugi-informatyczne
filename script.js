document.addEventListener("DOMContentLoaded", () => {
  const html = document.documentElement;
  const themeToggle = document.querySelector(".theme-toggle");
  const themeIcon = themeToggle ? themeToggle.querySelector(".theme-icon") : null;

  // Ustawienie motywu na podstawie localStorage
  const storedTheme = localStorage.getItem("theme");
  if (storedTheme === "light" || storedTheme === "dark") {
    html.setAttribute("data-theme", storedTheme);
  } else {
    html.setAttribute("data-theme", "dark");
  }

  const updateThemeIcon = () => {
    if (!themeIcon) return;
    const current = html.getAttribute("data-theme");
    themeIcon.textContent = current === "light" ? "🌙" : "☀️";
  };

  updateThemeIcon();

  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const current = html.getAttribute("data-theme") === "light" ? "light" : "dark";
      const next = current === "light" ? "dark" : "light";
      html.setAttribute("data-theme", next);
      localStorage.setItem("theme", next);
      updateThemeIcon();
    });
  }

  // Smooth scroll dla przycisków z data-scroll-to
  document.querySelectorAll("[data-scroll-to]").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      const targetSelector = btn.getAttribute("data-scroll-to");
      const target = document.querySelector(targetSelector);
      if (!target) return;
      const headerOffset = 80;
      const rect = target.getBoundingClientRect();
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const offsetTop = rect.top + scrollTop - headerOffset;

      window.scrollTo({
        top: offsetTop,
        behavior: "smooth",
      });
    });
  });

  // Mobile nav
  const navToggle = document.querySelector(".nav-toggle");
  const navMobile = document.getElementById("navMobile");

  if (navToggle && navMobile) {
    navToggle.addEventListener("click", () => {
      const isOpen = navMobile.classList.toggle("open");
      navToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    navMobile.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        navMobile.classList.remove("open");
        navToggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  // Walidacja formularza kontaktowego (prosta, po stronie frontu)
  const form = document.getElementById("contactForm");
  if (form) {
    const fieldName = document.getElementById("name");
    const fieldEmail = document.getElementById("email");
    const fieldMessage = document.getElementById("message");

    const errorName = document.getElementById("errorName");
    const errorEmail = document.getElementById("errorEmail");
    const errorMessage = document.getElementById("errorMessage");
    const successMsg = document.getElementById("formSuccess");

    const emailRegex =
      /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    form.addEventListener("submit", (e) => {
      let hasError = false;

      if (!fieldName.value.trim()) {
        errorName.classList.add("visible");
        hasError = true;
      } else {
        errorName.classList.remove("visible");
      }

      if (!emailRegex.test(fieldEmail.value.trim())) {
        errorEmail.classList.add("visible");
        hasError = true;
      } else {
        errorEmail.classList.remove("visible");
      }

      if (!fieldMessage.value.trim()) {
        errorMessage.classList.add("visible");
        hasError = true;
      } else {
        errorMessage.classList.remove("visible");
      }

      if (hasError) {
        e.preventDefault();
        if (successMsg) successMsg.classList.remove("visible");
        return;
      }

      // tutaj możesz kiedyś dodać AJAX;
      // na razie pozwalamy formularzowi normalnie się wysłać
      if (successMsg) {
        successMsg.classList.add("visible");
      }
    });
  }

  // Rok w stopce
  const yearSpan = document.getElementById("year");
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }

  // === ANIMACJE POJAWIANIA SIĘ PRZY SCROLLU ===
  const revealElements = document.querySelectorAll(".reveal");

  if (revealElements.length > 0) {
    if ("IntersectionObserver" in window) {
      const observer = new IntersectionObserver(
        (entries, obs) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const el = entry.target;
              const delay = el.dataset.revealDelay
                ? parseInt(el.dataset.revealDelay, 10) || 0
                : 0;

              if (delay > 0) {
                setTimeout(() => {
                  el.classList.add("reveal-visible");
                }, delay);
              } else {
                el.classList.add("reveal-visible");
              }

              obs.unobserve(el);
            }
          });
        },
        {
          threshold: 0.15,
        }
      );

      revealElements.forEach((el) => observer.observe(el));
    } else {
      // starsze przeglądarki – po prostu pokaż
      revealElements.forEach((el) => el.classList.add("reveal-visible"));
    }
  }
});
