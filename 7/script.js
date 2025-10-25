const track = document.getElementById('track');
const slides = document.querySelectorAll('.slide');
const pager = document.getElementById('pager');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let slidesPerPage = 3;
let pages = [];
let currentPage = 0;

// Calcul des pages : indices de départ
function calculatePages() {
  slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
  pages = [];
  for (let i = 0; i < slides.length; i += slidesPerPage) {
    pages.push(i);
  }
}

// Affichage pager et numéro de page
function renderPager() {
  pager.innerHTML = '';

  const pageNumber = document.createElement('span');
  pageNumber.id = 'page-number';
  pageNumber.textContent = `${currentPage + 1} / ${pages.length}`;
  pager.appendChild(pageNumber);

  pages.forEach((_, i) => {
    const dot = document.createElement('span');
    dot.classList.add('dot');
    if (i === currentPage) dot.classList.add('active');
    dot.addEventListener('click', () => goToPage(i));
    pager.appendChild(dot);
  });
}

// Aller à une page
function goToPage(page) {
  currentPage = page;
  const totalWidth = track.scrollWidth;
  const containerWidth = track.parentElement.offsetWidth;

  // Décalage en % pour que ça marche sur toutes tailles
  let shiftPercent = (pages[page] / slides.length) * 100;
  // Limite max
  const maxShiftPercent = ((slides.length - slidesPerPage) / slides.length) * 100;
  if (shiftPercent > maxShiftPercent) shiftPercent = maxShiftPercent;

  track.style.transform = `translateX(-${shiftPercent}%)`;
  renderPager();
}

// Flèche gauche
prevBtn.addEventListener('click', () => {
  currentPage = currentPage > 0 ? currentPage - 1 : pages.length - 1;
  goToPage(currentPage);
});

// Flèche droite
nextBtn.addEventListener('click', () => {
  currentPage = currentPage < pages.length - 1 ? currentPage + 1 : 0;
  goToPage(currentPage);
});

// Recalcul sur resize
window.addEventListener('resize', () => {
  calculatePages();
  currentPage = 0;
  track.style.transform = 'translateX(0)';
  renderPager();
});

// Initialisation
calculatePages();
goToPage(0);
