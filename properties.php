<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="section-padding bg-primary text-white text-center position-relative" style="background: linear-gradient(rgba(10, 37, 64, 0.8), rgba(10, 37, 64, 0.8)), url('https://images.unsplash.com/photo-1449824913935-59a10b8d2000?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;">
    <div class="container py-5">
        <h1 class="display-4 fw-bold mb-3" data-aos="fade-up">Explore Our Properties</h1>
        <p class="lead mb-0 opacity-75" data-aos="fade-up" data-aos-delay="100">Find your perfect home from our curated selection of luxury listings.</p>
    </div>
</section>

<!-- Search and Listing Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-5">
                <div class="card border-0 shadow-sm p-4 rounded-4" data-aos="fade-right">
                    <h5 class="fw-bold mb-4 border-bottom pb-3"><i class="fas fa-filter me-2 text-accent"></i> Filter Properties</h5>
                    <form action="properties.php" method="GET">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keyword</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="keyword" class="form-control border-start-0 ps-0" placeholder="Search title..." value="<?php echo $_GET['keyword'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Location</label>
                            <select name="location" class="form-select">
                                <option value="">All Locations</option>
                                <option value="New York" <?php echo ($_GET['location'] ?? '') == 'New York' ? 'selected' : ''; ?>>New York</option>
                                <option value="London" <?php echo ($_GET['location'] ?? '') == 'London' ? 'selected' : ''; ?>>London</option>
                                <option value="Los Angeles" <?php echo ($_GET['location'] ?? '') == 'Los Angeles' ? 'selected' : ''; ?>>Los Angeles</option>
                                <option value="Miami" <?php echo ($_GET['location'] ?? '') == 'Miami' ? 'selected' : ''; ?>>Miami</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Property Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="House" <?php echo ($_GET['type'] ?? '') == 'House' ? 'selected' : ''; ?>>House</option>
                                <option value="Apartment" <?php echo ($_GET['type'] ?? '') == 'Apartment' ? 'selected' : ''; ?>>Apartment</option>
                                <option value="Villa" <?php echo ($_GET['type'] ?? '') == 'Villa' ? 'selected' : ''; ?>>Villa</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo $_GET['min_price'] ?? ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo $_GET['max_price'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bedrooms</label>
                            <select name="bedrooms" class="form-select">
                                <option value="">Any</option>
                                <option value="1" <?php echo ($_GET['bedrooms'] ?? '') == '1' ? 'selected' : ''; ?>>1+</option>
                                <option value="2" <?php echo ($_GET['bedrooms'] ?? '') == '2' ? 'selected' : ''; ?>>2+</option>
                                <option value="3" <?php echo ($_GET['bedrooms'] ?? '') == '3' ? 'selected' : ''; ?>>3+</option>
                                <option value="4" <?php echo ($_GET['bedrooms'] ?? '') == '4' ? 'selected' : ''; ?>>4+</option>
                                <option value="5" <?php echo ($_GET['bedrooms'] ?? '') == '5' ? 'selected' : ''; ?>>5+</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-accent w-100 py-3 fw-bold rounded-3">Apply Filter</button>
                        <a href="properties.php" class="btn btn-link w-100 text-muted mt-2 text-decoration-none small">Clear All Filters</a>
                    </form>
                </div>
            </div>
            
            <!-- Properties Listing -->
            <div class="col-lg-9">
                <div class="row g-4">
                    <?php
                    // Build Search Query
                    $sql = "SELECT * FROM properties WHERE 1=1";
                    
                    if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                        $keyword = $conn->real_escape_string($_GET['keyword']);
                        $sql .= " AND (title LIKE '%$keyword%' OR description LIKE '%$keyword%')";
                    }
                    if (isset($_GET['location']) && !empty($_GET['location'])) {
                        $location = $conn->real_escape_string($_GET['location']);
                        $sql .= " AND location = '$location'";
                    }
                    if (isset($_GET['type']) && !empty($_GET['type'])) {
                        $type = $conn->real_escape_string($_GET['type']);
                        $sql .= " AND type = '$type'";
                    }
                    if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
                        $min_price = $conn->real_escape_string($_GET['min_price']);
                        $sql .= " AND price >= $min_price";
                    }
                    if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
                        $max_price = $conn->real_escape_string($_GET['max_price']);
                        $sql .= " AND price <= $max_price";
                    }
                    if (isset($_GET['bedrooms']) && !empty($_GET['bedrooms'])) {
                        $bedrooms = $conn->real_escape_string($_GET['bedrooms']);
                        $sql .= " AND bedrooms >= $bedrooms";
                    }
                    
                    $sql .= " ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            ?>
                            <div class="col-md-6" data-aos="fade-up">
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
                                        <a href="property-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-accent w-100 mt-4">View Details</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center py-5" data-aos="fade-up">
                                <div class="bg-white p-5 rounded-4 shadow-sm">
                                    <i class="fas fa-search-minus fa-4x text-muted mb-4"></i>
                                    <h4 class="text-dark fw-bold">No Properties Found</h4>
                                    <p class="text-muted">We couldn\'t find any properties matching your criteria. Try adjusting your filters.</p>
                                    <a href="properties.php" class="btn btn-accent mt-3 px-4 py-2">View All Properties</a>
                                </div>
                              </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
