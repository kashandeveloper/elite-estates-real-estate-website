<?php 
require_once 'includes/auth_check.php';
include 'includes/header.php'; 

// Fetch Stats
$total_properties = $conn->query("SELECT COUNT(*) as count FROM properties")->fetch_assoc()['count'];
$total_inquiries = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];
$total_agents = $conn->query("SELECT COUNT(*) as count FROM agents")->fetch_assoc()['count'];
$total_views = $conn->query("SELECT SUM(views) as count FROM properties")->fetch_assoc()['count'] ?: 0;

// Fetch Top Viewed Properties for Chart
$top_viewed_sql = "SELECT title, views FROM properties ORDER BY views DESC LIMIT 5";
$top_viewed_result = $conn->query($top_viewed_sql);
$viewed_labels = [];
$viewed_data = [];
while($row = $top_viewed_result->fetch_assoc()) {
    $viewed_labels[] = $row['title'];
    $viewed_data[] = $row['views'];
}

// Fetch Inquiries per Day for Chart
$inquiries_per_day_sql = "SELECT DATE(created_at) as date, COUNT(*) as count FROM contact_messages GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7";
$inquiries_per_day_result = $conn->query($inquiries_per_day_sql);
$inquiry_dates = [];
$inquiry_counts = [];
while($row = $inquiries_per_day_result->fetch_assoc()) {
    $inquiry_dates[] = $row['date'];
    $inquiry_counts[] = $row['count'];
}
?>

<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon icon-primary"><i class="fas fa-home"></i></div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Properties</h6>
            <h3 class="fw-bold mb-0"><?php echo number_format($total_properties); ?></h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon icon-accent"><i class="fas fa-envelope"></i></div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Inquiries</h6>
            <h3 class="fw-bold mb-0"><?php echo number_format($total_inquiries); ?></h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon icon-success"><i class="fas fa-users"></i></div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Active Agents</h6>
            <h3 class="fw-bold mb-0"><?php echo number_format($total_agents); ?></h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon icon-info"><i class="fas fa-eye"></i></div>
            <h6 class="text-muted small fw-bold text-uppercase mb-1">Total Views</h6>
            <h3 class="fw-bold mb-0"><?php echo number_format($total_views); ?></h3>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <h5 class="fw-bold mb-4">Property Performance</h5>
            <canvas id="viewsChart" height="150"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-4">
            <h5 class="fw-bold mb-4">Inquiry Trends</h5>
            <canvas id="inquiriesChart" height="320"></canvas>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm p-4 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0">Recent Inquiries</h5>
        <a href="inquiries.php" class="btn btn-primary btn-sm px-3 rounded-pill">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 rounded-start">Client Name</th>
                    <th class="border-0">Property</th>
                    <th class="border-0">Date</th>
                    <th class="border-0 rounded-end">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_inquiries_sql = "SELECT cm.*, p.title as property_title FROM contact_messages cm 
                                        LEFT JOIN properties p ON cm.property_id = p.id 
                                        ORDER BY cm.created_at DESC LIMIT 5";
                $recent_result = $conn->query($recent_inquiries_sql);
                if ($recent_result->num_rows > 0) {
                    while($row = $recent_result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-3">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold"><?php echo $row['name']; ?></p>
                                        <p class="mb-0 text-muted small"><?php echo $row['email']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $row['property_title'] ?: '<span class="text-muted">General Inquiry</span>'; ?></td>
                            <td class="text-muted small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td><span class="badge bg-light text-primary rounded-pill">New</span></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center py-5 text-muted">No recent inquiries found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Charts Initialization
        const viewsCtx = document.getElementById('viewsChart').getContext('2d');
        new Chart(viewsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($viewed_labels); ?>,
                datasets: [{
                    label: 'Views',
                    data: <?php echo json_encode($viewed_data); ?>,
                    backgroundColor: '#F97316',
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        const inquiriesCtx = document.getElementById('inquiriesChart').getContext('2d');
        new Chart(inquiriesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_reverse($inquiry_dates)); ?>,
                datasets: [{
                    label: 'Inquiries',
                    data: <?php echo json_encode(array_reverse($inquiry_counts)); ?>,
                    borderColor: '#0A2540',
                    backgroundColor: 'rgba(10, 37, 64, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#0A2540'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
