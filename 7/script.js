const track = document.getElementById('track');
const slides = document.querySelectorAll('.slide');
const pager = document.getElementById('pager');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
let pages = [];
let currentPage = 0;

// Calcul des pages en fonction du nombre de slides et slidesPerPage
function calculatePages() {
  slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
  pages = [];
  let i = 0;
  while (i < slides.length) {
    pages.push(i);
    i += slidesPerPage;
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
  for (let i = 0; i < pages.length; i++) {
    const dot = document.createElement('span');
    dot.classList.add('dot');
    if (i === currentPage) dot.classList.add('active');
    dot.addEventListener('click', () => goToPage(i));
    pager.appendChild(dot);
  }
}

// Aller à une page spécifique
function goToPage(page) {
  currentPage = page;
  const slideWidth = slides[0].offsetWidth;
  const shift = pages[page] * slideWidth;
  track.style.transform = `translateX(-${shift}px)`;
  renderPager();
}

// Flèche gauche
prevBtn.addEventListener('click', () => {
  if(currentPage > 0){
    currentPage--;
  } else {
    currentPage = pages.length - 1;
  }
  goToPage(currentPage);
});

// Flèche droite
nextBtn.addEventListener('click', () => {
  if(currentPage < pages.length - 1){
    currentPage++;
  } else {
    currentPage = 0;
  }
  goToPage(currentPage);
});

// Recalculer les pages et réinitialiser le slider au redimensionnement
window.addEventListener('resize', () => {
  calculatePages();
  currentPage = 0;
  track.style.transform = 'translateX(0)';
  renderPager();
});

// Initialisation
calculatePages();
goToPage(0);
