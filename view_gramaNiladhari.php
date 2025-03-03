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

// Fetch Grama Niladhari data
$query = "SELECT * FROM grama_niladhari";
$result = $conn->query($query);

$conn->close();
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Grama Niladhari Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="Style_viewPages.css">
</head>
<body id="b2">
<div class="navbar">
    <div class="left-nav">
        <a href="index.php"> Home </a>
        <a href="admin.php"> Dashboard </a>
        <a href="contact.php"> Contact </a>
    </div>

    <div class="right-nav">
        <a href='#' class="Registro"> Registro </a>
    </div>
</div>

<!-- Grama Niladhari Data Table -->
<div class="container mt-5">
    <h2 class="h1 mb-4">View Grama Niladhari Data</h2>

    <!-- Add Grama Niladhari Button -->
    <div class="d-flex justify-content-end mb-3">
        <a href="add_gramaniladhari.php" class="btn btn-success btn-lg">
            <i class="fas fa-user-plus"></i> Add Grama Niladhari
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>GN ID</th>
                <th>Name</th>
                <th>Area</th>
                <th>Username</th>
                <th>Action</th>
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
                        <td>
                            <a href="update_gramaa.php?id=<?php echo $row['gn_id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Update
                            </a>
                            <a href="delete_gramaa.php?id=<?php echo $row['gn_id']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this record?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No Record Found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
