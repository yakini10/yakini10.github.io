const track = document.getElementById('track');
const slides = document.querySelectorAll('.slide');
const pager = document.getElementById('pager');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let slidesPerPage = window.innerWidth <= 768 ? 1 : 3;
let totalPages = Math.ceil(slides.length / slidesPerPage);
let currentPage = 0;

function renderDots() {
  pager.innerHTML = '';
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
  const shift = -(page * (100 / (slides.length / slidesPerPage)));
  track.style.transform = `translateX(${shift}%)`;
  updateDots();
}

function updateDots() {
  document.querySelectorAll('.dot').forEach((dot, i) => {
    dot.classList.toggle('active', i === currentPage);
  });
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
  renderDots();
});

renderDots();
