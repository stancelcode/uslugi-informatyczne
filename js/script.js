document.addEventListener('DOMContentLoaded', () => {
  // ===========================
  //  SLIDER (SWIPER)
  // ===========================
  try {
    const swiper = new Swiper('.heroSwiper', {
      loop: true,
      effect: 'fade',
      speed: 800,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
      }
    });

    console.log('✅ Swiper zainicjowany poprawnie.');
  } catch (err) {
    console.error('❌ Błąd inicjalizacji Swipera:', err);
  }

  // ===========================
  //  AOS ANIMACJE
  // ===========================
  AOS.init({ duration: 1000, once: true });

  // ===========================
  //  TRYB CIEMNY
  // ===========================
  const toggle = document.getElementById('darkModeToggle');
  if (toggle) {
    toggle.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      toggle.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
    });
  }

  // ===========================
  //  POWRÓT DO GÓRY
  // ===========================
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', () => {
      backToTop.style.display = window.scrollY > 300 ? 'block' : 'none';
    });
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ===========================
  //  CAPTCHA
  // ===========================
  const reloadBtn = document.getElementById('reloadCaptcha');
  const captchaImg = document.getElementById('captchaImage');
  if (reloadBtn && captchaImg) {
    reloadBtn.addEventListener('click', () => {
      captchaImg.src = 'captcha.php?' + new Date().getTime();
    });
  }

  // ===========================
  //  FORMULARZ AJAX
  // ===========================
  const form = document.getElementById('contactForm');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      try {
        const res = await fetch('send_form.php', { method: 'POST', body: formData });
        const data = await res.text();
        showToast(data.includes('❌') ? '❌ Błąd przy wysyłaniu.' : '✅ Wiadomość wysłana pomyślnie!');
        captchaImg.src = 'captcha.php?' + new Date().getTime();
        form.reset();
      } catch {
        showToast('❌ Wystąpił błąd sieci.', 'error');
      }
    });
  }

  // ===========================
  //  TOAST
  // ===========================
  function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.style.background = type === 'error' ? '#e74c3c' : '#4CAF50';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
  }
});
