<?php 
require_once 'includes/auth_check.php';

// Handle Message to Agent
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_agent'])) {
    $agent_id = (int)$_POST['agent_id'];
    $agent_email = $_POST['agent_email'];
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO admin_agent_messages (agent_id, subject, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $agent_id, $subject, $message);
    
    if ($stmt->execute()) {
        // Send actual email
        $headers = "From: admin@eliteestates.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $email_body = "<h3>Message from Admin</h3><p>$message</p>";
        @mail($agent_email, $subject, $email_body, $headers);

        header('Location: agents.php?messaged=1');
        exit;
    } else {
        $error = "Error sending message: " . $conn->error;
    }
}

// Handle Agent Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    
    // Check if agent has properties before deleting (optional but recommended for data integrity)
    $check_sql = "SELECT COUNT(*) as count FROM properties WHERE agent_id = '$delete_id'";
    $check_res = $conn->query($check_sql);
    $prop_count = $check_res->fetch_assoc()['count'];
    
    if ($prop_count > 0) {
        $error = "Cannot delete agent. They have $prop_count active listings. Reassign or delete the listings first.";
    } else {
        $sql = "DELETE FROM agents WHERE id = '$delete_id'";
        if ($conn->query($sql)) {
            header('Location: agents.php?deleted=1');
            exit;
        } else {
            $error = "Error deleting agent: " . $conn->error;
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_agent'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $bio = $conn->real_escape_string($_POST['bio']);

    $profile_image = 'https://i.pravatar.cc/300?u=' . urlencode($name);
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $upload_dir = realpath(__DIR__ . '/../assets/images/agents') . '/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('agent_') . '.' . $extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $profile_image = $file_name;
        }
    }

    $sql = "INSERT INTO agents (name, email, phone, profile_image, bio) 
            VALUES ('$name', '$email', '$phone', '$profile_image', '$bio')";

    if ($conn->query($sql)) {
        header('Location: agents.php?success=1');
        exit;
    }
}

include 'includes/header.php'; 

// Fetch all agents
$agents_sql = "SELECT a.*, (SELECT COUNT(*) FROM properties WHERE agent_id = a.id) as property_count FROM agents a ORDER BY a.name ASC";
$agents_result = $conn->query($agents_sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <div>
        <h2 class="fw-bold m-0">Manage Agents</h2>
        <p class="text-muted mb-0">Add and manage real estate agents.</p>
    </div>
    <button type="button" class="btn btn-accent px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addAgentModal">
        <i class="fas fa-user-plus me-2"></i> Add New Agent
    </button>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <strong>Success!</strong> The agent has been added successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <strong>Success!</strong> The agent has been deleted successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['messaged'])): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <strong>Success!</strong> Your message has been sent to the agent.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($error) && !empty($error)): ?>
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
                    <th class="border-0">Agent</th>
                    <th class="border-0">Contact Info</th>
                    <th class="border-0">Properties</th>
                    <th class="border-0 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($agents_result->num_rows > 0): ?>
                    <?php while($row = $agents_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php 
                                $agent_img_file = ($row['profile_image'] ?? '');
                                $agent_image = $agent_img_file;
                                if (!filter_var($agent_img_file, FILTER_VALIDATE_URL)) {
                                    $agent_image_path = '../assets/images/agents/' . $agent_img_file;
                                    $agent_image = 'https://i.pravatar.cc/300?u=' . urlencode($row['name']);
                                    if (!empty($agent_img_file) && file_exists(__DIR__ . '/' . $agent_image_path) && filesize(__DIR__ . '/' . $agent_image_path) > 0) {
                                        $agent_image = $agent_image_path;
                                    }
                                }
                                ?>
                                <img src="<?php echo $agent_image; ?>" class="rounded-circle me-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold"><?php echo $row['name']; ?></div>
                                    <div class="text-muted small">Senior Property Consultant</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-semibold"><i class="fas fa-envelope me-2 text-muted"></i><?php echo $row['email']; ?></div>
                            <div class="small fw-semibold"><i class="fas fa-phone-alt me-2 text-muted"></i><?php echo $row['phone']; ?></div>
                        </td>
                        <td>
                            <span class="badge bg-primary px-3 py-1"><?php echo $row['property_count']; ?> Listings</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" data-bs-target="#messageAgentModal" 
                                        data-agent-id="<?php echo $row['id']; ?>" 
                                        data-agent-name="<?php echo htmlspecialchars($row['name']); ?>"
                                        data-agent-email="<?php echo htmlspecialchars($row['email']); ?>"
                                        title="Message Agent">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <a href="../agent.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="agents.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this agent?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center py-5 text-muted">No agents found. Add one to get started.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Add New Agent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="agents.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. john@eliteestates.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="e.g. +1 (234) 567-8900" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Bio</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="Tell us about the agent..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Profile Photo</label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_agent" class="btn btn-accent px-4 fw-bold">Save Agent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Message Agent Modal -->
<div class="modal fade" id="messageAgentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="messageAgentModalLabel">Message Agent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="agents.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="agent_id" id="modal_agent_id">
                    <input type="hidden" name="agent_email" id="modal_agent_email">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Message</label>
                        <textarea name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="message_agent" class="btn btn-accent px-4 fw-bold">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageAgentModal = document.getElementById('messageAgentModal');
    if (messageAgentModal) {
        messageAgentModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const agentId = button.getAttribute('data-agent-id');
            const agentName = button.getAttribute('data-agent-name');
            const agentEmail = button.getAttribute('data-agent-email');

            const modalTitle = messageAgentModal.querySelector('.modal-title');
            const modalAgentIdInput = messageAgentModal.querySelector('#modal_agent_id');
            const modalAgentEmailInput = messageAgentModal.querySelector('#modal_agent_email');

            modalTitle.textContent = 'Message ' + agentName;
            modalAgentIdInput.value = agentId;
            modalAgentEmailInput.value = agentEmail;
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
