(() => {
  // ===== Header shadow on scroll =====
  const header = document.querySelector('[data-tt-header]');
  const onScroll = () => {
    if (!header) return;
    if (window.scrollY > 4) header.classList.add('tt-header--scrolled');
    else header.classList.remove('tt-header--scrolled');
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // ===== Burger menu =====
  const burger = document.querySelector('[data-tt-burger]');
  const menu = document.querySelector('[data-tt-menu]');

  const closeMenu = () => {
    if (!menu || !burger) return;
    menu.classList.remove('is-open');
    burger.setAttribute('aria-expanded', 'false');
  };
  const openMenu = () => {
    if (!menu || !burger) return;
    menu.classList.add('is-open');
    burger.setAttribute('aria-expanded', 'true');
  };

  if (burger && menu) {
    burger.addEventListener('click', () => {
      if (menu.classList.contains('is-open')) closeMenu();
      else openMenu();
    });

    // Click outside to close
    document.addEventListener('click', (e) => {
      if (!menu.classList.contains('is-open')) return;
      const within = menu.contains(e.target) || burger.contains(e.target);
      if (!within) closeMenu();
    });

    // ESC to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMenu();
    });
  }

  // ===== Auth page: switch login/register by hash =====
  const loginPane = document.getElementById('login');
  const registerPane = document.getElementById('register');

  function showAuthPane(which) {
    if (!loginPane || !registerPane) return;
    if (which === 'register') {
      registerPane.classList.remove('display-none');
      loginPane.classList.add('display-none');
    } else {
      // default: login
      loginPane.classList.remove('display-none');
      registerPane.classList.add('display-none');
    }
  }

  const applyHash = () => {
    const h = (location.hash || '').toLowerCase();
    if (h.includes('register')) showAuthPane('register');
    else if (h.includes('login')) showAuthPane('login');
  };

  // On load and on hash change
  applyHash();
  window.addEventListener('hashchange', applyHash);

})();

const toggleThemeBtn = document.querySelector('[data-theme-toggle]');
if (toggleThemeBtn) {
  toggleThemeBtn.addEventListener('click', () => {
    document.body.classList.toggle('theme-dark');
  });
}