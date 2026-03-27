<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['property_id'])) {
    $property_id = $conn->real_escape_string($_POST['property_id']);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check if already favorited
    $check_sql = "SELECT * FROM favorites WHERE ip_address = '$ip_address' AND property_id = '$property_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Remove from favorites
        $delete_sql = "DELETE FROM favorites WHERE ip_address = '$ip_address' AND property_id = '$property_id'";
        if ($conn->query($delete_sql)) {
            echo 'removed';
        }
    } else {
        // Add to favorites
        $insert_sql = "INSERT INTO favorites (ip_address, property_id) VALUES ('$ip_address', '$property_id')";
        if ($conn->query($insert_sql)) {
            echo 'added';
        }
    }
}
?>
