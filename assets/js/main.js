// Navigation mobile toggle
document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function (event) {
        if (!navToggle.contains(event.target) && !navMenu.contains(event.target)) {
            navMenu.classList.remove('active');
        }
    });
});

// Product image gallery
function initProductGallery() {
    const thumbnails = document.querySelectorAll('.product-thumbnail');
    const mainImage = document.querySelector('.product-main-image');

    if (thumbnails.length > 0 && mainImage) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function () {
                // Remove active class from all thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));
                // Add active class to clicked thumbnail
                this.classList.add('active');
                // Update main image
                mainImage.src = this.src.replace('thumb_', '');
            });
        });
    }
}

// Initialize product gallery on page load
document.addEventListener('DOMContentLoaded', initProductGallery);

// Attribute selection
function initAttributeSelection() {
    const attributeOptions = document.querySelectorAll('.attribute-option');

    attributeOptions.forEach(option => {
        option.addEventListener('click', function () {
            const group = this.closest('.attribute-group');
            const options = group.querySelectorAll('.attribute-option');
            options.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
}

document.addEventListener('DOMContentLoaded', initAttributeSelection);

// WhatsApp order function
function sendWhatsAppOrder(productId, productName, price, quantity, attributes) {
    const phoneNumber = '221XXXXXXXX'; // Remplacer par le numéro réel
    let message = `Bonjour, je souhaite commander:\n\n`;
    message += `Produit: ${productName}\n`;
    message += `Prix: $${price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}\n`;
    message += `Quantité: ${quantity}\n`;

    if (attributes && attributes.length > 0) {
        message += `\nOptions:\n`;
        attributes.forEach(attr => {
            message += `- ${attr.type}: ${attr.value}\n`;
        });
    }

    message += `\nMerci de me confirmer la disponibilité et les modalités de livraison.`;

    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

// Search functionality
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                const productName = card.querySelector('h3').textContent.toLowerCase();
                const productCategory = card.querySelector('.product-category').textContent.toLowerCase();

                if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}
// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }
});
document.addEventListener('DOMContentLoaded', initSearch);

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function (e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    }
}

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Hero Slider
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.prev-slide');
    const nextBtn = document.querySelector('.next-slide');

    if (slides.length > 0) {
        let currentSlide = 0;

        // Show specific slide
        function showSlide(n) {
            // Reset index if out of bounds
            if (n >= slides.length) currentSlide = 0;
            if (n < 0) currentSlide = slides.length - 1;

            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Show current
            slides[currentSlide].classList.add('active');
            if (dots.length > 0) dots[currentSlide].classList.add('active');
        }

        // Next/Prev events
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentSlide++;
                showSlide(currentSlide);
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentSlide--;
                showSlide(currentSlide);
            });
        }

        // Dot events
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        // Auto play (optional - 5 seconds)
        setInterval(() => {
            currentSlide++;
            showSlide(currentSlide);
        }, 8000);
    }
});
