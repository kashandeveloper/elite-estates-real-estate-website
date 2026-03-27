<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $message = $conn->real_escape_string($_POST['message']);
    $property_id = isset($_POST['property_id']) ? $conn->real_escape_string($_POST['property_id']) : 'NULL';

    $sql = "INSERT INTO contact_messages (name, email, phone, message, property_id) 
            VALUES ('$name', '$email', '$phone', '$message', $property_id)";

    if ($conn->query($sql)) {
        if ($property_id != 'NULL') {
            header("Location: property-detail.php?id=$property_id&success=1");
        } else {
            header("Location: contact.php?success=1");
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    header("Location: index.php");
}
?>
