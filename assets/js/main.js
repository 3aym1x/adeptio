/* ── Lucide icons ──────────────────────────────────────── */
const initLucide = () => {
  if (window.lucide) window.lucide.createIcons({ strokeWidth: 2.1 });
};
initLucide();
// Retry after a tick in case the CDN script executes slightly after main.js
setTimeout(initLucide, 0);

/* ── Header / nav toggle ───────────────────────────────── */
const header    = document.querySelector('[data-header]');
const navToggle = document.querySelector('[data-nav-toggle]');
const navLinks  = document.querySelectorAll('.nav-link');

navToggle?.addEventListener('click', () => {
  const isOpen = header.classList.toggle('is-open');
  navToggle.setAttribute('aria-expanded', String(isOpen));
});

navLinks.forEach(link => {
  link.addEventListener('click', () => {
    header?.classList.remove('is-open');
    navToggle?.setAttribute('aria-expanded', 'false');
  });
});

/* ── Active nav on scroll ──────────────────────────────── */
const sections = [...document.querySelectorAll('main section[id], main[id]')];

const setActiveLink = () => {
  const pos = window.scrollY + 140;
  let activeId = sections[0]?.id || 'accueil';
  sections.forEach(s => {
    if (s.id && s.offsetTop <= pos) activeId = s.id;
  });
  navLinks.forEach(link => {
    link.classList.toggle('is-active', link.getAttribute('href') === `#${activeId}`);
  });
};

if (sections.length > 0) {
  window.addEventListener('scroll', setActiveLink, { passive: true });
  setActiveLink();
}

/* ── Scroll-reveal animation ───────────────────────────── */
const revealObserver = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('is-visible');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0, rootMargin: '0px 0px -40px 0px' });

// Add class to body so CSS only hides elements when JS is running
document.body.classList.add('js-reveal-ready');

// Wait for layout before observing so in-viewport elements trigger correctly
requestAnimationFrame(() => {
  document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
});

// Hard fallback: reveal everything after 600ms in case the observer misfires
setTimeout(() => {
  document.querySelectorAll('.reveal:not(.is-visible)').forEach(el => {
    el.classList.add('is-visible');
  });
}, 600);

/* ── Destination hero slow-zoom trigger ────────────────── */
const destHero = document.querySelector('.dest-hero');
if (destHero) setTimeout(() => destHero.classList.add('is-loaded'), 80);

/* ── Pre-fill destination from URL ?destination= ──────── */
const destParam = new URLSearchParams(window.location.search).get('destination');
if (destParam) {
  const sel = document.querySelector('[name="destination"]');
  if (sel) {
    const opt = [...sel.options].find(
      o => o.value.toLowerCase() === destParam.toLowerCase() ||
           o.textContent.toLowerCase() === destParam.toLowerCase()
    );
    if (opt) opt.selected = true;
  }
}

/* ── Contact form — ADEPTIO backend ────────────────────── */
/* Relative path so it works both under a subfolder (localhost/ADEPTIO/)
   and at a domain root. All pages live at the same depth.              */
const CONTACT_ENDPOINT = 'api/submit-form.php';

const contactForm = document.querySelector('[data-contact-form]');
const formStatus  = document.querySelector('[data-form-status]');

contactForm?.addEventListener('submit', async event => {
  event.preventDefault();

  const btn      = contactForm.querySelector('[type="submit"]');
  const btnSpan  = btn?.querySelector('span');
  const origText = btnSpan?.textContent ?? 'Envoyer';

  if (btn) { btn.disabled = true; }
  if (btnSpan) btnSpan.textContent = 'Envoi en cours…';
  if (formStatus) { formStatus.textContent = ''; formStatus.style.color = ''; }

  // Tag the submission with the page it came from.
  const data = new FormData(contactForm);
  data.set('source_page', window.location.pathname);

  try {
    const res = await fetch(CONTACT_ENDPOINT, {
      method:  'POST',
      body:    data,
      headers: { Accept: 'application/json' }
    });
    const result = await res.json().catch(() => ({}));

    if (res.ok && result.ok) {
      if (formStatus) {
        formStatus.textContent = result.message || 'Message envoyé ! Nous vous recontacterons très bientôt.';
        formStatus.style.color = '#16a34a';
      }
      contactForm.reset();
    } else {
      throw new Error(result.error || 'non-ok');
    }
  } catch (err) {
    if (formStatus) {
      formStatus.textContent = (err && err.message && err.message !== 'non-ok')
        ? err.message
        : 'Une erreur est survenue. Réessayez ou écrivez-nous directement.';
      formStatus.style.color = 'var(--orange)';
    }
  } finally {
    if (btn) btn.disabled = false;
    if (btnSpan) btnSpan.textContent = origText;
  }
});

/* ── Visit tracking — ping backend on load ─────────────── */
(() => {
  const body = new FormData();
  body.set('page', window.location.pathname);
  body.set('referrer', document.referrer || '');
  fetch('api/track-visit.php', {
    method: 'POST',
    body,
    keepalive: true
  }).catch(() => { /* tracking is best-effort, never block the page */ });
})();
