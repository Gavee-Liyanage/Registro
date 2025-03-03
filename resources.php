<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="Style_resources.css">
    <title>View Users</title>
</head>
<body>
    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="admin.php"> Dashborad </a>
            <a href="about.php"> About </a>
        </div>
    
        <div class="right-nav">
                <a href='#' class="Registro"> Registro </a>
        </div>
    </div>
    
    <div class="container" id="con1">
        <h1 class="mt-4 text-center">View Users</h1>

        <div class="grid-container">
            <!-- Admin Search -->
            <div class="grid-item">
                <img src="image/admin-view.jpg" alt="">
                <h3>Admin</h3>
                <p>View admin records.</p>
                <a href="view_admin.php">
                    <button class="view-btn">View Admins</button>
                </a>
            </div>

            <!-- Panel Members Search -->
            <div class="grid-item">
                <img src="image/panel_view.jpeg" alt="">
                <h3>Panel Members</h3>
                <p>View panel member records.</p>
                <a href="view_panel.php">
                    <button class="view-btn">View Panel Members</button>
                </a>
            </div>

            <!-- Grama Niladhari Search -->
            <div class="grid-item">
                <img src="image/grama-view.jpg" alt="">
                <h3>Grama Niladhari</h3>
                <p>View Grama Niladhari records.</p>
                <a href="view_gramaNiladhari.php">
                    <button class="view-btn">View Grama Niladhari</button>
                </a>
            </div>
        </div>
    </div>

<!-- Bootstrap and JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>