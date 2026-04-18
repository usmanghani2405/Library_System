<?php
$pageTitle = 'My Requests';
require_once '../../includes/header.php';
requireStudent();

use Library\Library\Library;

$library = new Library();

$message = '';

// Handle book return
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_book'])) {
    $result = $library->returnBook($_POST['request_id']);
    $message = $result;
}

$requests = $library->getUserRequest($_SESSION['user_id']);

require_once '../../includes/nav-student.php';
?>

<div class="container mt-4">
    <h2>My Book Requests</h2>
    
    <?php if ($message) { ?>
        <div class="alert alert-info mt-3"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>
    
    <div class="table-responsive mt-4">
        <?php if (empty($requests)) { ?>
            <p class="text-muted">You haven't requested any books yet.</p>
        <?php } else { ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['title']); ?></td>
                            <td><?php echo htmlspecialchars($request['author']); ?></td>
                            <td>
                                <?php
                                $badgeClass = match ($request['status']) {
                                    'pending' => 'bg-warning',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'returned' => 'bg-info',
                                    default => 'bg-secondary'
                                };
                        ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo ucfirst($request['status']); ?></span>
                            </td>
                            <td>
                                <?php if ($request['due_date']) { ?>
                                    <?php echo $request['due_date']; ?>
                                    <?php
                            $today = date('Y-m-d');
                                    if ($request['status'] === 'approved' && $request['due_date'] < $today) { ?>
                                        <span class="badge bg-danger ms-2">OVERDUE</span>
                                    <?php } ?>
                                <?php } else { ?>
                                    N/A
                                <?php } ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                            <td>
                                <?php if ($request['status'] === 'approved') { ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" name="return_book" class="btn btn-primary btn-sm">Return Book</button>
                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted">-</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>