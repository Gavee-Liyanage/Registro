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

// Fetch child data along with approve_status from the applicant table
$sql = "
    SELECT 
        c.child_id, 
        c.f_name, 
        c.l_name, 
        c.initial_name, 
        c.gender, 
        c.dob, 
        c.country, 
        COALESCE(a.approval_status, 0) AS approval_status
    FROM 
        child c
    LEFT JOIN 
        applicant a ON c.child_id = a.applicant_id
";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Member Page</title>
    <link rel="stylesheet" href="Style_panel.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body id="b2">
    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="about.php"> About </a>
            <a href="contact.php"> Contact </a>
            <a href="panel_logout.php"> Logout </a>
        </div>
        <div class="right-nav">
            <a class="Registro"> Registro </a>
        </div>
    </div>



    <!-- Grama Niladhari Filter -->
    <div class="container mt-5">
        <form method="GET" action="">
            <label for="school_id">Filter by School:</label>
            <select name="school_id" id="school_id" class="form-control">
                <option value="">--Select School--</option>
                <?php while ($sclRow = $sclResult->fetch_assoc()): ?>
                    <option value="<?php echo $sclRow['school_id']; ?>" 
                        <?php echo (isset($_GET['school_id']) && $_GET['school_id'] == $sclRow['school_id']) ? 'selected' : ''; ?>>
                        <?php echo $sclRow['School_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary mt-3">Filter</button>
        </form>
    </div>



    <div class="container mt-5">
        <table class="table table-bordered" id="childTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Gender</th>
                    <th>Form Document</th>
                    <th>Birth Certificate Document</th>
                    <th>Grama Niladhari Status</th>
                    <th>School Selected Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['child_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['initial_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><a href="generate_pdf.php?child_id=<?php echo htmlspecialchars($row['child_id']); ?>">View Document</a></td>
                            <td><a href="view_birthcertificate.php?child_id=<?php echo htmlspecialchars($row['child_id']); ?>">View Files</a></td>
                            <td>
                                <?php echo $row['approval_status'] == 1 ? 'Approved' : 'Pending'; ?>
                            </td>
                            <td><a href="#"> School Selected Status</a></td>
                            <td><input type="checkbox" class="document-check" data-child-id="<?php echo htmlspecialchars($row['child_id']); ?>"></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Modal data fetching using jQuery (Optional)
        $('.view-details').click(function () {
            var id = $(this).data('id');
            $('#childDetails').text('Details for ID: ' + id); // You can enhance this logic to fetch details via AJAX.
        });
    </script>
</body>
</html>

