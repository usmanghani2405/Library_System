<?php
$pageTitle = 'Pending Requests';
require_once '../../includes/header.php';
requireAdmin();

use Library\Library\Library;

$library = new Library();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $result = $library->approveRequest($_POST['request_id']);
        $message = $result;
    } elseif (isset($_POST['reject'])) {
        $result = $library->rejectRequest($_POST['request_id']);
        $message = $result;
    }
}

$pendingRequests = $library->getPendingRequests();

require_once '../../includes/nav-admin.php';
?>

<div class="container mt-4">
    <h2>Pending Book Requests</h2>
    
    <?php if ($message) { ?>
        <div class="alert alert-info mt-3"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>
    
    <div class="table-responsive mt-4">
        <?php if (empty($pendingRequests)) { ?>
            <p class="text-muted">No pending requests</p>
        <?php } else { ?>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingRequests as $request) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['email']); ?></td>
                            <td><?php echo htmlspecialchars($request['title']); ?></td>
                            <td><?php echo htmlspecialchars($request['author']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
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