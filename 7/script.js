document.addEventListener('DOMContentLoaded', function() {
    const slidesContainer = document.querySelector('.slides-container');
    const slides = document.querySelectorAll('.slide');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const currentPageSpan = document.querySelector('.current-page');
    const totalPagesSpan = document.querySelector('.total-pages');
    
    let currentIndex = 0;
    let slidesToShow = 3;
    
    // Calculer le nombre total de pages
    function calculateTotalPages() {
        return Math.ceil(slides.length / slidesToShow);
    }
    
    // Mettre à jour l'affichage en fonction de la taille d'écran
    function updateSlidesToShow() {
        if (window.innerWidth <= 768) {
            slidesToShow = 1;
        } else {
            slidesToShow = 3;
        }
        
        // Recalculer le nombre total de pages
        const totalPages = calculateTotalPages();
        totalPagesSpan.textContent = totalPages;
        
        // Ajuster l'index courant si nécessaire
        if (currentIndex >= totalPages) {
            currentIndex = totalPages - 1;
            updateSlider();
        }
    }
    
    // Mettre à jour la position du slider
    function updateSlider() {
        const slideWidth = 100 / slidesToShow;
        const translateX = -currentIndex * slideWidth;
        slidesContainer.style.transform = `translateX(${translateX}%)`;
        
        // Mettre à jour le pager
        currentPageSpan.textContent = currentIndex + 1;
    }
    
    // Aller à l'image suivante
    function nextSlide() {
        const totalPages = calculateTotalPages();
        if (currentIndex < totalPages - 1) {
            currentIndex++;
        } else {
            currentIndex = 0; // Retour à la première page
        }
        updateSlider();
    }
    
    // Aller à l'image précédente
    function prevSlide() {
        const totalPages = calculateTotalPages();
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = totalPages - 1; // Aller à la dernière page
        }
        updateSlider();
    }
    
    // Initialisation
    updateSlidesToShow();
    updateSlider();
    
    // Événements
    prevButton.addEventListener('click', prevSlide);
    nextButton.addEventListener('click', nextSlide);
    
    // Mettre à jour lors du redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        updateSlidesToShow();
        updateSlider();
    });
});
