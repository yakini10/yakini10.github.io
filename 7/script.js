document.addEventListener('DOMContentLoaded', function() {
    const slidesContainer = document.querySelector('.slides-container');
    const slides = document.querySelectorAll('.slide');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const currentPageSpan = document.querySelector('.current-page');
    const totalPagesSpan = document.querySelector('.total-pages');
    
    let currentIndex = 0;
    let slidesToShow = 3;
    
    // Функция для расчета общего количества страниц
    function calculateTotalPages() {
        return Math.ceil(slides.length / slidesToShow);
    }
    
    // Функция для обновления количества отображаемых слайдов
    function updateSlidesToShow() {
        if (window.innerWidth <= 768) {
            slidesToShow = 1;
        } else {
            slidesToShow = 3;
        }
        
        // Пересчитываем общее количество страниц
        const totalPages = calculateTotalPages();
        totalPagesSpan.textContent = totalPages;
        
        // Корректируем текущий индекс при необходимости
        if (currentIndex >= totalPages) {
            currentIndex = totalPages - 1;
            updateSlider();
        }
    }
    
    // Функция для обновления положения слайдера
    function updateSlider() {
        const slideWidth = 100 / slidesToShow;
        const translateX = -currentIndex * slideWidth;
        slidesContainer.style.transform = `translateX(${translateX}%)`;
        
        // Обновляем пейджер
        currentPageSpan.textContent = currentIndex + 1;
    }
    
    // Функция для перехода к следующему слайду
    function nextSlide() {
        const totalPages = calculateTotalPages();
        if (currentIndex < totalPages - 1) {
            currentIndex++;
        } else {
            currentIndex = 0; // Возврат к первой странице
        }
        updateSlider();
    }
    
    // Функция для перехода к предыдущему слайду
    function prevSlide() {
        const totalPages = calculateTotalPages();
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = totalPages - 1; // Переход к последней странице
        }
        updateSlider();
    }
    
    // Инициализация
    updateSlidesToShow();
    updateSlider();
    
    // Обработчики событий
    prevButton.addEventListener('click', prevSlide);
    nextButton.addEventListener('click', nextSlide);
    
    // Обновление при изменении размера окна
    window.addEventListener('resize', function() {
        updateSlidesToShow();
        updateSlider();
    });
    
    // Добавляем поддержку клавиатуры
    document.addEventListener('keydown', function(event) {
        if (event.key === 'ArrowLeft') {
            prevSlide();
        } else if (event.key === 'ArrowRight') {
            nextSlide();
        }
    });
});
