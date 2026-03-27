<?php 
include 'includes/header.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: properties.php');
    exit;
}

$property_id = $conn->real_escape_string($_GET['id']);

// Increment views
$conn->query("UPDATE properties SET views = views + 1 WHERE id = '$property_id'");

// Fetch property details
$sql = "SELECT p.*, a.name as agent_name, a.email as agent_email, a.phone as agent_phone, a.profile_image as agent_image, a.bio as agent_bio 
        FROM properties p 
        LEFT JOIN agents a ON p.agent_id = a.id 
        WHERE p.id = '$property_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: properties.php');
    exit;
}

$property = $result->fetch_assoc();

// Fetch additional images
$images_sql = "SELECT * FROM property_images WHERE property_id = '$property_id'";
$images_result = $conn->query($images_sql);
$additional_images = [];
while($img = $images_result->fetch_assoc()) {
    $additional_images[] = $img['image_path'];
}
?>

<!-- Page Header -->
<?php 
$header_bg_file = ($property['main_image'] ?? '');
$header_bg_path = 'assets/images/properties/' . $header_bg_file;
$header_bg = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
if (!empty($header_bg_file) && file_exists(__DIR__ . '/' . $header_bg_path)) {
    $header_bg = $header_bg_path;
}
?>
<section class="section-padding bg-primary text-white" style="background: linear-gradient(rgba(10, 37, 64, 0.9), rgba(10, 37, 64, 0.9)), url('<?php echo $header_bg; ?>'); background-size: cover; background-position: center;">
    <div class="container pt-5">
        <div class="row align-items-end">
            <div class="col-lg-8" data-aos="fade-right">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="properties.php" class="text-white-50 text-decoration-none">Properties</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $property['title']; ?></li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-3"><?php echo $property['title']; ?></h1>
                <p class="lead mb-0 opacity-75"><i class="fas fa-map-marker-alt me-2 text-accent"></i><?php echo $property['location']; ?></p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                <div class="bg-white text-dark d-inline-block p-4 rounded-4 shadow-lg">
                    <h6 class="text-muted text-uppercase fw-bold mb-1 small">Property Price</h6>
                    <h2 class="display-6 fw-bold text-primary mb-0">$<?php echo number_format($property['price']); ?></h2>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Property Image Gallery (directly below hero) -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden" data-aos="fade-up">
            <div class="swiper property-detail-slider" style="height: 100%; width: 100%; position: relative;">
                <div class="swiper-wrapper">
                    <!-- 1. Main Property Image -->
                    <div class="swiper-slide">
                        <?php 
                        $main_img = ($property['main_image'] ?? '');
                        $main_rel_path = 'assets/images/properties/' . $main_img;
                        $main_abs_path = __DIR__ . '/' . $main_rel_path;

                        // Default fallback if main image is missing or empty
                        $main_src = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';

                        if (!empty($main_img) && file_exists($main_abs_path) && filesize($main_abs_path) > 0) {
                            $main_src = $main_rel_path;
                        }
                        ?>
                        <img src="<?php echo $main_src; ?>" class="w-100 h-100" style="object-fit: cover; display: block;" alt="<?php echo htmlspecialchars($property['title'] ?? 'Property Main Image'); ?>">
                    </div>

                    <!-- 2. Additional Gallery Images -->
                    <?php if (!empty($additional_images) && is_array($additional_images)): ?>
                        <?php foreach($additional_images as $index => $img_file): ?>
                            <div class="swiper-slide">
                                <?php 
                                $gal_rel_path = 'assets/images/properties/' . $img_file;
                                $gal_abs_path = __DIR__ . '/' . $gal_rel_path;

                                // Variety fallback for additional images
                                $gal_src = 'https://images.unsplash.com/photo-1484154218962-a197022b5858?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';

                                if (!empty($img_file) && file_exists($gal_abs_path) && filesize($gal_abs_path) > 0) {
                                    $gal_src = $gal_rel_path;
                                }
                                ?>
                                <img src="<?php echo $gal_src; ?>" class="w-100 h-100" style="object-fit: cover; display: block;" alt="<?php echo htmlspecialchars($property['title'] ?? 'Property') . ' - Gallery ' . ($index + 1); ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Slider UI Controls -->
                <div class="swiper-button-next text-white shadow-sm" style="z-index: 10;"></div>
                <div class="swiper-button-prev text-white shadow-sm" style="z-index: 10;"></div>
                <div class="swiper-pagination" style="z-index: 10;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Property Details Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <!-- Left Content: Images and Description -->
            <div class="col-lg-8">
                <!-- Quick Stats -->
                <div class="row g-4 mb-5" data-aos="fade-up">
                    <div class="col-md-3 col-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm text-center h-100">
                            <i class="fas fa-bed fa-2x text-accent mb-3"></i>
                            <h5 class="fw-bold mb-0"><?php echo $property['bedrooms']; ?></h5>
                            <p class="text-muted small mb-0">Bedrooms</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm text-center h-100">
                            <i class="fas fa-bath fa-2x text-accent mb-3"></i>
                            <h5 class="fw-bold mb-0"><?php echo $property['bathrooms']; ?></h5>
                            <p class="text-muted small mb-0">Bathrooms</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm text-center h-100">
                            <i class="fas fa-expand fa-2x text-accent mb-3"></i>
                            <h5 class="fw-bold mb-0"><?php echo $property['area_sqft']; ?></h5>
                            <p class="text-muted small mb-0">Sq Ft</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm text-center h-100">
                            <i class="fas fa-building fa-2x text-accent mb-3"></i>
                            <h5 class="fw-bold mb-0"><?php echo $property['type']; ?></h5>
                            <p class="text-muted small mb-0">Property Type</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card border-0 shadow-sm mb-5 p-4 rounded-4" data-aos="fade-up">
                    <h4 class="fw-bold mb-4 border-bottom pb-3">About This Property</h4>
                    <p class="text-muted lead" style="white-space: pre-line; line-height: 1.8;"><?php echo $property['description']; ?></p>
                </div>

                <!-- Features List -->
                <div class="card border-0 shadow-sm mb-5 p-4 rounded-4" data-aos="fade-up">
                    <h4 class="fw-bold mb-4 border-bottom pb-3">Amenities</h4>
                    <div class="row g-3">
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-accent me-2"></i>
                                <span>Modern Kitchen</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-accent me-2"></i>
                                <span>Swimming Pool</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-accent me-2"></i>
                                <span>Private Garden</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-accent me-2"></i>
                                <span>24/7 Security</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-accent me-2"></i>
                                <span>Parking Space</span>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-accent me-2"></i>
                                <span>Fitness Center</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Agent -->
                <div class="card border-0 shadow-sm mb-5 p-4 rounded-4 text-center sticky-top" style="top: 100px;" data-aos="fade-left">
                    <h5 class="fw-bold mb-4 border-bottom pb-3">Interested in this property?</h5>
                    <div class="mb-4">
                        <?php 
                        $agent_img_file = ($property['agent_image'] ?? '');
                        $agent_image_path = 'assets/images/agents/' . $agent_img_file;
                        $agent_image = 'https://i.pravatar.cc/300?u=' . urlencode($property['agent_name'] ?? 'agent');
                        if (!empty($agent_img_file) && file_exists($agent_image_path) && filesize($agent_image_path) > 0) {
                            $agent_image = $agent_image_path;
                        }
                        ?>
                        <img src="<?php echo $agent_image; ?>" class="rounded-circle shadow-sm border border-4 border-light mb-3" style="width: 100px; height: 100px; object-fit: cover;" alt="<?php echo $property['agent_name']; ?>">
                        <h5 class="fw-bold mb-1"><?php echo $property['agent_name']; ?></h5>
                        <p class="text-accent small fw-bold">Expert Real Estate Agent</p>
                    </div>
                    
                    <form id="contactForm" action="process-inquiry.php" method="POST">
                        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control bg-light border-0 py-3" placeholder="Your Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control bg-light border-0 py-3" placeholder="Email Address" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="message" class="form-control bg-light border-0 py-3" rows="4" required>I am interested in "<?php echo $property['title']; ?>" and would like to schedule a viewing.</textarea>
                        </div>
                        <button type="submit" class="btn btn-accent w-100 py-3 fw-bold rounded-3 mb-3">Send Message</button>
                    </form>
                    
                    <div class="d-flex gap-2">
                        <a href="tel:<?php echo $property['agent_phone']; ?>" class="btn btn-outline-primary w-100 py-3 rounded-3"><i class="fas fa-phone-alt me-2"></i> Call</a>
                        <button class="btn btn-outline-danger w-100 py-3 rounded-3 btn-favorite" data-id="<?php echo $property_id; ?>"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
