const track = document.getElementById('track');
const slides = document.querySelectorAll('.slide');
const pager = document.getElementById('pager');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
let totalPages = Math.ceil(slides.length / slidesPerPage);
let currentPage = 0;

function renderPager() {
  pager.innerHTML = '';
  // Affichage numéro de page
  const pageNumber = document.createElement('span');
  pageNumber.id = 'page-number';
  pageNumber.textContent = `${currentPage + 1} / ${totalPages}`;
  pager.appendChild(pageNumber);

  // Points
  for (let i = 0; i < totalPages; i++) {
    const dot = document.createElement('span');
    dot.classList.add('dot');
    if (i === currentPage) dot.classList.add('active');
    dot.addEventListener('click', () => goToPage(i));
    pager.appendChild(dot);
  }
}

function goToPage(page) {
  currentPage = page;

  const slideWidth = slides[0].offsetWidth;
  const maxShift = (slides.length * slideWidth) - (slidesPerPage * slideWidth);
  let shift = page * slidesPerPage * slideWidth;

  if (shift > maxShift) shift = maxShift; // dernière page

  track.style.transform = `translateX(-${shift}px)`;
  renderPager();
}

prevBtn.addEventListener('click', () => {
  currentPage = (currentPage > 0) ? currentPage - 1 : totalPages - 1;
  goToPage(currentPage);
});

nextBtn.addEventListener('click', () => {
  currentPage = (currentPage < totalPages - 1) ? currentPage + 1 : 0;
  goToPage(currentPage);
});

window.addEventListener('resize', () => {
  slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
  totalPages = Math.ceil(slides.length / slidesPerPage);
  currentPage = 0;
  track.style.transform = 'translateX(0)';
  renderPager();
});

goToPage(0);
