const track = document.getElementById('track');
const slides = document.querySelectorAll('.slide');
const pager = document.getElementById('pager');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let slidesPerPage = 3;
let pages = [];
let currentPage = 0;

// Calcul des pages : indices de slide de départ de chaque page
function calculatePages() {
  slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
  pages = [];
  for (let i = 0; i < slides.length; i += slidesPerPage) {
    pages.push(i);
  }
}

// Affichage du pager avec numéro de page et points
function renderPager() {
  pager.innerHTML = '';

  // Numéro de page
  const pageNumber = document.createElement('span');
  pageNumber.id = 'page-number';
  pageNumber.textContent = `${currentPage + 1} / ${pages.length}`;
  pager.appendChild(pageNumber);

  // Points cliquables
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
  const slideWidth = slides[0].getBoundingClientRect().width;
  let shift = pages[page] * slideWidth;

  // Limite pour ne pas dépasser la largeur totale du slider
  const maxShift = track.scrollWidth - track.parentElement.offsetWidth;
  if (shift > maxShift) shift = maxShift;

  track.style.transform = `translateX(-${shift}px)`;
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
