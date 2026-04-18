<?php
$pageTitle = 'Student Dashboard';
require_once '../../includes/header.php';
requireStudent();

use Library\Library\Library;

$library = new Library();

$message = '';
$error = '';
$searchKeyword = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $result = $library->requestBook($_SESSION['user_id'], $_POST['book_id']);
    if (strpos($result, 'successfully') !== false) {
        $message = $result;
    } else {
        $error = $result;
    }
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchKeyword = trim($_GET['search']);
    $books = $library->searchBooks($searchKeyword);
} else {
    $books = $library->getAllBooks();
}

require_once '../../includes/nav-student.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Available Books</h2>
        
        <!-- Search Form -->
        <form method="GET" class="d-flex" style="width: 400px;">
            <input type="text" name="search" class="form-control me-2" 
                   placeholder="Search by title or author..." 
                   value="<?php echo htmlspecialchars($searchKeyword); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($searchKeyword) { ?>
                <a href="dashboard.php" class="btn btn-secondary ms-2">Clear</a>
            <?php } ?>
        </form>
    </div>
    
    <?php if ($message) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>
    
    <?php if ($error) { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>
    
    <?php if ($searchKeyword) { ?>
        <div class="alert alert-info">
            Search results for: <strong><?php echo htmlspecialchars($searchKeyword); ?></strong>
        </div>
    <?php } ?>
    
    <div class="row mt-4">
        <?php if (empty($books)) { ?>
            <p class="text-muted">
                <?php echo $searchKeyword ? 'No books found matching your search.' : 'No books available at the moment.'; ?>
            </p>
        <?php } else { ?>
            <?php foreach ($books as $book) { ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                            <p class="card-text">
                                <strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($book['description']); ?></small>
                            </p>
                            <?php if (!$book['available']) { ?>
                                <span class="badge bg-danger">Currently Unavailable</span>
                            <?php } ?>
                        </div>
                        <div class="card-footer">
                            <?php if ($book['available']) { ?>
                                <form method="POST">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Request Book</button>
                                </form>
                            <?php } else { ?>
                                <button class="btn btn-secondary btn-sm w-100" disabled>Not Available</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>