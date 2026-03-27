<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="section-padding bg-primary text-white text-center position-relative" style="background: linear-gradient(rgba(10, 37, 64, 0.8), rgba(10, 37, 64, 0.8)), url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="container py-5">
        <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">Our Expert Agents</h1>
        <p class="lead mb-0 opacity-75" data-aos="fade-up" data-aos-delay="100">Meet the dedicated professionals committed to your real estate success.</p>
    </div>
</section>

<!-- Agents Listing -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <?php
            $sql = "SELECT a.*, (SELECT COUNT(*) FROM properties WHERE agent_id = a.id) as property_count FROM agents a";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <div class="agent-card h-100 bg-white shadow-sm border-0 p-4 rounded-4 transition-all">
                            <div class="position-relative d-inline-block mb-4">
                                <?php 
                                $agent_image_file = ($row['profile_image'] ?? '');
                                $agent_image = $agent_image_file;
                                if (!filter_var($agent_image_file, FILTER_VALIDATE_URL)) {
                                    $agent_image_path = 'assets/images/agents/' . $agent_image_file;
                                    $agent_image = 'https://i.pravatar.cc/300?u=' . urlencode($row['name']);
                                    if (!empty($agent_image_file) && file_exists($agent_image_path) && filesize($agent_image_path) > 0) {
                                        $agent_image = $agent_image_path;
                                    }
                                }
                                ?>
                                <img src="<?php echo $agent_image; ?>" class="rounded-circle shadow-sm border border-5 border-light" style="width: 160px; height: 160px; object-fit: cover;" alt="<?php echo $row['name']; ?>">
                                <div class="position-absolute bottom-0 end-0 bg-accent text-white rounded-circle p-2 shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check small"></i>
                                </div>
                            </div>
                            
                            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($row['name']); ?></h4>
                            <p class="text-accent fw-bold small text-uppercase mb-3">Certified Real Estate Specialist</p>
                            
                            <div class="contact-info mb-4 text-start bg-light p-3 rounded-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-phone-alt text-accent me-3"></i>
                                    <span class="small fw-semibold"><?php echo htmlspecialchars($row['phone']); ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-accent me-3"></i>
                                    <span class="small fw-semibold text-truncate"><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                            </div>
                            
                            <p class="text-muted small mb-4 px-2" style="line-height: 1.8;"><?php echo htmlspecialchars($row['bio']) ?: "A dedicated professional with extensive knowledge of the local market, helping clients navigate their property journey with confidence."; ?></p>
                            
                            <div class="row g-2 mb-4 text-center border-top border-bottom py-3 mx-1">
                                <div class="col-4">
                                    <h5 class="fw-bold mb-0 text-primary small"><?php echo $row['property_count']; ?></h5>
                                    <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Listings</p>
                                </div>
                                <div class="col-4 border-start border-end">
                                    <h5 class="fw-bold mb-0 text-primary small">12+</h5>
                                    <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Years Exp.</p>
                                </div>
                                <div class="col-4">
                                    <h5 class="fw-bold mb-0 text-primary small">200+</h5>
                                    <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Sales</p>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="agent.php?id=<?php echo $row['id']; ?>" class="btn btn-primary fw-bold rounded-pill py-2">View Portfolio</a>
                                <div class="d-flex gap-2 mt-2">
                                    <button type="button" class="btn btn-outline-light text-primary border-primary-subtle w-100 rounded-pill py-2 contact-agent-btn" 
                                            data-bs-toggle="modal" data-bs-target="#contactAgentModal" 
                                            data-agent-id="<?php echo $row['id']; ?>" 
                                            data-agent-name="<?php echo htmlspecialchars($row['name']); ?>">
                                        <i class="fas fa-comment-alt me-2"></i> Contact
                                    </button>
                                    <a href="mailto:<?php echo $row['email']; ?>?subject=Property Inquiry" class="btn btn-accent text-white w-100 rounded-pill py-2" title="Email Agent">
                                        <i class="fas fa-envelope me-2"></i> Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5" data-aos="fade-up">
                        <div class="bg-white p-5 rounded-4 shadow-sm">
                            <i class="fas fa-users-slash fa-4x text-muted mb-4"></i>
                            <h4 class="text-dark fw-bold">No Agents Available</h4>
                            <p class="text-muted">Our team is currently being updated. Please check back later.</p>
                            <a href="contact.php" class="btn btn-accent mt-3 px-4 py-2 rounded-pill">Contact Agency</a>
                        </div>
                      </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Why Work With Us Section -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="section-title">
                    <h6 class="text-accent fw-bold text-uppercase">The Best Team</h6>
                    <h2>Why Work With Our Professionals?</h2>
                </div>
                <div class="mt-4">
                    <div class="d-flex mb-4">
                        <div class="bg-primary-subtle rounded-circle p-3 me-3 h-100 text-primary">
                            <i class="fas fa-certificate fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Certified Expertise</h5>
                            <p class="text-muted small">All our agents are fully licensed and undergo continuous training to stay ahead of market trends.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="bg-primary-subtle rounded-circle p-3 me-3 h-100 text-primary">
                            <i class="fas fa-handshake fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Personalized Service</h5>
                            <p class="text-muted small">We believe in building relationships, not just closing deals. Your needs always come first.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="bg-primary-subtle rounded-circle p-3 me-3 h-100 text-primary">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold">Data-Driven Results</h5>
                            <p class="text-muted small">Our agents use advanced market analytics to ensure you get the best possible value for your investment.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                <div class="ps-lg-5 position-relative">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Team Meeting" class="img-fluid rounded-4 shadow-lg">
                    <div class="position-absolute bottom-0 start-0 bg-accent text-white p-4 rounded-4 m-n3 d-none d-md-block shadow-lg">
                        <h4 class="fw-bold mb-0">98%</h4>
                        <p class="mb-0 small">Client Satisfaction</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
