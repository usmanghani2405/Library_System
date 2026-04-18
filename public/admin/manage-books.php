<?php
$pageTitle = 'Manage Books';
require_once '../../includes/header.php';
requireAdmin();

use Library\Library\Library;

$library = new Library();

$message = '';
$editBook = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_book'])) {
        $result = $library->addBook($_POST['title'], $_POST['author'], $_POST['description']);
        $message = $result;
    } elseif (isset($_POST['edit_book'])) {
        $result = $library->editBook($_POST['book_id'], $_POST['title'], $_POST['author'], $_POST['description'], $_POST['available']);
        $message = $result;
    } elseif (isset($_POST['delete_book'])) {
        $result = $library->deleteBook($_POST['book_id']);
        $message = $result;
    }
}

// Get book to edit
if (isset($_GET['edit'])) {
    $editBook = $library->getBookById($_GET['edit']);
}

$books = $library->getAllBooks();

require_once '../../includes/nav-admin.php';
?>

<div class="container mt-4">
    <h2>Manage Books</h2>
    
    <?php if ($message) { ?>
        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>
    
    <!-- Add/Edit Book Form -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5><?php echo $editBook ? 'Edit Book' : 'Add New Book'; ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php if ($editBook) { ?>
                    <input type="hidden" name="book_id" value="<?php echo $editBook['id']; ?>">
                <?php } ?>
                
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="title" class="form-control" placeholder="Title" 
                               value="<?php echo $editBook['title'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="author" class="form-control" placeholder="Author" 
                               value="<?php echo $editBook['author'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="description" class="form-control" placeholder="Description" 
                               value="<?php echo $editBook['description'] ?? ''; ?>">
                    </div>
                    
                    <?php if ($editBook) { ?>
                        <div class="col-md-2">
                            <select name="available" class="form-control" required>
                                <option value="1" <?php echo $editBook['available'] == 1 ? 'selected' : ''; ?>>Available</option>
                                <option value="0" <?php echo $editBook['available'] == 0 ? 'selected' : ''; ?>>Not Available</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="edit_book" class="btn btn-warning w-100">Update</button>
                            <a href="manage-books.php" class="btn btn-secondary w-100 mt-1">Cancel</a>
                        </div>
                    <?php } else { ?>
                        <div class="col-md-4">
                            <button type="submit" name="add_book" class="btn btn-success w-100">Add Book</button>
                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Books List -->
    <div class="table-responsive mt-4">
        <h4>All Books</h4>
        <?php if (empty($books)) { ?>
            <p class="text-muted">No books in the library</p>
        <?php } else { ?>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['description']); ?></td>
                            <td>
                                <span class="badge <?php echo $book['available'] ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $book['available'] ? 'Available' : 'Borrowed'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this book?');">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" name="delete_book" class="btn btn-danger btn-sm">Delete</button>
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