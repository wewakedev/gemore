/*
 * GeMore Nutrients - Optimized JavaScript
 * Consolidated and optimized scripts for better performance
 */

// Navigation functions
function openNav() {
  document.getElementById("myNav").style.width = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.width = "0%";
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Navbar mobile toggle
  const toggle = document.getElementById("navbar-toggle");
  const links = document.getElementById("navbar-links");

  if (toggle && links) {
    toggle.addEventListener("click", function () {
      links.classList.toggle("active");
      toggle.classList.toggle("active");
    });
  }

  // Product Slider functionality
  const slider = document.getElementById("productSlider");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const slides = document.querySelectorAll(".product-slide");

  if (slider && slides.length > 0) {
    let currentSlide = 0;
    const totalSlides = slides.length;
    let autoSlideInterval;

    function updateSlider() {
      slider.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    function goToSlide(slideIndex) {
      currentSlide = slideIndex;
      updateSlider();
    }

    function nextSlide() {
      currentSlide = (currentSlide + 1) % totalSlides;
      updateSlider();
    }

    function prevSlide() {
      currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
      updateSlider();
    }

    // Event listeners
    if (nextBtn) nextBtn.addEventListener("click", nextSlide);
    if (prevBtn) prevBtn.addEventListener("click", prevSlide);

    // Auto-slide functionality
    function startAutoSlide() {
      autoSlideInterval = setInterval(nextSlide, 5000);
    }

    function stopAutoSlide() {
      clearInterval(autoSlideInterval);
    }

    // Start auto-slide
    startAutoSlide();

    // Pause auto-slide on hover
    const sliderContainer = document.querySelector(".product-slider-container");
    if (sliderContainer) {
      sliderContainer.addEventListener("mouseenter", stopAutoSlide);
      sliderContainer.addEventListener("mouseleave", startAutoSlide);
    }

    // Keyboard navigation
    document.addEventListener("keydown", function (e) {
      if (e.key === "ArrowLeft") {
        prevSlide();
      } else if (e.key === "ArrowRight") {
        nextSlide();
      }
    });
  }

  // Enhanced Contact Form Handler
  const contactForm = document.getElementById("contactForm");
  const formMessages = document.getElementById("form-messages");

  if (contactForm && formMessages) {
    contactForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;

      // Show loading state
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      submitBtn.disabled = true;
      formMessages.style.display = "block";

      try {
        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData);

        const response = await fetch("/send-contact", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        if (response.ok) {
          formMessages.className = "alert alert-success mt-3";
          formMessages.innerHTML =
            '<i class="fas fa-check-circle"></i> ' +
            (result.message ||
              "Message sent successfully! We'll get back to you soon.");
          contactForm.reset();
        } else {
          formMessages.className = "alert alert-danger mt-3";
          formMessages.innerHTML =
            '<i class="fas fa-exclamation-triangle"></i> ' +
            (result.error || "Failed to send message. Please try again.");
        }
      } catch (error) {
        formMessages.className = "alert alert-danger mt-3";
        formMessages.innerHTML =
          '<i class="fas fa-exclamation-triangle"></i> Network error. Please check your connection and try again.';
      }

      // Reset button
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;

      // Hide message after 5 seconds
      setTimeout(() => {
        formMessages.style.display = "none";
      }, 5000);
    });
  }

  // Smooth scrolling for anchor links
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  anchorLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });

  // Lazy loading fallback for older browsers
  if ("IntersectionObserver" in window) {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.classList.add("loaded");
          observer.unobserve(img);
        }
      });
    });

    lazyImages.forEach((img) => imageObserver.observe(img));
  }
});

// Performance optimization - minimize reflows
window.addEventListener("load", function () {
  // Add loaded class to body for CSS animations
  document.body.classList.add("loaded");
});
