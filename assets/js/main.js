const header = document.querySelector("[data-header]");
const navToggle = document.querySelector("[data-nav-toggle]");
const navLinks = document.querySelectorAll(".nav-link");
const contactForm = document.querySelector("[data-contact-form]");
const formStatus = document.querySelector("[data-form-status]");

if (window.lucide) {
  window.lucide.createIcons({
    strokeWidth: 2.1
  });
}

navToggle?.addEventListener("click", () => {
  const isOpen = header.classList.toggle("is-open");
  navToggle.setAttribute("aria-expanded", String(isOpen));
});

navLinks.forEach((link) => {
  link.addEventListener("click", () => {
    header.classList.remove("is-open");
    navToggle?.setAttribute("aria-expanded", "false");
  });
});

const sections = [...document.querySelectorAll("main section[id], main[id]")];

const setActiveLink = () => {
  const scrollPosition = window.scrollY + 140;
  let activeId = "accueil";

  sections.forEach((section) => {
    const id = section.getAttribute("id");
    if (id && section.offsetTop <= scrollPosition) {
      activeId = id;
    }
  });

  navLinks.forEach((link) => {
    link.classList.toggle("is-active", link.getAttribute("href") === `#${activeId}`);
  });
};

window.addEventListener("scroll", setActiveLink, { passive: true });
setActiveLink();

contactForm?.addEventListener("submit", (event) => {
  event.preventDefault();

  const data = new FormData(contactForm);
  const name = data.get("name") || "";
  const phone = data.get("phone") || "";
  const destination = data.get("destination") || "";
  const message = data.get("message") || "";

  const subject = encodeURIComponent("Demande de rendez-vous ADEPTIO");
  const body = encodeURIComponent(
    `Nom: ${name}\nTéléphone: ${phone}\nDestination: ${destination}\n\nMessage:\n${message}`
  );

  if (formStatus) {
    formStatus.textContent = "Ouverture de votre e-mail...";
  }

  window.location.href = `mailto:info@adeptio.ma?subject=${subject}&body=${body}`;
});
