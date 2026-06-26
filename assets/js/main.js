// Navigation mobile toggle
document.addEventListener('DOMContentLoaded', function () {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navMenuRight = document.getElementById('navMenuRight');

    if (navToggle) {
        navToggle.addEventListener('click', function () {
            if (navMenu) navMenu.classList.toggle('active');
            if (navMenuRight) navMenuRight.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function (event) {
        if (navToggle && navMenu && !navToggle.contains(event.target) && !navMenu.contains(event.target)) {
            navMenu.classList.remove('active');
            if (navMenuRight) navMenuRight.classList.remove('active');
        }
    });
});

// WhatsApp order function
function sendWhatsAppOrder(productId, productName, price, quantity, attributes) {
    const phoneNumber = '+243975950972'; // Numéro WhatsApp mis à jour
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
