<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="section-padding bg-primary text-white text-center position-relative" style="background: linear-gradient(rgba(10, 37, 64, 0.8), rgba(10, 37, 64, 0.8)), url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="container py-5">
        <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">Get In Touch</h1>
        <p class="lead mb-0 opacity-75" data-aos="fade-up" data-aos-delay="100">Have a question or ready to start your journey? We're here to help.</p>
    </div>
</section>

<!-- Contact Content -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-5">
            <!-- Left: Contact Info -->
            <div class="col-lg-4" data-aos="fade-right">
                <div class="bg-white p-5 rounded-4 shadow-sm h-100">
                    <h4 class="fw-bold mb-4">Contact Information</h4>
                    <p class="text-muted mb-5">Reach out to us through any of these channels. Our team is available 24/7 for urgent inquiries.</p>
                    
                    <div class="d-flex mb-4">
                        <div class="bg-accent-subtle text-accent rounded-3 p-3 me-3 h-100">
                            <i class="fas fa-map-marker-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Our Location</h6>
                            <p class="text-muted small mb-0">123 Luxury Avenue, Real Estate Plaza, NY 10001</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="bg-accent-subtle text-accent rounded-3 p-3 me-3 h-100">
                            <i class="fas fa-phone-alt fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Phone Number</h6>
                            <p class="text-muted small mb-0">+1 (234) 567-8900</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-5">
                        <div class="bg-accent-subtle text-accent rounded-3 p-3 me-3 h-100">
                            <i class="fas fa-envelope fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Address</h6>
                            <p class="text-muted small mb-0">info@eliteestates.com</p>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mb-3">Connect With Us</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-light rounded-circle p-2 text-primary" style="width: 40px; height: 40px;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-light rounded-circle p-2 text-info" style="width: 40px; height: 40px;"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-light rounded-circle p-2 text-danger" style="width: 40px; height: 40px;"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-light rounded-circle p-2 text-primary" style="width: 40px; height: 40px;"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            <!-- Right: Contact Form -->
            <div class="col-lg-8" data-aos="fade-left">
                <div class="bg-white p-5 rounded-4 shadow-sm">
                    <h4 class="fw-bold mb-4">Send a Message</h4>
                    
                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3 fa-lg"></i>
                            <div><strong>Success!</strong> Your message has been sent. We'll get back to you within 24 hours.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <form action="process-inquiry.php" method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase opacity-75">Full Name</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-3" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase opacity-75">Email Address</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-3" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase opacity-75">Phone Number (Optional)</label>
                                <input type="tel" name="phone" class="form-control bg-light border-0 py-3" placeholder="+1 (234) 567-8900">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-uppercase opacity-75">Your Message</label>
                                <textarea name="message" class="form-control bg-light border-0 py-3" rows="5" placeholder="Tell us what you're looking for..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-accent px-5 py-3 fw-bold rounded-pill w-100 w-md-auto">Send Message Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="mt-5" data-aos="zoom-in">
            <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                <div class="ratio ratio-21x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.4221969427433!2d-73.985428!3d40.748817!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a147!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1625000000000!5m2!1sen!2sus" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
