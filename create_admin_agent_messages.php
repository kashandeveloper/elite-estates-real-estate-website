<?php
require_once 'includes/db.php';

$sql = "CREATE TABLE IF NOT EXISTS admin_agent_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agent_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql)) {
    echo "Table 'admin_agent_messages' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>