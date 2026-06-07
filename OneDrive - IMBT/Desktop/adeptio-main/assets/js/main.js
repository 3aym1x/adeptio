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

/* ── Contact form — Formspree ──────────────────────────── */
/* TODO: remplacer YOUR_FORM_ID par votre identifiant Formspree  */
/*       Créer un compte sur https://formspree.io et copier l'ID  */
const FORMSPREE_ENDPOINT = 'https://formspree.io/f/YOUR_FORM_ID';

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

  try {
    const res = await fetch(FORMSPREE_ENDPOINT, {
      method:  'POST',
      body:    new FormData(contactForm),
      headers: { Accept: 'application/json' }
    });
    if (res.ok) {
      if (formStatus) {
        formStatus.textContent = 'Message envoyé ! Nous vous recontacterons très bientôt.';
        formStatus.style.color = '#16a34a';
      }
      contactForm.reset();
    } else {
      throw new Error('non-ok');
    }
  } catch {
    if (formStatus) {
      formStatus.textContent = 'Une erreur est survenue. Réessayez ou écrivez-nous directement.';
      formStatus.style.color = 'var(--orange)';
    }
  } finally {
    if (btn) btn.disabled = false;
    if (btnSpan) btnSpan.textContent = origText;
  }
});
