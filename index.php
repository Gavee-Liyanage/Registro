<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Home Page</title>
    <link rel="stylesheet" href="Style_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body >

    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="about.php"> About </a>

            <div class="dropdown">
                <button class="dropbtn"> Users  <i class="fa-solid fa-angle-down" style="color: #4f555f;"></i> </button>
                <div class="dropdown-content">
                    <a href="admin_Login.php"> Admin </a>
                    <a href="guardian_HomePage.php"> Applicant</a>
                    <a href="GN_login.php"> Grama Niladhari </a>
                    <a href="panel_login.php"> Panel Members </a>
                </div>
            </div>

             <a href="contact.php"> Contact </a>
        </div>

        <div class="right-nav">
                <a href="login.php" class="login-btn">Login</a>
                <a class="Registro"> Registro </a>
                
        </div>
    </div>

    
    <div class="header">
        <h1>Welcome To Registro Government School Admission System </h1>
        <p>Register to government schools online.</p>

        <div class="applyBtn">
            <a href="guardian_HomePage.php"> Apply Now </a>
        </div>
    </div>

</body>
</html>