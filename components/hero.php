<section class="hero-section">
    <!-- Optional Top Text could go here if needed, but keeping it clean as requested -->
    
    <div class="hero-slider" id="heroSlider">
        <div class="hero-wrapper">
            <!-- Slide 1 -->
            <div class="slide">
                <img src="assets/hero/hero-banner.webp" alt="Banner 1">
            </div>
            <!-- Slide 2 -->
            <div class="slide">
                <img src="assets/hero/hero-banner.webp" alt="Banner 2">
            </div>
            <!-- Slide 3 -->
            <div class="slide">
                <img src="assets/hero/hero-banner.webp" alt="Banner 3">
            </div>
        </div>
        
        <!-- Dots for navigation -->
        <div class="slider-dots">
            <span class="dot active" onclick="currentSlide(0)"></span>
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
        </div>
    </div>
</section>

<script>
    let slideIndex = 0;
    const slidesWrapper = document.querySelector('.hero-wrapper');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = document.querySelectorAll('.slide').length;
    let slideInterval;

    function showSlides(n) {
        if (n >= totalSlides) slideIndex = 0;
        if (n < 0) slideIndex = totalSlides - 1;
        
        // Move slider
        slidesWrapper.style.transform = `translateX(-${slideIndex * 100}%)`;
        
        // Update dots
        dots.forEach(dot => dot.classList.remove('active'));
        dots[slideIndex].classList.add('active');
    }

    function currentSlide(n) {
        slideIndex = n;
        showSlides(slideIndex);
        resetTimer();
    }

    function autoPlay() {
        slideIndex++;
        showSlides(slideIndex);
    }

    function resetTimer() {
        clearInterval(slideInterval);
        slideInterval = setInterval(autoPlay, 4000); // 4 seconds per slide
    }

    // Initialize
    slideInterval = setInterval(autoPlay, 4000);
</script>
