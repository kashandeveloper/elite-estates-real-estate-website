<?php 
require_once 'includes/auth_check.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $upload_dir = '../assets/images/properties/';
    
    // Get image filenames to delete them from disk
    $sql = "SELECT main_image FROM properties WHERE id = '$id'";
    $res = $conn->query($sql);
    if ($row = $res->fetch_assoc()) {
        $main_image = $row['main_image'];
        if ($main_image != 'default-property.jpg' && file_exists($upload_dir . $main_image)) {
            unlink($upload_dir . $main_image);
        }
    }
    
    // Get additional images
    $sql = "SELECT image_path FROM property_images WHERE property_id = '$id'";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $img_path = $row['image_path'];
        if (file_exists($upload_dir . $img_path)) {
            unlink($upload_dir . $img_path);
        }
    }

    // Delete from database (assuming foreign key constraints handle favorites and gallery images, 
    // but better to be explicit if not sure)
    $conn->query("DELETE FROM property_images WHERE property_id = '$id'");
    $conn->query("DELETE FROM favorites WHERE property_id = '$id'");
    $conn->query("DELETE FROM contact_messages WHERE property_id = '$id'");
    
    if ($conn->query("DELETE FROM properties WHERE id = '$id'")) {
        header('Location: properties.php?success=1');
        exit;
    }
}
header('Location: properties.php');
?>
