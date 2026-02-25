<?php
require_once 'includes/db.php';
$conn = getDBConnection();

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$query = "SELECT * FROM employees WHERE status = 'inactive'";

if (!empty($search)) {
    $search_escaped = escapeString($conn, $search);
    $query .= " AND (name LIKE '%$search_escaped%' 
                OR position LIKE '%$search_escaped%' 
                OR department LIKE '%$search_escaped%' 
                OR id LIKE '%$search_escaped%')";
}

$query .= " ORDER BY status_date DESC";

$result = $conn->query($query);
$employees = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

$total_count = count($employees);

include 'includes/header.php';
?>

<div class="page-content">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtitle">View employee history records by category</p>
    </div>

    <!-- Tabs -->
    <div class="history-tabs">
        <div class="tabs-nav">
            <a href="history-inactive.php" class="tab-link tab-inactive active">
                <i data-lucide="file-x"></i>
                Inactive
            </a>
            <a href="history-resign.php" class="tab-link tab-resign">
                <i data-lucide="user-minus"></i>
                Resign
            </a>
            <a href="history-retired.php" class="tab-link tab-retired">
                <i data-lucide="user-x"></i>
                Retired
            </a>
            <a href="history-transfer.php" class="tab-link tab-transfer">
                <i data-lucide="arrow-right-left"></i>
                Transfer
            </a>
        </div>
    </div>

    <!-- Search -->
    <div style="margin-bottom: 1.5rem;">
        <div class="search-container" style="max-width: 28rem;">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="" id="searchForm">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search inactive employees..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    onchange="document.getElementById('searchForm').submit()"
                >
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div style="margin-bottom: 1.5rem;">
        <div class="stat-card stat-card-gray" style="display: inline-block; width: auto; min-width: 300px;">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i data-lucide="user-x" style="width: 32px; height: 32px;"></i>
                </div>
                <div class="stat-card-info" style="margin-left: 1rem;">
                    <p class="stat-label">Total Inactive Employees</p>
                    <p class="stat-value"><?php echo $total_count; ?></p>
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
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Date Joined</th>
                        <th>Inactive Since</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($employees) > 0): ?>
                        <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td class="employee-id" style="color: #6b7280;"><?php echo htmlspecialchars($employee['id']); ?></td>
                            <td class="employee-name-link" style="font-weight: 500; color: #1f2937;">
                                <?php echo htmlspecialchars($employee['name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($employee['position']); ?></td>
                            <td>
                                <span class="badge badge-outline badge-outline-gray">
                                    <?php echo htmlspecialchars($employee['department']); ?>
                                </span>
                            </td>
                            <td><?php echo date('m/d/Y', strtotime($employee['date_joined'])); ?></td>
                            <td>
                                <?php echo $employee['status_date'] ? date('m/d/Y', strtotime($employee['status_date'])) : '-'; ?>
                            </td>
                            <td>
                                <span class="badge badge-inactive">Inactive</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i data-lucide="user-x" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                    <p class="empty-title">No inactive employees found</p>
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
