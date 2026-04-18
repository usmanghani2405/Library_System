<?php
$pageTitle = 'Return Books';
require_once '../../includes/header.php';
requireAdmin();

use Library\Library\Library;

$library = new Library();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book'])) {
    $result = $library->returnBook($_POST['request_id']);
    $message = $result;
}

$approvedRequests = $library->getApprovedRequests();

require_once '../../includes/nav-admin.php';
?>

<div class="container mt-4">
    <h2>Return Books</h2>
    
    <?php if ($message) { ?>
        <div class="alert alert-info mt-3"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>
    
    <div class="table-responsive mt-4">
        <?php if (empty($approvedRequests)) { ?>
            <p class="text-muted">No books currently borrowed</p>
        <?php } else { ?>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Book Title</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approvedRequests as $request) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['email']); ?></td>
                            <td><?php echo htmlspecialchars($request['title']); ?></td>
                            <td><?php echo $request['due_date']; ?></td>
                            <td>
                                <?php
                                $today = date('Y-m-d');
                        $isOverdue = $request['due_date'] < $today;
                        ?>
                                <span class="badge <?php echo $isOverdue ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo $isOverdue ? 'OVERDUE' : 'Active'; ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="return_book" class="btn btn-primary btn-sm">Mark as Returned</button>
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