<?php
include 'includes/header.php';

// Fetch Featured Properties (Random 3)
$featured_query = "SELECT * FROM properties ORDER BY RAND() LIMIT 3";
$featured_result = $conn->query($featured_query);

// Fetch Latest Properties (6)
$latest_query = "SELECT * FROM properties ORDER BY created_at DESC LIMIT 6";
$latest_result = $conn->query($latest_query);

// Fetch Agents (4)
$agents_query = "SELECT * FROM agents LIMIT 4";
$agents_result = $conn->query($agents_query);

// Stats
$total_properties = $conn->query("SELECT COUNT(*) as count FROM properties")->fetch_assoc()['count'];
$total_agents = $conn->query("SELECT COUNT(*) as count FROM agents")->fetch_assoc()['count'];
$total_inquiries = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content" data-aos="fade-right">
            <h1 class="hero-title">Find Your <span class="text-accent">Dream Property</span> With Elite Estates</h1>
            <p class="hero-subtitle">Discover the finest selection of luxury homes, modern apartments, and exclusive villas in the world's most prestigious locations.</p>
            <div class="hero-btns">
                <a href="properties.php" class="btn btn-accent me-3">Browse Properties</a>
                <a href="contact.php" class="btn btn-outline-light px-4 py-3 rounded-3 fw-bold">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Search Bar -->
<div class="container">
    <div class="search-container" data-aos="fade-up" data-aos-delay="200">
        <form action="properties.php" method="GET" class="row g-3">
            <div class="col-lg-4 col-md-6">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Enter keywords (e.g. Modern Villa)">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="search-input-group">
                    <i class="fas fa-home"></i>
                    <select name="type">
                        <option value="">Property Type</option>
                        <option value="Apartment">Apartment</option>
                        <option value="House">House</option>
                        <option value="Villa">Villa</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="search-input-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <select name="location">
                        <option value="">Location</option>
                        <option value="New York">New York</option>
                        <option value="London">London</option>
                        <option value="Los Angeles">Los Angeles</option>
                        <option value="Miami">Miami</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="search-input-group">
                    <i class="fas fa-tag"></i>
                    <select name="price_range">
                        <option value="">Price Range</option>
                        <option value="0-500000">$0 - $500k</option>
                        <option value="500000-1000000">$500k - $1M</option>
                        <option value="1000000+">$1M+</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-12">
                <button type="submit" class="btn btn-accent w-100 h-100">Search Now</button>
            </div>
        </form>
    </div>
</div>

