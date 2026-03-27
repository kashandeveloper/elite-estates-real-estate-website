<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="section-padding bg-primary text-white text-center position-relative" style="background: linear-gradient(rgba(10, 37, 64, 0.8), rgba(10, 37, 64, 0.8)), url('https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="container py-5">
        <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">My Favorite Properties</h1>
        <p class="lead mb-0 opacity-75" data-aos="fade-up" data-aos-delay="100">Properties you've saved to your wishlist.</p>
    </div>
</section>

<!-- Favorites Content -->
<section class="section-padding bg-light min-vh-100">
    <div class="container">
        <div class="row g-4">
            <?php
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $sql = "SELECT p.* FROM properties p 
                    JOIN favorites f ON p.id = f.property_id 
                    WHERE f.ip_address = '$ip_address' 
                    ORDER BY f.id DESC";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <div class="property-card h-100">
                            <div class="property-image">
                                <span class="property-badge"><?php echo $row['status']; ?></span>
                                <span class="property-type"><?php echo $row['type']; ?></span>
                                <?php 
                                $prop_img_file = ($row['main_image'] ?? '');
                                $image_path = $prop_img_file;
                                if (!filter_var($prop_img_file, FILTER_VALIDATE_URL)) {
                                    $image_path_rel = 'assets/images/properties/' . $prop_img_file;
                                    $image_path = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
                                    if (!empty($prop_img_file) && file_exists(__DIR__ . '/' . $image_path_rel) && filesize(__DIR__ . '/' . $image_path_rel) > 0) {
                                        $image_path = $image_path_rel;
                                    }
                                }
                                ?>
                                <img src="<?php echo $image_path; ?>" alt="<?php echo $row['title']; ?>">
                            </div>
                            <div class="property-content">
                                <div class="property-price">$<?php echo number_format($row['price']); ?></div>
                                <h5 class="property-title"><?php echo $row['title']; ?></h5>
                                <p class="property-location small"><i class="fas fa-map-marker-alt me-2 text-accent"></i><?php echo $row['location']; ?></p>
                                <div class="property-features">
                                    <span class="feature-item"><i class="fas fa-bed"></i> <?php echo $row['bedrooms']; ?> Beds</span>
                                    <span class="feature-item"><i class="fas fa-bath"></i> <?php echo $row['bathrooms']; ?> Baths</span>
                                    <span class="feature-item"><i class="fas fa-expand"></i> <?php echo $row['area_sqft']; ?> Sqft</span>
                                </div>
                                <div class="d-flex gap-2 mt-4">
                                    <a href="property-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-accent w-100">View Details</a>
                                    <button class="btn btn-outline-danger btn-favorite active" data-id="<?php echo $row['id']; ?>"><i class="fas fa-heart"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5" data-aos="fade-up">
                        <div class="bg-white p-5 rounded-4 shadow-sm">
                            <i class="far fa-heart fa-4x text-muted mb-4 opacity-25"></i>
                            <h4 class="text-dark fw-bold">No Saved Properties</h4>
                            <p class="text-muted">You haven\'t added any properties to your favorites yet. Start exploring and save what you love!</p>
                            <a href="properties.php" class="btn btn-accent mt-3 px-4 py-2 rounded-pill">Browse All Properties</a>
                        </div>
                      </div>';
            }
            ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
