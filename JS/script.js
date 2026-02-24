// 🔹 Gestion du menu burger
const burgerIcon = document.querySelector('.burger-icon');
const navMenu = document.querySelector('.nav-menu');

burgerIcon.addEventListener('click', function() {
    // Toggle la classe 'change' pour l'animation du burger
    this.classList.toggle('change');
    // Toggle la classe 'active' pour afficher/masquer le menu
    navMenu.classList.toggle('active');
});

// 🔹 Fermer le menu burger au clic sur un lien
const navLinks = document.querySelectorAll('.nav-menu a');

navLinks.forEach(link => {
    link.addEventListener('click', () => {
        burgerIcon.classList.remove('change');
        navMenu.classList.remove('active');
    });
});

// 🔹 Fermer le menu si on clique en dehors
document.addEventListener('click', function(event) {
    const isClickInsideMenu = navMenu.contains(event.target);
    const isClickOnBurger = burgerIcon.contains(event.target);
    
    if (!isClickInsideMenu && !isClickOnBurger && navMenu.classList.contains('active')) {
        burgerIcon.classList.remove('change');
        navMenu.classList.remove('active');
    }
});

// 🔹 Animation des barres de compétences au scroll
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const progressBars = entry.target.querySelectorAll('.skill-progress');
            progressBars.forEach(bar => {
                bar.style.width = bar.style.getPropertyValue('--progress');
            });
        }
    });
}, observerOptions);

const skillsSection = document.getElementById('competences');
if (skillsSection) {
    observer.observe(skillsSection);
}

// 🔹 Scroll fluide pour les ancres
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// 🔹 Gestion des fenêtres modales
document.querySelectorAll(".modal-button-open").forEach((btn) => {
    btn.addEventListener("click", () => {
        const id = btn.getAttribute("data-modal-id");
      document.getElementById(id).classList.add("modal-show");
    });
  });
  
  document.querySelectorAll(".modal-button-close").forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.closest("div.modal-div-wrapper").classList.remove("modal-show");
    });
  });