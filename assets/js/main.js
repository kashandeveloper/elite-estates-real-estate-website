$(document).ready(function() {
    // Navbar scroll effect
    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('.navbar').addClass('scrolled shadow-sm');
        } else {
            $('.navbar').removeClass('scrolled shadow-sm');
        }
    });

    // Testimonial Swiper
    if ($('.testimonial-slider').length) {
        new Swiper('.testimonial-slider', {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            loop: true
        });
    }

    // Property Image Swiper (for detail page)
    if ($('.property-detail-slider').length) {
        new Swiper('.property-detail-slider', {
            slidesPerView: 1,
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            loop: true
        });
    }

    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });

    // Counter Animation
    var counted = 0;
    $(window).scroll(function() {
        var oTop = $('.stats-section').length ? $('.stats-section').offset().top - window.innerHeight : null;
        if (oTop !== null && counted == 0 && $(window).scrollTop() > oTop) {
            $('.counter').each(function() {
                var $this = $(this),
                    countTo = $this.text();
                $({
                    countNum: 0
                }).animate({
                        countNum: countTo
                    },
                    {
                        duration: 2000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $this.text(this.countNum);
                        }
                    });
            });
            counted = 1;
        }
    });

    // Favorite Button AJAX
    $('.btn-favorite').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var propertyId = btn.data('id');
        
        $.ajax({
            url: 'ajax/favorite.php',
            method: 'POST',
            data: { property_id: propertyId },
            success: function(response) {
                if (response === 'added') {
                    btn.addClass('active');
                    btn.find('i').removeClass('far').addClass('fas');
                    // Optional: Add a toast notification here
                } else if (response === 'removed') {
                    btn.removeClass('active');
                    btn.find('i').removeClass('fas').addClass('far');
                    
                    // If we're on the favorites page, remove the card
                    if (window.location.pathname.includes('favorites.php')) {
                        btn.closest('.col-lg-4').fadeOut(300, function() {
                            $(this).remove();
                            if ($('.property-card').length === 0) {
                                location.reload(); // Show empty state
                            }
                        });
                    }
                }
            }
        });
    });
});
