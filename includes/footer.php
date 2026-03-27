<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <a class="navbar-brand mb-4 d-inline-block" href="index.php">ELITE <span>ESTATES</span></a>
                <p class="text-white-50">Providing premium real estate services with a focus on quality, transparency, and client satisfaction. Find your dream home with Elite Estates.</p>
                <div class="social-links mt-4">
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm me-2"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h5 class="mb-4 fw-bold">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="footer-link">Home</a></li>
                    <li class="mb-2"><a href="properties.php" class="footer-link">All Properties</a></li>
                    <li class="mb-2"><a href="agents.php" class="footer-link">Our Agents</a></li>
                    <li class="mb-2"><a href="contact.php" class="footer-link">Contact Us</a></li>
                    <li class="mb-2"><a href="favorites.php" class="footer-link">Favorites</a></li>
                </ul>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h5 class="mb-4 fw-bold">Property Types</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="properties.php?type=House" class="footer-link">Houses</a></li>
                    <li class="mb-2"><a href="properties.php?type=Apartment" class="footer-link">Apartments</a></li>
                    <li class="mb-2"><a href="properties.php?type=Villa" class="footer-link">Villas</a></li>
                    <li class="mb-2"><a href="properties.php?type=Commercial" class="footer-link">Commercial</a></li>
                    <li class="mb-2"><a href="properties.php?type=Land" class="footer-link">Land</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <h5 class="mb-4 fw-bold">Contact Info</h5>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-map-marker-alt me-3 text-accent"></i>
                        123 Luxury Avenue, Real Estate Plaza, NY 10001
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-phone-alt me-3 text-accent"></i>
                        +1 (234) 567-8900
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-envelope me-3 text-accent"></i>
                        info@eliteestates.com
                    </li>
                </ul>
            </div>
        </div>
        <hr class="mt-5 border-white-50">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-white-50">&copy; <?php echo date('Y'); ?> Elite Estates. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-white-50">Designed with <i class="fas fa-heart text-danger"></i> for real estate professionals.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Contact Agent Modal -->
<div class="modal fade" id="contactAgentModal" tabindex="-1" aria-labelledby="contactAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-4 px-4">
                <h5 class="modal-title fw-bold" id="contactAgentModalLabel">Contact Agent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="contactFormAlert" class="alert d-none" role="alert"></div>
                <form id="contactAgentForm">
                    <input type="hidden" id="modal_agent_id" name="agent_id">
                    <div class="mb-3">
                        <label for="modal_sender_name" class="form-label small fw-bold text-muted">Your Name</label>
                        <input type="text" class="form-control bg-light border-0 py-3" id="modal_sender_name" name="sender_name" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_sender_email" class="form-label small fw-bold text-muted">Email Address</label>
                        <input type="email" class="form-control bg-light border-0 py-3" id="modal_sender_email" name="sender_email" placeholder="Enter your email address" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_message" class="form-label small fw-bold text-muted">Your Message</label>
                        <textarea class="form-control bg-light border-0 py-3" id="modal_message" name="message" rows="4" placeholder="How can we help you?" required></textarea>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" id="submitContactForm" class="btn btn-accent text-white py-3 fw-bold rounded-pill shadow-sm">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- AOS Animation -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<!-- Swiper.js -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>
<script>
    $(document).ready(function() {
        // Handle Contact Agent Modal Data
        $('.contact-agent-btn').on('click', function() {
            const agentId = $(this).data('agent-id');
            const agentName = $(this).data('agent-name');
            
            $('#modal_agent_id').val(agentId);
            $('#contactAgentModalLabel').text('Contact ' + agentName);
        });

        // Handle Form Submission via AJAX
        $('#contactAgentForm').on('submit', function(e) {
            e.preventDefault();
            
            const $submitBtn = $('#submitContactForm');
            const $spinner = $submitBtn.find('.spinner-border');
            const $alert = $('#contactFormAlert');
            
            $submitBtn.prop('disabled', true);
            $spinner.removeClass('d-none');
            $alert.addClass('d-none').removeClass('alert-success alert-danger');

            $.ajax({
                url: 'ajax/send-agent-message.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $alert.text(response.message).addClass('alert-success').removeClass('d-none');
                        $('#contactAgentForm')[0].reset();
                        setTimeout(function() {
                            $('#contactAgentModal').modal('hide');
                            $alert.addClass('d-none');
                        }, 2000);
                    } else {
                        $alert.text(response.message).addClass('alert-danger').removeClass('d-none');
                    }
                },
                error: function() {
                    $alert.text('An unexpected error occurred. Please try again later.').addClass('alert-danger').removeClass('d-none');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                    $spinner.addClass('d-none');
                }
            });
        });
    });

    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
</script>
</body>
</html>
