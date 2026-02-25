<?php
require_once 'includes/db.php';
$conn = getDBConnection();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT * FROM employees WHERE status = 'transfer'";

if (!empty($search)) {
    $search_escaped = escapeString($conn, $search);
    $query .= " AND (name LIKE '%$search_escaped%' OR position LIKE '%$search_escaped%' OR department LIKE '%$search_escaped%' OR id LIKE '%$search_escaped%' OR transfer_location LIKE '%$search_escaped%')";
}

$query .= " ORDER BY status_date DESC";
$result = $conn->query($query);
$employees = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

include 'includes/header.php';
?>

<div class="page-content">
    <div class="page-header">
        <h1 class="page-title">History</h1>
        <p class="page-subtitle">View employee history records by category</p>
    </div>

    <div class="history-tabs">
        <div class="tabs-nav">
            <a href="history-inactive.php" class="tab-link tab-inactive">
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
            <a href="history-transfer.php" class="tab-link tab-transfer active">
                <i data-lucide="arrow-right-left"></i>
                Transfer
            </a>
        </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <div class="search-container" style="max-width: 28rem;">
            <i data-lucide="search" class="search-icon"></i>
            <form method="GET" action="">
                <input type="text" name="search" class="search-input" placeholder="Search transferred employees..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>
    </div>

    <div style="margin-bottom: 1.5rem;">
        <div class="stat-card stat-card-blue" style="display: inline-block; width: auto; min-width: 300px;">
            <div class="stat-card-content">
                <div class="stat-icon">
                    <i data-lucide="arrow-right-left" style="width: 32px; height: 32px;"></i>
                </div>
                <div class="stat-card-info" style="margin-left: 1rem;">
                    <p class="stat-label">Total Transferred Employees</p>
                    <p class="stat-value"><?php echo count($employees); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr style="background: linear-gradient(to right, #eff6ff, #dbeafe);">
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Date Joined</th>
                        <th>Transfer Date</th>
                        <th>Transfer Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($employees) > 0): ?>
                        <?php foreach ($employees as $employee): ?>
                        <tr style="transition: background 0.2s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background=''">
                            <td class="employee-id" style="color: #2563eb;"><?php echo htmlspecialchars($employee['id']); ?></td>
                            <td style="font-weight: 500; color: #1f2937;"><?php echo htmlspecialchars($employee['name']); ?></td>
                            <td><?php echo htmlspecialchars($employee['position']); ?></td>
                            <td>
                                <span class="badge badge-outline badge-outline-blue">
                                    <?php echo htmlspecialchars($employee['department']); ?>
                                </span>
                            </td>
                            <td><?php echo date('m/d/Y', strtotime($employee['date_joined'])); ?></td>
                            <td><?php echo $employee['status_date'] ? date('m/d/Y', strtotime($employee['status_date'])) : '-'; ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i data-lucide="map-pin" style="width: 16px; height: 16px; color: #3b82f6;"></i>
                                    <span style="color: #374151; font-weight: 500;">
                                        <?php echo $employee['transfer_location'] ? htmlspecialchars($employee['transfer_location']) : '-'; ?>
                                    </span>
                                </div>
                            </td>
                            <td><span class="badge badge-transfer">Transferred</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i data-lucide="arrow-right-left" class="empty-icon" style="width: 48px; height: 48px;"></i>
                                    <p class="empty-title">No transferred employees found</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>

<?php
closeDBConnection($conn);
include 'includes/footer.php';
?>
