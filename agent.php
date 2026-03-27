<?php 
include 'includes/header.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: agents.php');
    exit;
}

$agent_id = $conn->real_escape_string($_GET['id']);

// Fetch agent details
$sql = "SELECT * FROM agents WHERE id = '$agent_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: agents.php');
    exit;
}

$agent = $result->fetch_assoc();

// Fetch properties listed by this agent
$props_sql = "SELECT * FROM properties WHERE agent_id = '$agent_id' ORDER BY created_at DESC";
$props_result = $conn->query($props_sql);
?>

<!-- Agent Profile Header -->
<?php 
$header_bg_file = ($agent['profile_image'] ?? '');
$header_bg = $header_bg_file;
if (!filter_var($header_bg_file, FILTER_VALIDATE_URL)) {
    $header_bg_path = 'assets/images/agents/' . $header_bg_file;
    $header_bg = 'https://images.unsplash.com/photo-1560523105-f55869b27f5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    if (!empty($header_bg_file) && file_exists(__DIR__ . '/' . $header_bg_path)) {
        $header_bg = $header_bg_path;
    }
}
?>
<section class="section-padding bg-primary text-white position-relative overflow-hidden" style="background: linear-gradient(rgba(10, 37, 64, 0.9), rgba(10, 37, 64, 0.9)), url('<?php echo $header_bg; ?>'); background-size: cover; background-position: center;">
    <div class="container py-5">
        <div class="row align-items-center text-center text-md-start">
            <div class="col-md-3 mb-4 mb-md-0" data-aos="zoom-in">
                <?php 
                $agent_img_file = ($agent['profile_image'] ?? '');
                $display_agent_image = $agent_img_file;
                if (!filter_var($agent_img_file, FILTER_VALIDATE_URL)) {
                    $agent_image_path = 'assets/images/agents/' . $agent_img_file;
                    // Professional avatar fallback
                    $display_agent_image = 'https://i.pravatar.cc/300?u=' . urlencode($agent['name'] ?? 'agent');
                    
                    if (!empty($agent_img_file) && file_exists(__DIR__ . '/' . $agent_image_path) && filesize(__DIR__ . '/' . $agent_image_path) > 0) {
                        $display_agent_image = $agent_image_path;
                    }
                }
                ?>
                <img src="<?php echo $display_agent_image; ?>" class="rounded-circle shadow-lg border border-5 border-white-50" style="width: 200px; height: 200px; object-fit: cover;" alt="<?php echo htmlspecialchars($agent['name']); ?>">
            </div>
            <div class="col-md-9" data-aos="fade-left">
                <h1 class="display-4 fw-bold mb-2"><?php echo $agent['name']; ?></h1>
                <p class="lead mb-4 opacity-75">Senior Property Consultant at Elite Estates</p>
                <div class="row g-4 mt-2">
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-accent-subtle text-accent rounded-circle p-3 me-3">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <div class="text-white-50 small fw-bold text-uppercase">Direct Line</div>
                                <div class="fw-bold"><?php echo $agent['phone']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-accent-subtle text-accent rounded-circle p-3 me-3">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <div class="text-white-50 small fw-bold text-uppercase">Email Agent</div>
                                <div class="fw-bold"><?php echo $agent['email']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="bg-accent-subtle text-accent rounded-circle p-3 me-3">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div>
                                <div class="text-white-50 small fw-bold text-uppercase">Experience</div>
                                <div class="fw-bold">12+ Years</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Agent Content -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row g-4">
            <!-- Left: About Agent -->
            <div class="col-lg-4">
                <div class="bg-white p-5 rounded-4 shadow-sm h-100" data-aos="fade-right">
                    <h4 class="fw-bold mb-4 border-bottom pb-3">Professional Bio</h4>
                    <p class="text-muted mb-4" style="line-height: 1.8;"><?php echo $agent['bio'] ?: "A results-oriented real estate professional with a passion for helping clients find their ideal properties. With over a decade of experience, I bring deep market knowledge and a commitment to excellence to every transaction."; ?></p>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Specializations:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-primary border-0 px-3 py-2 rounded-pill small fw-bold">Luxury Villas</span>
                            <span class="badge bg-light text-primary border-0 px-3 py-2 rounded-pill small fw-bold">Urban Living</span>
                            <span class="badge bg-light text-primary border-0 px-3 py-2 rounded-pill small fw-bold">Investments</span>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h6 class="fw-bold mb-3">Follow My Journey:</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-light rounded-circle p-2 text-primary" style="width: 40px; height: 40px;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-light rounded-circle p-2 text-info" style="width: 40px; height: 40px;"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-light rounded-circle p-2 text-primary" style="width: 40px; height: 40px;"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    
                    <div class="mt-5 bg-primary text-white p-4 rounded-4 text-center">
                        <h5 class="fw-bold mb-3">Ready to Consult?</h5>
                        <p class="small opacity-75 mb-4">Let's discuss your real estate goals today.</p>
                        <a href="contact.php" class="btn btn-accent w-100 py-3 rounded-pill fw-bold">Contact Agent</a>
                    </div>
                </div>
            </div>

            <!-- Right: Listed Properties -->
            <div class="col-lg-8">
                <div class="bg-white p-5 rounded-4 shadow-sm" data-aos="fade-left">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h4 class="fw-bold m-0">Listed Properties <span class="text-accent small ms-2">(<?php echo $props_result->num_rows; ?>)</span></h4>
                    </div>
                    
                    <div class="row g-4">
                        <?php if ($props_result->num_rows > 0): ?>
                            <?php while($prop = $props_result->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <div class="property-card h-100">
                                    <div class="property-image" style="height: 200px;">
                                        <span class="property-badge"><?php echo $prop['status']; ?></span>
                                        <span class="property-type"><?php echo $prop['type']; ?></span>
                                        <?php 
                                        $prop_img_file = ($prop['main_image'] ?? '');
                                        $image_path = $prop_img_file;
                                        if (!filter_var($prop_img_file, FILTER_VALIDATE_URL)) {
                                            $image_path_rel = 'assets/images/properties/' . $prop_img_file;
                                            $image_path = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                                            if (!empty($prop_img_file) && file_exists($image_path_rel) && filesize($image_path_rel) > 0) {
                                                $image_path = $image_path_rel;
                                            }
                                        }
                                        ?>
                                        <img src="<?php echo $image_path; ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo $prop['title']; ?>">
                                    </div>
                                    <div class="property-content">
                                        <div class="property-price">$<?php echo number_format($prop['price']); ?></div>
                                        <h6 class="property-title"><?php echo $prop['title']; ?></h6>
                                        <p class="property-location small"><i class="fas fa-map-marker-alt me-2 text-accent"></i><?php echo $prop['location']; ?></p>
                                        <div class="property-features">
                                            <span class="feature-item"><i class="fas fa-bed"></i> <?php echo $prop['bedrooms']; ?></span>
                                            <span class="feature-item"><i class="fas fa-bath"></i> <?php echo $prop['bathrooms']; ?></span>
                                            <span class="feature-item"><i class="fas fa-expand"></i> <?php echo $prop['area_sqft']; ?></span>
                                        </div>
                                        <a href="property-detail.php?id=<?php echo $prop['id']; ?>" class="btn btn-accent btn-sm w-100 mt-4">View Property</a>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-home fa-4x text-muted mb-4 opacity-25"></i>
                                <h4 class="text-muted">No Properties Listed</h4>
                                <p class="text-muted small">This agent hasn't listed any properties yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
