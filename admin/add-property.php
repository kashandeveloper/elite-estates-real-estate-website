<?php 
require_once 'includes/auth_check.php';

// Define the absolute path for the upload directory
$upload_dir = __DIR__ . '/../assets/images/properties/';

// Ensure the upload directory exists, create it if it doesn't
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_property'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $price = $conn->real_escape_string($_POST['price']);
    $location = $conn->real_escape_string($_POST['location']);
    $bedrooms = $conn->real_escape_string($_POST['bedrooms']);
    $bathrooms = $conn->real_escape_string($_POST['bathrooms']);
    $area_sqft = $conn->real_escape_string($_POST['area_sqft']);
    $type = $conn->real_escape_string($_POST['type']);
    $status = $conn->real_escape_string($_POST['status']);
    $agent_id = $conn->real_escape_string($_POST['agent_id']);
    $description = $conn->real_escape_string($_POST['description']);
    $google_map_link = $conn->real_escape_string($_POST['google_map_link']);

    // Handle Main Image Upload
    $main_image = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('prop_') . '.' . $extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
            $main_image = $file_name;
        }
    }

    // Insert Property
    $sql = "INSERT INTO properties (title, price, location, bedrooms, bathrooms, area_sqft, type, status, agent_id, description, main_image, google_map_link, views) 
            VALUES ('$title', '$price', '$location', '$bedrooms', '$bathrooms', '$area_sqft', '$type', '$status', '$agent_id', '$description', '$main_image', '$google_map_link', 0)";

    if ($conn->query($sql)) {
        $property_id = $conn->insert_id;

        // Handle Additional Images Upload
        if (isset($_FILES['additional_images'])) {
            foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['additional_images']['error'][$key] == 0) {
                    $extension = pathinfo($_FILES['additional_images']['name'][$key], PATHINFO_EXTENSION);
                    $file_name = uniqid('gallery_') . '.' . $extension;
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $conn->query("INSERT INTO property_images (property_id, image_path) VALUES ('$property_id', '$file_name')");
                    }
                }
            }
        }
        
        header('Location: properties.php?success=1');
        exit;
    } else {
        $error = "Error adding property: " . $conn->error;
    }
}

include 'includes/header.php'; 

// Fetch agents for the dropdown
$agents_sql = "SELECT id, name FROM agents";
$agents_result = $conn->query($agents_sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <div>
        <h2 class="fw-bold m-0">Add New Property</h2>
        <p class="text-muted mb-0">Fill in the details to list a new property.</p>
    </div>
    <a href="properties.php" class="btn btn-outline-secondary px-4 py-2">
        <i class="fas fa-arrow-left me-2"></i> Back to List
    </a>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="add-property.php" method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3">Basic Information</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Property Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Modern Luxury Villa with Pool" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Price ($)</label>
                        <input type="number" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Location</label>
                        <input type="text" name="location" class="form-control" placeholder="e.g. Manhattan, NY" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="8" placeholder="Detailed property description..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Google Map Link (Embed URL)</label>
                    <input type="text" name="google_map_link" class="form-control" placeholder="Paste the src URL from Google Maps embed code">
                    <small class="text-muted">Example: https://www.google.com/maps/embed?pb=...</small>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3">Gallery Images</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Additional Images</label>
                    <input type="file" name="additional_images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">You can select multiple images at once.</small>
                </div>
                <div id="imagePreview" class="row g-2 mt-3"></div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3">Property Details</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Property Type</label>
                    <select name="type" class="form-select" required>
                        <option value="House">House</option>
                        <option value="Apartment">Apartment</option>
                        <option value="Villa">Villa</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Land">Land</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="For Sale">For Sale</option>
                        <option value="For Rent">For Rent</option>
                        <option value="Sold">Sold</option>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Bedrooms</label>
                        <input type="number" name="bedrooms" class="form-control" min="0" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Bathrooms</label>
                        <input type="number" name="bathrooms" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Area (Sq Ft)</label>
                    <input type="number" name="area_sqft" class="form-control" placeholder="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Assign Agent</label>
                    <select name="agent_id" class="form-select" required>
                        <option value="">Select Agent</option>
                        <?php while($agent = $agents_result->fetch_assoc()): ?>
                            <option value="<?php echo $agent['id']; ?>"><?php echo $agent['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4 text-center">
                <h5 class="fw-bold mb-4 border-bottom pb-3">Main Featured Image</h5>
                <div class="mb-3">
                    <div class="mb-3 bg-light rounded d-flex align-items-center justify-content-center overflow-hidden" style="height: 200px;">
                        <img id="mainPreview" src="#" alt="Main Image Preview" class="img-fluid d-none" style="height: 100%; width: 100%; object-fit: cover;">
                        <i id="mainIcon" class="fas fa-image fa-4x text-muted"></i>
                    </div>
                    <input type="file" name="main_image" id="mainImageInput" class="form-control" accept="image/*" required>
                    <small class="text-muted">This image will be shown on listings.</small>
                </div>
            </div>

            <button type="submit" name="add_property" class="btn btn-accent w-100 py-3 fw-bold">
                <i class="fas fa-check-circle me-2"></i> Save Property
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview Main Image
    document.getElementById('mainImageInput').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('mainPreview').src = URL.createObjectURL(file);
            document.getElementById('mainPreview').classList.remove('d-none');
            document.getElementById('mainIcon').classList.add('d-none');
        }
    };
});
</script>

<?php include 'includes/footer.php'; ?>
