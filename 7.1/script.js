// Récupération des éléments du DOM
const track = document.getElementById('track');
const slides = document.querySelectorAll('.slide');
const pager = document.getElementById('pager');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

// Variables d'état
let slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
let totalPages = Math.ceil(slides.length / slidesPerPage);
let currentPage = 0;
let startX = 0;
let currentX = 0;
let isDragging = false;

// Fonction pour mettre à jour l'affichage de la pagination
function updatePager() {
  pager.innerHTML = `Page <span class="pagination-text">${currentPage + 1}</span> sur <span class="pagination-text">${totalPages}</span>`;
}

// Fonction pour aller à une page spécifique
function goToPage(page) {
  // Vérification des limites
  if (page < 0) page = 0;
  if (page >= totalPages) page = totalPages - 1;
  
  currentPage = page;
  
  // Calcul du décalage en pourcentage
  const shift = -(currentPage * (100 / slidesPerPage));
  track.style.transform = `translateX(${shift}%)`;
  
  // Mise à jour de l'affichage de la pagination
  updatePager();
}

// Fonction pour gérer le redimensionnement de la fenêtre
function handleResize() {
  // Détermination du nombre de slides par page selon la largeur d'écran
  slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
  
  // Calcul du nombre total de pages
  totalPages = Math.ceil(slides.length / slidesPerPage);
  
  // Réinitialisation à la première page
  currentPage = 0;
  goToPage(currentPage);
}

// Fonction pour gérer le début du balayage tactile
function handleTouchStart(e) {
  isDragging = true;
  startX = e.touches[0].clientX;
  currentX = startX;
}

// Fonction pour gérer le mouvement pendant le balayage tactile
function handleTouchMove(e) {
  if (!isDragging) return;
  
  currentX = e.touches[0].clientX;
  const diff = startX - currentX;
  
  // Appliquer un décalage temporaire pendant le balayage
  const baseShift = -(currentPage * (100 / slidesPerPage));
  const dragShift = (diff / track.offsetWidth) * 100;
  track.style.transform = `translateX(calc(${baseShift}% + ${-dragShift}px))`;
}

// Fonction pour gérer la fin du balayage tactile
function handleTouchEnd(e) {
  if (!isDragging) return;
  
  isDragging = false;
  const diff = startX - currentX;
  const threshold = 50; // Seuil minimal pour changer de page
  
  // Déterminer la direction du balayage
  if (diff > threshold) {
    // Balayage vers la gauche - page suivante
    goToPage(currentPage + 1);
  } else if (diff < -threshold) {
    // Balayage vers la droite - page précédente
    goToPage(currentPage - 1);
  } else {
    // Retour à la page actuelle si le balayage est insuffisant
    goToPage(currentPage);
  }
}

// Écouteurs d'événements pour les boutons de navigation
prevBtn.addEventListener('click', () => {
  goToPage(currentPage - 1);
});

nextBtn.addEventListener('click', () => {
  goToPage(currentPage + 1);
});

// Écouteurs d'événements pour le balayage tactile
track.addEventListener('touchstart', handleTouchStart, { passive: true });
track.addEventListener('touchmove', handleTouchMove, { passive: true });
track.addEventListener('touchend', handleTouchEnd);

// Écouteur d'événement pour le redimensionnement de la fenêtre
window.addEventListener('resize', handleResize);

// Écouteur d'événement pour les touches du clavier
document.addEventListener('keydown', (e) => {
  if (e.key === 'ArrowLeft') {
    goToPage(currentPage - 1);
  } else if (e.key === 'ArrowRight') {
    goToPage(currentPage + 1);
  }
});

// Initialisation
updatePager();
