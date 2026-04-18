<?php
$pageTitle = 'Pending Users';
require_once '../../includes/header.php';
requireAdmin();

use Library\Library\Library;

$library = new Library();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $result = $library->approveUserRegistration($_POST['user_id']);
        $message = $result;
    } elseif (isset($_POST['reject'])) {
        $result = $library->rejectUserRegistration($_POST['user_id']);
        $message = $result;
    }
}

$pendingUsers = $library->getPendingRegistrations();

require_once '../../includes/nav-admin.php';
?>

<div class="container mt-4">
    <h2>Pending User Registrations</h2>
    
    <?php if ($message) { ?>
        <div class="alert alert-info mt-3"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>
    
    <div class="table-responsive mt-4">
        <?php if (empty($pendingUsers)) { ?>
            <p class="text-muted">No pending registrations</p>
        <?php } else { ?>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingUsers as $user) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>