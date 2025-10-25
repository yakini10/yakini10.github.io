const track = document.getElementById('track');
const slides = document.querySelectorAll('.slide');
const pager = document.getElementById('pager');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
let pages = [];
let currentPage = 0;

function calculatePages() {
  slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
  pages = [];
  let i = 0;
  while (i < slides.length) {
    pages.push(i);
    i += slidesPerPage;
  }
}

function renderPager() {
  pager.innerHTML = '';
  const pageNumber = document.createElement('span');
  pageNumber.id = 'page-number';
  pageNumber.textContent = `${currentPage + 1} / ${pages.length}`;
  pager.appendChild(pageNumber);

  // Points
  for (let i = 0; i < pages.length; i++) {
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
  const shift = pages[page] * slideWidth;
  track.style.transform = `translateX(-${shift}px)`;
  renderPager();
}

prevBtn.addEventListener('click', () => {
  currentPage = currentPage > 0 ? currentPage - 1 : pages.length - 1;
  goToPage(currentPage);
});

nextBtn.addEventListener('click', () => {
  currentPage = currentPage < pages.length - 1 ? currentPage + 1 : 0;
  goToPage(currentPage);
});

window.addEventListener('resize', () => {
  calculatePages();
  currentPage = 0;
  track.style.transform = 'translateX(0)';
  renderPager();
});

// Initialisation
calculatePages();
goToPage(0);
