<?php
require_once 'includes/db.php';
$conn = getDBConnection();

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : 'all';

// Build query
$query = "SELECT * FROM requests WHERE 1=1";

if (!empty($search)) {
    $search_escaped = escapeString($conn, $search);
    $query .= " AND (employee_name LIKE '%$search_escaped%' 
                OR request_type LIKE '%$search_escaped%' 
                OR id LIKE '%$search_escaped%' 
                OR description LIKE '%$search_escaped%')";
}

if ($status_filter !== 'all') {
    $status_escaped = escapeString($conn, $status_filter);
    $query .= " AND status = '$status_escaped'";
}

$query .= " ORDER BY request_date DESC";

$result = $conn->query($query);
$requests = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

// Get statistics
$all_requests = $conn->query("SELECT * FROM requests")->num_rows;
$pending_count = $conn->query("SELECT * FROM requests WHERE status = 'pending'")->num_rows;
$approved_count = $conn->query("SELECT * FROM requests WHERE status = 'approved'")->num_rows;
$rejected_count = $conn->query("SELECT * FROM requests WHERE status = 'rejected'")->num_rows;

include 'includes/header.php';
?>

<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">Request List</h1>
        <p class="page-subtitle">Manage and review employee requests</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="search-container">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="" id="searchForm">
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search requests by name, type, or ID..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >
            </form>
        </div>
        <div class="button-group">
            <a href="?search=<?php echo urlencode($search); ?>&status=all" 
               class="btn-filter filter-all <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                <i data-lucide="filter"></i>
                All
            </a>
            <a href="?search=<?php echo urlencode($search); ?>&status=pending" 
               class="btn-filter filter-pending <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                Pending
            </a>
            <a href="?search=<?php echo urlencode($search); ?>&status=approved" 
               class="btn-filter filter-approved <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">
                Approved
            </a>
            <a href="?search=<?php echo urlencode($search); ?>&status=rejected" 
               class="btn-filter filter-rejected <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">
                Rejected
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="stat-card stat-card-indigo">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Total Requests</p>
                    <p class="stat-value"><?php echo $all_requests; ?></p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="file-check" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-orange">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Pending</p>
                    <p class="stat-value"><?php echo $pending_count; ?></p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="clock" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-green">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Approved</p>
                    <p class="stat-value"><?php echo $approved_count; ?></p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="check-circle" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>

        <div class="stat-card stat-card-red">
            <div class="stat-card-content">
                <div class="stat-card-info">
                    <p class="stat-label">Rejected</p>
                    <p class="stat-value"><?php echo $rejected_count; ?></p>
                </div>
                <div class="stat-icon">
                    <i data-lucide="x-circle" style="width: 28px; height: 28px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Employee Name</th>
                        <th>Employee ID</th>
                        <th>Request Type</th>
                        <th>Request Date</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($requests) > 0): ?>
                        <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="employee-id"><?php echo htmlspecialchars($request['id']); ?></td>
                            <td class="employee-name-link" style="font-weight: 500;">
                                <?php echo htmlspecialchars($request['employee_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($request['employee_id']); ?></td>
                            <td>
                                <?php
                                $type_colors = [
                                    'leave' => 'badge-outline-blue',
                                    'transfer' => 'badge-outline-purple',
                                    'resignation' => 'badge-outline-red',
                                    'update' => 'badge-outline-green'
                                ];
                                $color_class = $type_colors[$request['request_type']] ?? '';
                                ?>
                                <span class="badge badge-outline <?php echo $color_class; ?>">
                                    <?php echo htmlspecialchars($request['request_type']); ?>
                                </span>
                            </td>
                            <td><?php echo date('m/d/Y', strtotime($request['request_date'])); ?></td>
                            <td style="max-width: 300px;">
                                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo htmlspecialchars($request['description']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $request['status']; ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($request['status'] === 'pending'): ?>
                                <div style="display: flex; gap: 0.25rem;">
                                    <button class="icon-btn icon-btn-green" title="Approve">
                                        <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                                    </button>
                                    <button class="icon-btn icon-btn-red" title="Reject">
                                        <i data-lucide="x-circle" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </div>
                                <?php else: ?>
                                <span style="color: #9ca3af; font-size: 0.875rem;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i data-lucide="alert-circle" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                    <p class="empty-title">No requests found matching your criteria</p>
                                    <p class="empty-subtitle">Try adjusting your filters or search terms</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
lucide.createIcons();
</script>

<?php
closeDBConnection($conn);
include 'includes/footer.php';
?>
