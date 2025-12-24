// Wearsy Fashion Store - Main JavaScript

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initHeroSlider();
    initImageZoom();
    initQuantityControls();
    initFormValidation();
    initLazyLoading();
    initScrollToTop();
});

// Hero Slider
function initHeroSlider() {
    const slider = document.querySelector('.hero-slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('.hero-dot');
    const prevBtn = slider.querySelector('.hero-arrow-prev');
    const nextBtn = slider.querySelector('.hero-arrow-next');

    if (!slides.length) return;

    let currentIndex = 0;
    let autoPlayTimer = null;
    // Thời gian tự động chuyển slide (ms)
    const AUTO_PLAY_INTERVAL = 4000;

    function goToSlide(index, options = {}) {
        const { animate = true, direction = 1 } = options;
        const total = slides.length;
        const newIndex = (index + total) % total;

        if (newIndex === currentIndex && animate) return;

        const currentSlide = slides[currentIndex];
        const nextSlideEl = slides[newIndex];

        // Cập nhật dot
        if (dots.length) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === newIndex);
            });
        }

        if (!animate) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === newIndex);
                slide.style.transform = '';
                slide.style.opacity = '';
            });
            currentIndex = newIndex;
            return;
        }

        if (currentSlide === nextSlideEl) return;

        const offsetIn = direction === 1 ? '100%' : '-100%';
        const offsetOut = direction === 1 ? '-100%' : '100%';

        // Chuẩn bị slide mới ở ngoài khung nhìn
        nextSlideEl.classList.add('active');
        nextSlideEl.style.visibility = 'visible';
        nextSlideEl.style.opacity = '1';
        nextSlideEl.style.transform = `translateX(${offsetIn})`;

        // Buộc reflow để transition hoạt động
        void nextSlideEl.offsetWidth;

        // Animate trượt
        currentSlide.style.transform = `translateX(${offsetOut})`;
        currentSlide.style.opacity = '0';
        nextSlideEl.style.transform = 'translateX(0)';

        // Sau khi kết thúc animation, dọn dẹp
        setTimeout(() => {
            currentSlide.classList.remove('active');
            currentSlide.style.transform = '';
            currentSlide.style.opacity = '';
            nextSlideEl.style.transform = '';
        }, 600);

        currentIndex = newIndex;
    }

    function nextSlide() {
        goToSlide(currentIndex + 1, { animate: true, direction: 1 });
    }

    function prevSlide() {
        goToSlide(currentIndex - 1, { animate: true, direction: -1 });
    }

    function startAutoPlay() {
        if (autoPlayTimer) clearInterval(autoPlayTimer);
        autoPlayTimer = setInterval(nextSlide, AUTO_PLAY_INTERVAL);
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            nextSlide();
            startAutoPlay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            prevSlide();
            startAutoPlay();
        });
    }

    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-slide'), 10);
            if (!isNaN(index)) {
                const direction = index > currentIndex ? 1 : -1;
                goToSlide(index, { animate: true, direction });
                startAutoPlay();
            }
        });
    });

    slider.addEventListener('mouseenter', stopAutoPlay);
    slider.addEventListener('mouseleave', startAutoPlay);

    // Khởi tạo slide đầu tiên không animation
    goToSlide(0, { animate: false });
    startAutoPlay();
}

// Mobile Menu Toggle
function initMobileMenu() {
    const navMenu = document.querySelector('.nav-menu');
    if (window.innerWidth <= 768) {
        // Add mobile menu button if not exists
        if (!document.querySelector('.mobile-menu-toggle')) {
            const toggle = document.createElement('button');
            toggle.className = 'mobile-menu-toggle';
            toggle.innerHTML = '<i class="fas fa-bars"></i>';
            toggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                this.innerHTML = navMenu.classList.contains('active') 
                    ? '<i class="fas fa-times"></i>' 
                    : '<i class="fas fa-bars"></i>';
            });
            document.querySelector('.main-nav .container').prepend(toggle);
        }
    }
}

// Image Zoom on Product Detail
function initImageZoom() {
    const mainImage = document.querySelector('.main-image img');
    if (mainImage) {
        mainImage.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.2)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        mainImage.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
}

// Quantity Controls
function initQuantityControls() {
    const quantityInputs = document.querySelectorAll('.quantity-input, #quantity');
    
    quantityInputs.forEach(input => {
        // Prevent negative values
        input.addEventListener('change', function() {
            if (this.value < 1) {
                this.value = 1;
            }
            if (this.max && this.value > this.max) {
                this.value = this.max;
            }
        });
        
        // Prevent non-numeric input
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
                e.preventDefault();
            }
        });
    });
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    showError(field, 'Email không hợp lệ');
                }
            });
            
            // Password validation
            const passwordFields = form.querySelectorAll('input[type="password"]');
            passwordFields.forEach(field => {
                if (field.value && field.value.length < 6) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    showError(field, 'Mật khẩu phải có ít nhất 6 ký tự');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
}

// Email Validation Helper
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Show Error Message
function showError(field, message) {
    let errorDiv = field.parentElement.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '5px';
        field.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    
    setTimeout(() => {
        errorDiv.remove();
        field.style.borderColor = '';
    }, 5000);
}

// Lazy Loading Images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
        });
    }
}

// Scroll to Top Button
function initScrollToTop() {
    // Create scroll to top button
    const scrollBtn = document.createElement('button');
    scrollBtn.className = 'scroll-to-top';
    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #2c5282 0%, #3182ce 100%);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 15px rgba(44, 82, 130, 0.3);
        transition: all 0.3s ease;
        z-index: 999;
    `;
    
    document.body.appendChild(scrollBtn);
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollBtn.style.display = 'flex';
        } else {
            scrollBtn.style.display = 'none';
        }
    });
    
    // Scroll to top on click
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Hover effect
    scrollBtn.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px) scale(1.1)';
        this.style.boxShadow = '0 6px 20px rgba(44, 82, 130, 0.4)';
    });
    
    scrollBtn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
        this.style.boxShadow = '0 4px 15px rgba(44, 82, 130, 0.3)';
    });
}

// Add to Cart Animation
function addToCartAnimation(productName) {
    // Create notification
    const notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>Đã thêm "${productName}" vào giỏ hàng!</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: linear-gradient(135deg, #48bb78 0%, #4299e1 100%);
        color: white;
        padding: 15px 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 184, 148, 0.3);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Search Auto-complete (if needed)
function initSearchAutocomplete() {
    const searchInput = document.querySelector('.search-box input');
    if (!searchInput) return;
    
    let timeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideAutocomplete();
            return;
        }
        
        timeout = setTimeout(() => {
            // Implement search autocomplete here
            // fetchSearchSuggestions(query);
        }, 300);
    });
}

// Product Quick View (if needed)
function initQuickView() {
    const quickViewButtons = document.querySelectorAll('.quick-view-btn');
    
    quickViewButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            // Implement quick view modal here
        });
    });
}

// Price Range Slider (if needed)
function initPriceSlider() {
    const priceSlider = document.getElementById('price-slider');
    if (!priceSlider) return;
    
    // Implement price range slider here
}

// Wishlist Toggle Animation
function toggleWishlist(productId, element) {
    const icon = element.querySelector('i');
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        element.style.color = '#e74c3c';
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        element.style.color = '';
    }
}

// Smooth Scroll for Anchor Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Console Log for Debug
console.log('Wearsy Fashion Store - JavaScript Loaded');

