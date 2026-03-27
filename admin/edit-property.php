<?php 
require_once 'includes/auth_check.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: properties.php');
    exit;
}

$id = $conn->real_escape_string($_GET['id']);

// Fetch property data
$sql = "SELECT * FROM properties WHERE id = '$id'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    header('Location: properties.php');
    exit;
}
$property = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_property'])) {
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

    // Handle Main Image Update
    $main_image = $property['main_image'];
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $upload_dir = '../assets/images/properties/';
        $extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('prop_') . '.' . $extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file)) {
            // Delete old image if it's not a URL and not the default
            if (!filter_var($main_image, FILTER_VALIDATE_URL) && $main_image != 'default-property.jpg' && file_exists($upload_dir . $main_image)) {
                unlink($upload_dir . $main_image);
            }
            $main_image = $file_name;
        }
    }

    // Update Property
    $sql = "UPDATE properties SET 
            title = '$title', 
            price = '$price', 
            location = '$location', 
            bedrooms = '$bedrooms', 
            bathrooms = '$bathrooms', 
            area_sqft = '$area_sqft', 
            type = '$type', 
            status = '$status', 
            agent_id = '$agent_id', 
            description = '$description', 
            main_image = '$main_image', 
            google_map_link = '$google_map_link' 
            WHERE id = '$id'";

    if ($conn->query($sql)) {
        header('Location: properties.php?success=1');
        exit;
    } else {
        $error = "Error updating property: " . $conn->error;
    }
}

include 'includes/header.php'; 

// Fetch agents for the dropdown
$agents_sql = "SELECT id, name FROM agents";
$agents_result = $conn->query($agents_sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <div>
        <h2 class="fw-bold m-0">Edit Property</h2>
        <p class="text-muted mb-0">Modify the details for "<?php echo $property['title']; ?>"</p>
    </div>
    <a href="properties.php" class="btn btn-outline-secondary px-4 py-2">
        <i class="fas fa-arrow-left me-2"></i> Back to List
    </a>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<form action="edit-property.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
    <div class="row g-4">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3">Basic Information</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Property Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $property['title']; ?>" required>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Price ($)</label>
                        <input type="number" name="price" class="form-control" value="<?php echo $property['price']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo $property['location']; ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="8" required><?php echo $property['description']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Google Map Link (Embed URL)</label>
                    <input type="text" name="google_map_link" class="form-control" value="<?php echo $property['google_map_link']; ?>">
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3">Property Details</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Property Type</label>
                    <select name="type" class="form-select" required>
                        <?php 
                        $types = ['House', 'Apartment', 'Villa', 'Commercial', 'Land'];
                        foreach ($types as $t) {
                            $selected = ($property['type'] == $t) ? 'selected' : '';
                            echo "<option value='$t' $selected>$t</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <?php 
                        $statuses = ['For Sale', 'For Rent', 'Sold'];
                        foreach ($statuses as $s) {
                            $selected = ($property['status'] == $s) ? 'selected' : '';
                            echo "<option value='$s' $selected>$s</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Bedrooms</label>
                        <input type="number" name="bedrooms" class="form-control" value="<?php echo $property['bedrooms']; ?>" min="0" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Bathrooms</label>
                        <input type="number" name="bathrooms" class="form-control" value="<?php echo $property['bathrooms']; ?>" min="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Area (Sq Ft)</label>
                    <input type="number" name="area_sqft" class="form-control" value="<?php echo $property['area_sqft']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Assign Agent</label>
                    <select name="agent_id" class="form-select" required>
                        <option value="">Select Agent</option>
                        <?php while($agent = $agents_result->fetch_assoc()): ?>
                            <?php $selected = ($property['agent_id'] == $agent['id']) ? 'selected' : ''; ?>
                            <option value="<?php echo $agent['id']; ?>" <?php echo $selected; ?>><?php echo $agent['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4 text-center">
                <h5 class="fw-bold mb-4 border-bottom pb-3">Featured Image</h5>
                <div class="mb-3 bg-light rounded overflow-hidden" style="height: 200px;">
                    <?php 
                    $preview_image = $property['main_image'];
                    if (!filter_var($preview_image, FILTER_VALIDATE_URL)) {
                        $preview_image = '../assets/images/properties/' . $preview_image;
                    }
                    ?>
                    <img id="mainPreview" src="<?php echo $preview_image; ?>" alt="Preview" class="img-fluid" style="height: 100%; width: 100%; object-fit: cover;">
                </div>
                <input type="file" name="main_image" id="mainImageInput" class="form-control" accept="image/*">
                <small class="text-muted d-block mt-2">Leave blank to keep current image.</small>
            </div>

            <button type="submit" name="update_property" class="btn btn-accent w-100 py-3 fw-bold">
                <i class="fas fa-check-circle me-2"></i> Update Property
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('mainImageInput').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('mainPreview').src = URL.createObjectURL(file);
        }
    };
});
</script>

<?php include 'includes/footer.php'; ?>