<!-- Featured Properties -->
<section class="section-padding">
    <div class="container">
        <div class="section-title text-center" data-aos="fade-up">
            <h6 class="text-accent fw-bold text-uppercase">Exclusive Deals</h6>
            <h2>Featured Properties</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Handpicked luxury properties that offer the best value and premium living experience.</p>
        </div>
        
        <div class="row g-4">
            <?php while($property = $featured_result->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="property-card">
                    <div class="property-image">
                        <span class="property-badge"><?php echo $property['status']; ?></span>
                        <span class="property-type"><?php echo $property['type']; ?></span>
                        <?php 
                        $prop_img_file = ($property['main_image'] ?? '');
                        $image_path = $prop_img_file;
                        if (!filter_var($prop_img_file, FILTER_VALIDATE_URL)) {
                            $image_path_rel = 'assets/images/properties/' . $prop_img_file;
                            $image_path = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
                            if (!empty($prop_img_file) && file_exists(__DIR__ . '/' . $image_path_rel) && filesize(__DIR__ . '/' . $image_path_rel) > 0) {
                                $image_path = $image_path_rel;
                            }
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>" alt="<?php echo $property['title']; ?>">
                    </div>
                    <div class="property-content">
                        <div class="property-price">$<?php echo number_format($property['price']); ?></div>
                        <h4 class="property-title"><?php echo $property['title']; ?></h4>
                        <div class="property-location"><i class="fas fa-map-marker-alt me-2 text-accent"></i><?php echo $property['location']; ?></div>
                        <div class="property-features">
                            <span class="feature-item"><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                            <span class="feature-item"><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                            <span class="feature-item"><i class="fas fa-vector-square"></i> <?php echo $property['area_sqft']; ?> Sqft</span>
                        </div>
                        <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="btn btn-accent w-100 mt-4">View Details</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="section-title">
                    <h6 class="text-accent fw-bold text-uppercase">Our Value</h6>
                    <h2>Why Choose Elite Estates?</h2>
                </div>
                <p class="mb-5">We provide a seamless experience for buyers and sellers alike. Our expertise and dedication ensure you find the perfect property at the best price.</p>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="feature-box">
                            <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                            <h5>Trusted Agency</h5>
                            <p class="small text-muted mb-0">Years of experience and thousands of satisfied clients.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-box">
                            <div class="feature-icon"><i class="fas fa-hand-holding-usd"></i></div>
                            <h5>Best Prices</h5>
                            <p class="small text-muted mb-0">Exclusive access to off-market deals and competitive pricing.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-box">
                            <div class="feature-icon"><i class="fas fa-headset"></i></div>
                            <h5>24/7 Support</h5>
                            <p class="small text-muted mb-0">Our dedicated team is always ready to assist you.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-box">
                            <div class="feature-icon"><i class="fas fa-key"></i></div>
                            <h5>Easy Process</h5>
                            <p class="small text-muted mb-0">We handle all the paperwork and legal formalities.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                <div class="ps-lg-5">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="About Us" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6" data-aos="zoom-in">
                <div class="stat-item">
                    <h3 class="counter"><?php echo $total_properties; ?></h3>
                    <p class="mb-0 text-white-50">Properties Listed</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-item">
                    <h3 class="counter"><?php echo $total_agents; ?></h3>
                    <p class="mb-0 text-white-50">Expert Agents</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-item">
                    <h3 class="counter"><?php echo $total_inquiries; ?></h3>
                    <p class="mb-0 text-white-50">Happy Clients</p>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="300">
                <div class="stat-item">
                    <h3 class="counter">15+</h3>
                    <p class="mb-0 text-white-50">Years Experience</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Properties -->
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end section-title" data-aos="fade-up">
            <div>
                <h6 class="text-accent fw-bold text-uppercase">New Arrivals</h6>
                <h2>Latest Properties</h2>
            </div>
            <a href="properties.php" class="btn btn-outline-dark mb-3">View All Properties</a>
        </div>
        
        <div class="row g-4">
            <?php while($property = $latest_result->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="property-card">
                    <div class="property-image">
                        <span class="property-badge"><?php echo $property['status']; ?></span>
                        <span class="property-type"><?php echo $property['type']; ?></span>
                        <?php 
                        $prop_img_file = ($property['main_image'] ?? '');
                        $image_path = $prop_img_file;
                        if (!filter_var($prop_img_file, FILTER_VALIDATE_URL)) {
                            $image_path_rel = 'assets/images/properties/' . $prop_img_file;
                            $image_path = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
                            if (!empty($prop_img_file) && file_exists(__DIR__ . '/' . $image_path_rel) && filesize(__DIR__ . '/' . $image_path_rel) > 0) {
                                $image_path = $image_path_rel;
                            }
                        }
                        ?>
                        <img src="<?php echo $image_path; ?>" alt="<?php echo $property['title']; ?>">
                    </div>
                    <div class="property-content">
                        <div class="property-price">$<?php echo number_format($property['price']); ?></div>
                        <h4 class="property-title"><?php echo $property['title']; ?></h4>
                        <div class="property-location"><i class="fas fa-map-marker-alt me-2 text-accent"></i><?php echo $property['location']; ?></div>
                        <div class="property-features">
                            <span class="feature-item"><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                            <span class="feature-item"><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                            <span class="feature-item"><i class="fas fa-vector-square"></i> <?php echo $property['area_sqft']; ?> Sqft</span>
                        </div>
                        <a href="property-detail.php?id=<?php echo $property['id']; ?>" class="btn btn-accent w-100 mt-4">View Details</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Agents Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="section-title text-center" data-aos="fade-up">
            <h6 class="text-accent fw-bold text-uppercase">Expert Team</h6>
            <h2>Meet Our Agents</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Our professionals are here to guide you through every step of your real estate journey.</p>
        </div>
        
        <div class="row g-4">
            <?php while($agent = $agents_result->fetch_assoc()): ?>
            <div class="col-lg-3 col-md-6" data-aos="fade-up">
                <div class="agent-card h-100 bg-white shadow-sm border-0 p-4 rounded-4 transition-all text-center">
                    <div class="position-relative d-inline-block mb-4">
                        <?php 
                        $agent_img_file = ($agent['profile_image'] ?? '');
                        $agent_image_path = 'assets/images/agents/' . $agent_img_file;
                        $agent_image = 'https://i.pravatar.cc/300?u=' . urlencode($agent['name']);
                        if (!empty($agent_img_file) && file_exists(__DIR__ . '/' . $agent_image_path) && filesize(__DIR__ . '/' . $agent_image_path) > 0) {
                            $agent_image = $agent_image_path;
                        }
                        ?>
                        <img src="<?php echo $agent_image; ?>" alt="<?php echo htmlspecialchars($agent['name']); ?>" class="rounded-circle shadow-sm border border-4 border-light" style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($agent['name']); ?></h5>
                    <p class="text-accent small fw-bold text-uppercase mb-3">Real Estate Agent</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <a href="tel:<?php echo $agent['phone']; ?>" class="btn btn-sm btn-outline-secondary rounded-circle" title="Call"><i class="fas fa-phone-alt"></i></a>
                        <a href="mailto:<?php echo $agent['email']; ?>?subject=Property Inquiry" class="btn btn-sm btn-outline-secondary rounded-circle" title="Email"><i class="fas fa-envelope"></i></a>
                        <a href="agent.php?id=<?php echo $agent['id']; ?>" class="btn btn-sm btn-outline-secondary rounded-circle" title="Portfolio"><i class="fas fa-briefcase"></i></a>
                    </div>
                    
                    <div class="d-grid">
                        <button type="button" class="btn btn-accent text-white btn-sm rounded-pill py-2 fw-bold contact-agent-btn" 
                                data-bs-toggle="modal" data-bs-target="#contactAgentModal" 
                                data-agent-id="<?php echo $agent['id']; ?>" 
                                data-agent-name="<?php echo htmlspecialchars($agent['name']); ?>">
                            <i class="fas fa-comment-alt me-2"></i>Contact Agent
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section-padding">
    <div class="container">
        <div class="section-title text-center" data-aos="fade-up">
            <h6 class="text-accent fw-bold text-uppercase">Testimonials</h6>
            <h2>What Our Clients Say</h2>
        </div>
        
        <div class="swiper testimonial-slider" data-aos="fade-up">
            <div class="swiper-wrapper">
                <div class="swiper-slide p-4">
                    <div class="card border-0 bg-light p-4 rounded-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://i.pravatar.cc/100?u=1" alt="Client" class="rounded-circle me-3" width="60">
                            <div>
                                <h6 class="mb-0">Sarah Johnson</h6>
                                <p class="small text-muted mb-0">Home Buyer</p>
                            </div>
                        </div>
                        <p class="fst-italic">"Elite Estates helped me find my dream home in London. The process was smooth and the team was incredibly professional."</p>
                        <div class="text-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide p-4">
                    <div class="card border-0 bg-light p-4 rounded-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://i.pravatar.cc/100?u=2" alt="Client" class="rounded-circle me-3" width="60">
                            <div>
                                <h6 class="mb-0">David Miller</h6>
                                <p class="small text-muted mb-0">Property Investor</p>
                            </div>
                        </div>
                        <p class="fst-italic">"As an investor, I value market insights and honesty. Elite Estates provided both, helping me secure a great commercial deal."</p>
                        <div class="text-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide p-4">
                    <div class="card border-0 bg-light p-4 rounded-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://i.pravatar.cc/100?u=3" alt="Client" class="rounded-circle me-3" width="60">
                            <div>
                                <h6 class="mb-0">Jessica Lee</h6>
                                <p class="small text-muted mb-0">Villa Owner</p>
                            </div>
                        </div>
                        <p class="fst-italic">"The best real estate experience I've ever had. Their attention to detail and customer service is unmatched."</p>
                        <div class="text-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="section-title">
                    <h6 class="text-accent fw-bold text-uppercase">Contact Us</h6>
                    <h2>Get in Touch With Our Experts</h2>
                </div>
                <p class="mb-5 text-muted">Whether you're looking to buy, sell, or rent, our team is here to provide you with the best advice and service.</p>
                
                <div class="d-flex mb-4">
                    <div class="bg-white text-accent rounded-circle p-3 shadow-sm me-3 h-100">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Call Us 24/7</h6>
                        <p class="text-muted small">+1 (234) 567-8900</p>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="bg-white text-accent rounded-circle p-3 shadow-sm me-3 h-100">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Email Support</h6>
                        <p class="text-muted small">info@eliteestates.com</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="bg-white text-accent rounded-circle p-3 shadow-sm me-3 h-100">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Our Office</h6>
                        <p class="text-muted small">123 Luxury Avenue, Real Estate Plaza, NY 10001</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="bg-white p-5 rounded-4 shadow-sm">
                    <form action="process-inquiry.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control bg-light border-0 py-3" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control bg-light border-0 py-3" placeholder="Email Address" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control bg-light border-0 py-3" placeholder="Subject">
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control bg-light border-0 py-3" rows="5" placeholder="Your Message" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-accent w-100 py-3 fw-bold rounded-pill">Send Message Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
