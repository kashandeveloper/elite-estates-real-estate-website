<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    $agent_id = isset($_POST['agent_id']) ? (int)$_POST['agent_id'] : 0;
    $sender_name = isset($_POST['sender_name']) ? trim($_POST['sender_name']) : '';
    $sender_email = isset($_POST['sender_email']) ? trim($_POST['sender_email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($agent_id) || empty($sender_name) || empty($sender_email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        exit;
    }

    // Prepare statement for security
    $stmt = $conn->prepare("INSERT INTO agent_messages (agent_id, sender_name, sender_email, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $agent_id, $sender_name, $sender_email, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try again later.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>