<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../admin/dashboard.php">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pending-users.php">Pending Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pending-requests.php">Pending Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="return-book.php">Return Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage-books.php">Manage Books</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="navbar-text me-3">Admin: <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>