<?php
// Database connection
$host = 'localhost:3306';
$username = 'dbgavee';
$password = '1234';
$database = 'registrodb';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search query
$filtervalues = '';
$result = null;
if (isset($_GET['search'])) {
    $filtervalues = $_GET['search'];
    $query = "SELECT * FROM grama_niladhari WHERE CONCAT(gn_id, gn_name, gn_area, gn_username) LIKE '%$filtervalues%'";
    $result = $conn->query($query);
}

$conn->close();
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grama Niladhari Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="Style_viewPages.css">
</head>
<body>
    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="admin.php"> Dashboard </a>
            <a href="contact.php"> Contact </a>
        </div>
        <div class="right-nav">
            <a class="Registro"> Registro </a>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="h1 mb-4">Search Grama Niladhari Data</h2>
        <form action="" method="GET" class="mb-4">
            <div class="input-group d-flex align-items-center">
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" id="searchInput" value="<?php echo htmlspecialchars($filtervalues); ?>" class="form-control" placeholder="Search Data">
                    <i class="fa-regular fa-circle-xmark clear-icon" id="clearIcon"></i>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Guardian Data Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>GN ID</th>
                    <th>Name</th>
                    <th>Area</th>
                    <th>Username</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['gn_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['gn_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gn_area']); ?></td>
                            <td><?php echo htmlspecialchars($row['gn_username']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No Record Found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        // Show or hide the clear icon based on input field value
        const searchInput = document.getElementById('searchInput');
        const clearIcon = document.getElementById('clearIcon');

        const updateClearIconVisibility = () => {
            if (searchInput && clearIcon) {
                clearIcon.style.display = searchInput.value.trim() ? 'inline' : 'none';
            }
        };

        // Initial update of the clear icon visibility
        updateClearIconVisibility();

        // Update clear icon visibility on input event
        searchInput.addEventListener('input', updateClearIconVisibility);

        // Clear the search input when the clear icon is clicked
        clearIcon.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = ''; // Clear the input field
                searchInput.focus();   // Set focus back to the input

                // Remove the search parameter from the URL
                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                window.history.pushState({}, document.title, url.toString());
            }
            updateClearIconVisibility(); // Hide the clear icon
        });

        // Clear the search input only when the page is refreshed
        window.onload = function() {
            const isPageRefreshed = !window.performance || window.performance.navigation.type === 1;
            if (isPageRefreshed && searchInput) {
                searchInput.value = ''; // Clear the input field
                updateClearIconVisibility(); // Hide the clear icon
            }
        };
    </script>
</body>
</html>
