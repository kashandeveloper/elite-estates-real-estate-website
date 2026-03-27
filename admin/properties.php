<?php 
require_once 'includes/auth_check.php';
include 'includes/header.php'; 

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search logic
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$where_clause = "WHERE 1=1";
if (!empty($search)) {
    $where_clause .= " AND (p.title LIKE '%$search%' OR p.location LIKE '%$search%' OR p.type LIKE '%$search%')";
}

$sql = "SELECT p.*, a.name as agent_name, a.profile_image as agent_image FROM properties p 
        LEFT JOIN agents a ON p.agent_id = a.id 
        $where_clause
        ORDER BY p.created_at DESC LIMIT $start, $limit";
$result = $conn->query($sql);

$total_sql = "SELECT COUNT(*) as count FROM properties p $where_clause";
$total_result = $conn->query($total_sql)->fetch_assoc()['count'];
$total_pages = ceil($total_result / $limit);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold m-0 text-primary">Properties Management</h3>
        <p class="text-muted mb-0 small">Total Properties: <?php echo $total_result; ?></p>
    </div>
    <div class="d-flex gap-2">
        <form action="" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search properties..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary px-3">Filter</button>
        </form>
        <a href="add-property.php" class="btn btn-accent px-4 py-2 fw-bold text-nowrap">
            <i class="fas fa-plus me-2"></i> Add New
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-check-circle me-3 fa-lg"></i>
        <div><strong>Success!</strong> The operation was completed successfully.</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase">Property</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Price</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Status</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Agent</th>
                    <th class="border-0 py-3 text-muted small fw-bold text-uppercase">Views</th>
                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="px-4 py-3" style="min-width: 300px;">
                            <div class="d-flex align-items-center">
                                <?php 
                                $prop_img_file = ($row['main_image'] ?? '');
                                $image_path = $prop_img_file;
                                if (!filter_var($prop_img_file, FILTER_VALIDATE_URL)) {
                                    $image_path_rel = '../assets/images/properties/' . $prop_img_file;
                                    $image_path = 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
                                    if (!empty($prop_img_file) && file_exists(__DIR__ . '/' . $image_path_rel) && filesize(__DIR__ . '/' . $image_path_rel) > 0) {
                                        $image_path = $image_path_rel;
                                    }
                                }
                                ?>
                                <img src="<?php echo $image_path; ?>" class="rounded-3 me-3 shadow-sm" style="width: 60px; height: 45px; object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 220px;"><?php echo $row['title']; ?></div>
                                    <div class="text-muted small"><i class="fas fa-map-marker-alt me-1 opacity-50"></i><?php echo $row['location']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="fw-bold text-primary">$<?php echo number_format($row['price']); ?></span>
                        </td>
                        <td class="py-3">
                            <?php 
                            $badge_class = 'bg-primary-subtle text-primary';
                            if ($row['status'] == 'Sold') $badge_class = 'bg-danger-subtle text-danger';
                            if ($row['status'] == 'For Rent') $badge_class = 'bg-info-subtle text-info';
                            ?>
                            <span class="badge <?php echo $badge_class; ?> px-3 py-2 rounded-pill small"><?php echo $row['status']; ?></span>
                        </td>
                        <td class="py-3">
                            <div class="d-flex align-items-center">
                                <?php 
                                $agent_img_file = ($row['agent_image'] ?? '');
                                $agent_image = $agent_img_file;
                                if (!filter_var($agent_img_file, FILTER_VALIDATE_URL)) {
                                    $agent_image_path = '../assets/images/agents/' . $agent_img_file;
                                    $agent_image = 'https://i.pravatar.cc/300?u=' . urlencode($row['agent_name'] ?? 'agent');
                                    if (!empty($agent_img_file) && file_exists(__DIR__ . '/' . $agent_image_path) && filesize(__DIR__ . '/' . $agent_image_path) > 0) {
                                        $agent_image = $agent_image_path;
                                    }
                                }
                                ?>
                                <img src="<?php echo $agent_image; ?>" class="rounded-circle me-2" width="24" height="24">
                                <span class="small"><?php echo $row['agent_name'] ?: 'N/A'; ?></span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="text-muted small"><i class="fas fa-eye me-1 opacity-50"></i><?php echo number_format($row['views']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle p-2" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                    <li><a class="dropdown-item py-2" href="../property-detail.php?id=<?php echo $row['id']; ?>" target="_blank"><i class="fas fa-eye me-2 text-muted"></i> View Site</a></li>
                                    <li><a class="dropdown-item py-2" href="edit-property.php?id=<?php echo $row['id']; ?>"><i class="fas fa-edit me-2 text-muted"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button class="dropdown-item py-2 text-danger delete-property" data-id="<?php echo $row['id']; ?>"><i class="fas fa-trash me-2"></i> Delete</button></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-5 text-muted">No properties found. <a href="add-property.php" class="text-accent text-decoration-none">Add one now.</a></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="px-4 py-3 border-top bg-light d-flex justify-content-between align-items-center">
        <p class="mb-0 text-muted small">Showing <?php echo $start + 1; ?> to <?php echo min($start + $limit, $total_result); ?> of <?php echo $total_result; ?> properties</p>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link px-3 rounded-start" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link px-3" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link px-3 rounded-end" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete Property Confirmation
    $('.delete-property').on('click', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
            window.location.href = 'delete-property.php?id=' + id;
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
