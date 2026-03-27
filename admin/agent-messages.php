<?php 
require_once 'includes/auth_check.php';

// Handle Admin Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $message_id = (int)$_POST['message_id'];
    $reply_text = trim($_POST['admin_reply']);
    $sender_email = $_POST['sender_email'];

    if (!empty($reply_text)) {
        // First check if fields exist (safety check)
        $check_fields = $conn->query("SHOW COLUMNS FROM agent_messages LIKE 'admin_reply'");
        if ($check_fields->num_rows == 0) {
            $conn->query("ALTER TABLE agent_messages ADD COLUMN admin_reply TEXT DEFAULT NULL AFTER message, ADD COLUMN reply_date DATETIME DEFAULT NULL AFTER admin_reply");
        }

        $sql = "UPDATE agent_messages SET admin_reply = ?, reply_date = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("si", $reply_text, $message_id);
            
            if ($stmt->execute()) {
                // Optional: Send email notification
                $subject = "Reply to your property inquiry - Elite Estates";
                $headers = "From: info@eliteestates.com\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $email_body = "<h3>Hello,</h3><p>Thank you for your inquiry. Here is our response:</p><div style='padding: 15px; background: #f8f9fa; border-radius: 5px;'>" . nl2br(htmlspecialchars($reply_text)) . "</div><p>Best regards,<br>Elite Estates Team</p>";
                
                @mail($sender_email, $subject, $email_body, $headers);

                header('Location: agent-messages.php?replied=1');
                exit;
            } else {
                $error = "SQL Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Prepare Error: " . $conn->error;
        }
    } else {
        $error = "Reply message cannot be empty.";
    }
}

// Handle Message Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $sql = "DELETE FROM agent_messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header('Location: agent-messages.php?deleted=1');
        exit;
    } else {
        $error = "Error deleting message: " . $conn->error;
    }
}

include 'includes/header.php'; 

// Fetch all agent messages with agent names
$sql = "SELECT am.*, a.name as agent_name 
        FROM agent_messages am 
        JOIN agents a ON am.agent_id = a.id 
        ORDER BY am.created_at DESC";
$result = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <div>
        <h2 class="fw-bold m-0">Agent Messages</h2>
        <p class="text-muted mb-0">Manage inquiries sent directly to your real estate agents.</p>
    </div>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <strong>Success!</strong> The message has been deleted successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['replied'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <strong>Success!</strong> Your reply has been saved and sent.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <strong>Error!</strong> <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0">Sender</th>
                    <th class="border-0">Agent</th>
                    <th class="border-0">Message</th>
                    <th class="border-0">Status</th>
                    <th class="border-0">Date</th>
                    <th class="border-0 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?php echo htmlspecialchars($row['sender_name']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($row['sender_email']); ?></div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary px-3 py-1"><?php echo htmlspecialchars($row['agent_name']); ?></span>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($row['message']); ?>">
                                <?php echo htmlspecialchars($row['message']); ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($row['admin_reply'])): ?>
                                <span class="badge bg-success">Replied</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small text-muted"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></div>
                            <div class="small text-muted opacity-50"><?php echo date('h:i A', strtotime($row['created_at'])); ?></div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewMessageModal<?php echo $row['id']; ?>" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#replyMessageModal<?php echo $row['id']; ?>" title="Reply">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <a href="agent-messages.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this message?');" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>

                            <!-- View Message Modal -->
                            <div class="modal fade" id="viewMessageModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                                            <h5 class="modal-title fw-bold">Message Details</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="small fw-bold text-muted d-block">From</label>
                                                <p class="mb-0 fw-bold"><?php echo htmlspecialchars($row['sender_name']); ?> (<?php echo htmlspecialchars($row['sender_email']); ?>)</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold text-muted d-block">To Agent</label>
                                                <p class="mb-0 fw-bold"><?php echo htmlspecialchars($row['agent_name']); ?></p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold text-muted d-block">Date Received</label>
                                                <p class="mb-0"><?php echo date('F d, Y \a\t h:i A', strtotime($row['created_at'])); ?></p>
                                            </div>
                                            <hr>
                                            <div class="mb-3">
                                                <label class="small fw-bold text-muted d-block">Message</label>
                                                <p class="mb-0 bg-light p-3 rounded-3" style="white-space: pre-wrap;"><?php echo htmlspecialchars($row['message']); ?></p>
                                            </div>
                                            <?php if (!empty($row['admin_reply'])): ?>
                                            <div class="mb-0">
                                                <label class="small fw-bold text-success d-block">Admin Reply (<?php echo date('M d, Y', strtotime($row['reply_date'])); ?>)</label>
                                                <p class="mb-0 bg-success-subtle p-3 rounded-3" style="white-space: pre-wrap;"><?php echo htmlspecialchars($row['admin_reply']); ?></p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer border-0 p-4 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reply Message Modal -->
                            <div class="modal fade" id="replyMessageModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header bg-success text-white border-0 py-3 px-4">
                                            <h5 class="modal-title fw-bold">Reply to <?php echo htmlspecialchars($row['sender_name']); ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="agent-messages.php" method="POST">
                                            <div class="modal-body p-4 text-start">
                                                <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="sender_email" value="<?php echo htmlspecialchars($row['sender_email']); ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="small fw-bold text-muted d-block">Original Message</label>
                                                    <div class="bg-light p-3 rounded-3 small text-muted"><?php echo htmlspecialchars($row['message']); ?></div>
                                                </div>
                                                
                                                <div class="mb-0">
                                                    <label for="admin_reply_<?php echo $row['id']; ?>" class="small fw-bold text-muted d-block mb-2">Your Reply</label>
                                                    <textarea name="admin_reply" id="admin_reply_<?php echo $row['id']; ?>" class="form-control border-0 bg-light" rows="5" placeholder="Write your response here..." required><?php echo $row['admin_reply']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="submit" name="send_reply" class="btn btn-success rounded-pill px-4 fw-bold">Send Reply</button>
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>