/**
 * HID HOV Theme Scripts
 * Combined and adapted from HTML templates
 * 
 * @package HID_HOV_Theme
 */

(function($) {
  "use strict";

  // Wait for DOM to be ready
  $(document).ready(function() {
    initMobileMenu();
    initSmoothScroll();
    initBackToTop();
    initHeaderScroll();
    initBestSellersCarousel();
    initTestimonialRotation();
    initNewsletterForm();
    initFaqAccordion();
    initFaqSearch();
    handleUrlHash();
  });

  // --- 1. Mobile Menu Toggle ---
  function initMobileMenu() {
    var $hamburger = $(".house-of-veloria-container .hamburger-menu");
    var $header = $(".house-of-veloria-container .header");
    var $navLinks = $(".house-of-veloria-container .nav-link");

    if (!$hamburger.length || !$header.length) return;

    $hamburger.on("click", function() {
      $header.toggleClass("nav-active");
    });

    // Close mobile menu when a link is clicked
    $navLinks.on("click", function() {
      if ($header.hasClass("nav-active")) {
        $header.removeClass("nav-active");
      }
    });

    // Close menu when clicking outside
    $(document).on("click", function(e) {
      if ($header.hasClass("nav-active") && 
          !$(e.target).closest(".nav-menu").length && 
          !$(e.target).closest(".hamburger-menu").length) {
        $header.removeClass("nav-active");
      }
    });
  }

  // --- 2. Smooth Scrolling Navigation ---
  function initSmoothScroll() {
    var $links = $('.house-of-veloria-container a[href^="#"]');
    var headerHeight = $(".house-of-veloria-container .header").outerHeight() || 80;
    
    // Account for WordPress admin bar
    var adminBarHeight = 0;
    if ($('body').hasClass('admin-bar')) {
      // Admin bar is 32px on desktop, 46px on mobile
      adminBarHeight = ($(window).width() >= 783) ? 32 : 46;
    }

    $links.on("click", function(e) {
      var href = $(this).attr("href");
      if (href === "#" || href === "") return;

      var $target = $(href);
      if ($target.length) {
        e.preventDefault();
        var targetPosition = $target.offset().top - headerHeight - adminBarHeight;
        
        $("html, body").animate({
          scrollTop: targetPosition
        }, 600);
      }
    });
  }

  // --- 3. Back to Top Button ---
  function initBackToTop() {
    var $backToTopBtn = $(".house-of-veloria-container .back-to-top");
    
    if (!$backToTopBtn.length) return;

    // Show/hide button based on scroll position
    $(window).on("scroll", function() {
      if ($(window).scrollTop() > 500) {
        $backToTopBtn.addClass("visible");
      } else {
        $backToTopBtn.removeClass("visible");
      }
    });

    // Scroll to top on click
    $backToTopBtn.on("click", function() {
      $("html, body").animate({
        scrollTop: 0
      }, 600);
    });
  }

  // --- 4. Header Scroll Effect ---
  function initHeaderScroll() {
    var $header = $(".house-of-veloria-container .header");
    
    if (!$header.length) return;

    $(window).on("scroll", function() {
      var currentScroll = $(window).scrollTop();

      // Add solid background when scrolled
      if (currentScroll > 100) {
        $header.css("background-color", "rgba(71, 1, 8, 0.95)");
      } else {
        $header.css("background-color", "rgba(58, 58, 59, 0.8)");
      }
    });
  }

  // --- 5. Best Sellers Carousel ---
  function initBestSellersCarousel() {
    var $carousel = $("#bestSellersCarousel");
    var $prevBtn = $("#carouselPrev");
    var $nextBtn = $("#carouselNext");

    if (!$carousel.length || !$prevBtn.length || !$nextBtn.length) return;

    var $slides = $carousel.find(".carousel-slide");
    var slideWidth = 280 + 24; // slide width + gap
    var currentPosition = 0;
    var maxPosition = 0;
    var autoPlayInterval;

    function calculateMaxPosition() {
      var containerWidth = $carousel.parent().outerWidth();
      var totalWidth = $slides.length * slideWidth;
      maxPosition = Math.max(0, totalWidth - containerWidth);
    }

    function updateCarousel() {
      $carousel.css("transform", "translateX(-" + currentPosition + "px)");
    }

    function moveNext() {
      calculateMaxPosition();
      currentPosition = Math.min(currentPosition + slideWidth, maxPosition);
      updateCarousel();
    }

    function movePrev() {
      currentPosition = Math.max(currentPosition - slideWidth, 0);
      updateCarousel();
    }

    $nextBtn.on("click", moveNext);
    $prevBtn.on("click", movePrev);

    // Recalculate on resize
    $(window).on("resize", function() {
      calculateMaxPosition();
      currentPosition = Math.min(currentPosition, maxPosition);
      updateCarousel();
    });

    // Touch support for mobile
    var touchStartX = 0;
    var touchEndX = 0;

    $carousel[0].addEventListener("touchstart", function(e) {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    $carousel[0].addEventListener("touchend", function(e) {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, { passive: true });

    function handleSwipe() {
      var swipeThreshold = 50;
      if (touchStartX - touchEndX > swipeThreshold) {
        moveNext();
      } else if (touchEndX - touchStartX > swipeThreshold) {
        movePrev();
      }
    }

    // Auto-play
    function startAutoPlay() {
      autoPlayInterval = setInterval(function() {
        calculateMaxPosition();
        if (currentPosition >= maxPosition) {
          currentPosition = 0;
        } else {
          currentPosition += slideWidth;
        }
        updateCarousel();
      }, 5000);
    }

    function stopAutoPlay() {
      clearInterval(autoPlayInterval);
    }

    // Start auto-play
    startAutoPlay();

    // Pause on hover
    $carousel.parent().on("mouseenter", stopAutoPlay);
    $carousel.parent().on("mouseleave", startAutoPlay);
  }

  // --- 6. Testimonial Rotation ---
  function initTestimonialRotation() {
    var $testimonials = $(".house-of-veloria-container .testimonial-card");
    
    if ($testimonials.length <= 1) return;

    var currentIndex = 0;

    function showNextTestimonial() {
      $testimonials.eq(currentIndex).removeClass("active");
      currentIndex = (currentIndex + 1) % $testimonials.length;
      $testimonials.eq(currentIndex).addClass("active");
    }

    // Rotate every 6 seconds
    setInterval(showNextTestimonial, 6000);
  }

  // --- 7. Newsletter Form (Two-Step with Modal) ---
  function initNewsletterForm() {
    var $form = $("#newsletter-form");
    var $modal = $("#newsletter-captcha-modal");
    var $captchaForm = $("#newsletter-captcha-form");
    var $modalClose = $modal.find(".hid-modal-close");
    
    if (!$form.length) return;

    // Step 1: Email submission - show captcha modal
    $form.on("submit", function(e) {
      e.preventDefault();

      var $emailInput = $form.find('input[name="email"]');
      var $feedback = $form.closest('.section-container').find(".form-feedback");
      var email = $emailInput.val();
      var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      // Validate email
      if (!email || !emailPattern.test(email)) {
        if ($feedback.length) {
          $feedback.text("Please enter a valid email address.").removeClass("success").addClass("error");
        }
        return;
      }

      // Clear previous feedback
      $feedback.text("").removeClass("success error");

      // Store email in hidden field and show modal
      $("#newsletter-email-hidden").val(email);
      $modal.fadeIn();
      
      // Focus on captcha input
      setTimeout(function() {
        $("#captcha-answer").focus();
      }, 300);
    });

    // Step 2: Captcha verification and final submission
    $captchaForm.on("submit", function(e) {
      e.preventDefault();

      var email = $("#newsletter-email-hidden").val();
      var $captchaInput = $captchaForm.find('input[name="captcha"]');
      var $captchaSession = $captchaForm.find('input[name="captcha_session"]');
      var $button = $captchaForm.find('button[type="submit"]');
      var $captchaFeedback = $captchaForm.closest('.hid-modal-content').find(".captcha-feedback");
      var $mainFeedback = $form.closest('.section-container').find(".form-feedback");
      var captcha = $captchaInput.val();
      var captchaSession = $captchaSession.val();

      // Validate captcha
      if (!captcha) {
        if ($captchaFeedback.length) {
          $captchaFeedback.text("Please answer the math question.").removeClass("success").addClass("error");
        }
        return;
      }

      // Show loading state
      var originalButtonText = $button.text();
      $button.text("Verifying...").prop("disabled", true);

      // Send AJAX request
      $.ajax({
        url: hidTheme.ajaxUrl,
        type: 'POST',
        data: {
          action: 'hid_newsletter_subscribe',
          nonce: hidTheme.nonce,
          email: email,
          captcha: parseInt(captcha),
          captcha_session: parseInt(captchaSession)
        },
        success: function(response) {
          if (response.success) {
            // Show success message in main form
            if ($mainFeedback.length) {
              $mainFeedback.text(response.data.message).removeClass("error").addClass("success");
            }
            
            // Reset forms
            $form[0].reset();
            $captchaForm[0].reset();
            
            // Close modal
            $modal.fadeOut();
            
            // Clear feedback after 5 seconds
            setTimeout(function() {
              if ($mainFeedback.length) {
                $mainFeedback.text("").removeClass("success error");
              }
            }, 5000);
          } else {
            // Show error in modal
            if ($captchaFeedback.length) {
              $captchaFeedback.text(response.data.message).removeClass("success").addClass("error");
            }
          }
          $button.text(originalButtonText).prop("disabled", false);
        },
        error: function() {
          if ($captchaFeedback.length) {
            $captchaFeedback.text("An error occurred. Please try again.").removeClass("success").addClass("error");
          }
          $button.text(originalButtonText).prop("disabled", false);
        }
      });
    });

    // Close modal handlers
    $modalClose.on("click", function() {
      $modal.fadeOut();
      $captchaForm[0].reset();
      $captchaForm.find(".captcha-feedback").text("").removeClass("success error");
    });

    // Close modal when clicking outside
    $modal.on("click", function(e) {
      if ($(e.target).is($modal)) {
        $modal.fadeOut();
        $captchaForm[0].reset();
        $captchaForm.find(".captcha-feedback").text("").removeClass("success error");
      }
    });
  }

  // --- 8. FAQ Accordion ---
  function initFaqAccordion() {
    var $faqItems = $(".house-of-veloria-container .faq-item");

    if (!$faqItems.length) return;

    $faqItems.each(function() {
      var $item = $(this);
      var $question = $item.find(".faq-question");
      
      $question.on("click", function() {
        var isActive = $item.hasClass("active");
        
        // Close all other items
        $faqItems.removeClass("active");

        // Toggle current item
        if (!isActive) {
          $item.addClass("active");
        }
      });
    });
  }

  // --- 9. FAQ Search ---
  function initFaqSearch() {
    var $searchInput = $("#faq-search");
    var $faqItems = $(".house-of-veloria-container .faq-item");

    if (!$searchInput.length || !$faqItems.length) return;

    $searchInput.on("input", function() {
      var searchTerm = $(this).val().toLowerCase().trim();

      $faqItems.each(function() {
        var $item = $(this);
        var question = $item.find(".faq-question").text().toLowerCase();
        var answer = $item.find(".faq-answer").text().toLowerCase();

        if (question.indexOf(searchTerm) !== -1 || answer.indexOf(searchTerm) !== -1 || searchTerm === "") {
          $item.show();
        } else {
          $item.hide();
        }
      });
    });
  }

  // --- 10. Handle URL Hash ---
  function handleUrlHash() {
    var hash = window.location.hash.substring(1);
    
    if (hash) {
      var $target = $("#" + hash);
      
      if ($target.length) {
        // Scroll to section after a short delay
        setTimeout(function() {
          var headerHeight = $(".house-of-veloria-container .header").outerHeight() || 80;
          
          // Account for WordPress admin bar
          var adminBarHeight = 0;
          if ($('body').hasClass('admin-bar')) {
            adminBarHeight = ($(window).width() >= 783) ? 32 : 46;
          }
          
          var targetPosition = $target.offset().top - headerHeight - adminBarHeight - 20;
          $("html, body").animate({ scrollTop: targetPosition }, 600);
        }, 100);
      }
    }
  }

  // Listen for hash changes
  $(window).on("hashchange", handleUrlHash);

  // --- Utility Functions ---
  
  // Debounce function for scroll events
  window.veloriaDebounce = function(func, wait) {
    var timeout;
    return function() {
      var context = this;
      var args = arguments;
      var later = function() {
        clearTimeout(timeout);
        func.apply(context, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  };

})(jQuery);

