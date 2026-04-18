<?php
$pageTitle = 'Admin Dashboard';
require_once '../../includes/header.php';
requireAdmin();

use Library\Library\Library;

$library = new Library();

$stats = $library->getAdminDashboard();
$reminders = $library->sendDueReminder();

require_once '../../includes/nav-admin.php';
?>

<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4"><?php echo $stats['totalUsers']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pending Users</h5>
                    <p class="card-text display-4"><?php echo $stats['totalPendingUsers']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Available Books</h5>
                    <p class="card-text display-4"><?php echo $stats['totalAvailBooks']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pending Requests</h5>
                    <p class="card-text display-4"><?php echo $stats['totalPendingRequests']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5>Overdue Books (<?php echo count($reminders['overdue']); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($reminders['overdue'])) { ?>
                        <p class="text-muted">No overdue books</p>
                    <?php } else { ?>
                        <ul class="list-group">
                            <?php foreach ($reminders['overdue'] as $item) { ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($item['user_name']); ?></strong> - 
                                    <?php echo htmlspecialchars($item['title']); ?>
                                    <br><small class="text-danger">Due: <?php echo $item['due_date']; ?></small>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5>Due Soon (<?php echo count($reminders['due_soon']); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($reminders['due_soon'])) { ?>
                        <p class="text-muted">No books due soon</p>
                    <?php } else { ?>
                        <ul class="list-group">
                            <?php foreach ($reminders['due_soon'] as $item) { ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($item['user_name']); ?></strong> - 
                                    <?php echo htmlspecialchars($item['title']); ?>
                                    <br><small class="text-warning">Due: <?php echo $item['due_date']; ?></small>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>