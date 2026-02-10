<section class="testimonials-section">
    <div class="testimonials-container">
        
        <div class="section-header text-center">
            <h2>Meet Our Happy Customers</h2>
            <p>Real stories from women who trust WaryChary</p>
        </div>

        <div class="testimonials-slider-relative">
            <!-- Navigation Buttons -->
            <button class="testimonial-nav-btn nav-prev" onclick="scrollTestimonials(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="testimonial-nav-btn nav-next" onclick="scrollTestimonials(1)"><i class="fas fa-chevron-right"></i></button>

            <div class="testimonials-wrapper" id="testimonialsList">
                
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-img-wrapper">
                        <!-- Placeholder generic Indian girl image -->
                        <img src="../assets/images/Akansha.webp" alt="Anya" class="testimonial-img">
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <div class="testimonial-name-badge">ANYA</div>
                    <h4 class="testimonial-title">Quite absorbing for a pad w/o phthalates</h4>
                    <p class="testimonial-desc">"These pads don't lack the potential to deliver fluids to lower layers. While low in aesthetics, they are quite absorbent for organic sanitation."</p>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <div class="testimonial-img-wrapper">
                        <img src="https://media.licdn.com/dms/image/v2/D4D03AQE8X5y9x_L9Dg/profile-displayphoto-shrink_200_200/profile-displayphoto-shrink_200_200/0/1709971092923?e=2147483647&v=beta&t=Zk4zB7t8g-m2y5s2z9z4z8w8s8s8s8s8" alt="Naomi" class="testimonial-img">
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <div class="testimonial-name-badge">NAOMI</div>
                    <h4 class="testimonial-title">Really effective and affordable pads</h4>
                    <p class="testimonial-desc">"I've bought this box of 40 over 10 times. I truly love this product and recommend it over generic brands. Comfortable and don't slide around."</p>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card">
                    <div class="testimonial-img-wrapper">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRz-hH-l-l-l-l-l-l-l-l-l-l-l&s" alt="Akanksha" class="testimonial-img">
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <div class="testimonial-name-badge">AKANKSHA</div>
                    <h4 class="testimonial-title">Most effective brand</h4>
                    <p class="testimonial-desc">"I have been using this for the last 4 months. Trust me it is the most hygienic and best quality product. I suggested this to my younger sisters as well."</p>
                </div>

                <!-- Testimonial 4 -->
                <div class="testimonial-card">
                    <div class="testimonial-img-wrapper">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ-l-l-l-l-l-l-l-l-l-l-l-l-l&s" alt="Nashi" class="testimonial-img">
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <div class="testimonial-name-badge">NASHI</div>
                    <h4 class="testimonial-title">Must buy product</h4>
                    <p class="testimonial-desc">"It's 100% rash free, I really love the softness and it's 100% cotton as well. Don't think about the money, it's worth the price."</p>
                </div>

                 <!-- Testimonial 5 -->
                 <div class="testimonial-card">
                    <div class="testimonial-img-wrapper">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR-l-l-l-l-l-l-l-l-l-l-l-l-l&s" alt="Priya" class="testimonial-img">
                    </div>
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <div class="testimonial-name-badge">PRIYA</div>
                    <h4 class="testimonial-title">Best for sensitive skin</h4>
                    <p class="testimonial-desc">"Finally found something that works for my sensitive skin. No irritation at all, highly recommended for everyone."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function scrollTestimonials(direction) {
        const wrapper = document.getElementById('testimonialsList');
        const scrollAmount = 300; // Approx card width
        wrapper.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>
