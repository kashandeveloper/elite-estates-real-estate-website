<?php 
require_once 'includes/auth_check.php';

// Handle Admin Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reply'])) {
    $message_id = (int)$_POST['message_id'];
    $reply_text = trim($_POST['admin_reply']);
    $sender_email = $_POST['sender_email'];

    if (!empty($reply_text)) {
        $sql = "UPDATE contact_messages SET admin_reply = ?, reply_date = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("si", $reply_text, $message_id);
            if ($stmt->execute()) {
                // Send email
                $subject = "Reply to your inquiry - Elite Estates";
                $headers = "From: info@eliteestates.com\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                $email_body = "<h3>Hello,</h3><p>Thank you for contacting us. Here is our response to your inquiry:</p><div style='padding: 15px; background: #f8f9fa; border-radius: 5px;'>" . nl2br(htmlspecialchars($reply_text)) . "</div><p>Best regards,<br>Elite Estates Team</p>";
                @mail($sender_email, $subject, $email_body, $headers);

                header('Location: inquiries.php?replied=1');
                exit;
            } else {
                $error = "Error saving reply: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Prepare Error: " . $conn->error;
        }
    } else {
        $error = "Reply message cannot be empty.";
    }
}

include 'includes/header.php'; 

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT cm.*, p.title as property_title, p.main_image as property_image FROM contact_messages cm 
        LEFT JOIN properties p ON cm.property_id = p.id 
        ORDER BY cm.created_at DESC LIMIT $start, $limit";
$result = $conn->query($sql);

$total_sql = "SELECT COUNT(*) as count FROM contact_messages";
$total_result = $conn->query($total_sql)->fetch_assoc()['count'];
$total_pages = ceil($total_result / $limit);
?>

<div class="mb-4 border-bottom pb-3">
    <h2 class="fw-bold m-0">Manage Inquiries</h2>
    <p class="text-muted mb-0">Review messages from interested clients and property inquiries.</p>
</div>

<?php if (isset($_GET['replied'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <strong>Success!</strong> Your reply has been saved and sent to the client.
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
                    <th class="border-0">Client Info</th>
                    <th class="border-0">Property Inquiry</th>
                    <th class="border-0">Status</th>
                    <th class="border-0">Date Received</th>
                    <th class="border-0 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?php echo $row['name']; ?></div>
                            <div class="text-muted small"><?php echo $row['email']; ?></div>
                            <div class="text-muted small"><?php echo $row['phone'] ?: 'No Phone'; ?></div>
                        </td>
                        <td>
                            <?php if ($row['property_id']): ?>
                            <div class="d-flex align-items-center">
                                <?php 
                                $prop_img_file = ($row['property_image'] ?? '');
                                $image_path_rel = '../assets/images/properties/' . $prop_img_file;
                                $image_path = '../assets/images/properties/placeholder.jpg';
                                if (!empty($prop_img_file) && file_exists(__DIR__ . '/' . $image_path_rel)) {
                                    $image_path = $image_path_rel;
                                }
                                ?>
                                <img src="<?php echo $image_path; ?>" class="rounded me-2" style="width: 40px; height: 30px; object-fit: cover;">
                                <div class="small fw-semibold text-truncate" style="max-width: 150px;"><?php echo $row['property_title']; ?></div>
                            </div>
                            <?php else: ?>
                            <span class="badge bg-light text-dark">General Contact</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['admin_reply'])): ?>
                                <span class="badge bg-success">Replied</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?php echo date('M d, Y', strtotime($row['created_at'])); ?><br>
                            <?php echo date('H:i A', strtotime($row['created_at'])); ?>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary view-message" 
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                    data-email="<?php echo htmlspecialchars($row['email']); ?>" 
                                    data-phone="<?php echo htmlspecialchars($row['phone']); ?>" 
                                    data-message="<?php echo htmlspecialchars($row['message']); ?>" 
                                    data-reply="<?php echo htmlspecialchars($row['admin_reply'] ?? ''); ?>"
                                    data-reply-date="<?php echo $row['reply_date'] ? date('M d, Y', strtotime($row['reply_date'])) : ''; ?>"
                                    data-property="<?php echo htmlspecialchars($row['property_title'] ?: 'General Inquiry'); ?>"
                                    data-bs-toggle="modal" data-bs-target="#messageModal">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No inquiries received yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Message Detail Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Inquiry Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 border-end">
                        <div class="mb-3">
                            <label class="fw-bold text-accent small text-uppercase d-block mb-1">From</label>
                            <div id="modalName" class="fw-bold h5 mb-0"></div>
                            <div id="modalEmail" class="text-muted"></div>
                            <div id="modalPhone" class="text-muted"></div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold text-accent small text-uppercase d-block mb-1">Property</label>
                            <div id="modalProperty" class="fw-semibold"></div>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <label class="fw-bold text-accent small text-uppercase d-block mb-1">Original Message</label>
                            <div id="modalMessage" class="bg-light p-3 rounded small" style="white-space: pre-line; max-height: 200px; overflow-y: auto;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="replyDisplaySection" class="d-none">
                            <label class="fw-bold text-success small text-uppercase d-block mb-1">Admin Reply</label>
                            <div id="modalReplyDate" class="small text-muted mb-2 italic"></div>
                            <div id="modalReplyText" class="bg-success-subtle p-3 rounded small border border-success-subtle" style="white-space: pre-line;"></div>
                        </div>
                        
                        <div id="replyFormSection">
                            <label class="fw-bold text-primary small text-uppercase d-block mb-2">Send a Reply</label>
                            <form action="inquiries.php" method="POST">
                                <input type="hidden" name="message_id" id="replyMessageId">
                                <input type="hidden" name="sender_email" id="replySenderEmail">
                                <div class="mb-3">
                                    <textarea name="admin_reply" class="form-control border-0 bg-light" rows="8" placeholder="Type your response here..." required></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="send_reply" class="btn btn-primary fw-bold py-2 rounded-pill">
                                        <i class="fas fa-paper-plane me-2"></i> Submit Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.view-message').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        const message = $(this).data('message');
        const property = $(this).data('property');
        const reply = $(this).data('reply');
        const replyDate = $(this).data('reply-date');

        $('#modalName').text(name);
        $('#modalEmail').text(email);
        $('#modalPhone').text(phone);
        $('#modalMessage').text(message);
        $('#modalProperty').text(property);
        
        // Setup reply form
        $('#replyMessageId').val(id);
        $('#replySenderEmail').val(email);
        
        if (reply && reply.trim() !== '') {
            $('#replyDisplaySection').removeClass('d-none');
            $('#modalReplyText').text(reply);
            $('#modalReplyDate').text('Replied on: ' + replyDate);
            $('#replyFormSection').addClass('d-none');
        } else {
            $('#replyDisplaySection').addClass('d-none');
            $('#replyFormSection').removeClass('d-none');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
