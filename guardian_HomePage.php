<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Page</title>
    <link rel="stylesheet" href="Style_guardian.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body id="b1">
    <!-- Navbar -->
    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="about.php"> About </a>
            <div class="dropdown">
                <button class="dropbtn"> Admission <i class="fa-solid fa-angle-down" style="color: #4f555f;"></i> </button>
                <div class="dropdown-content">
                    <a href="guardian_login.php"> Guardian Form </a>
                    <a href="child.php"> Child Form </a>
                </div>
            </div>
            <a href="contact.php"> Contact </a>
            <button id="darkModeToggle" class="toggle-btn">
                <i class="fa-solid fa-circle-half-stroke fa-lg"></i>
            </button>
        </div>
        <div class="right-nav">
            <a href='#' class="Registro"> Registro </a>
        </div>
    </div>

    <!-- Animated Bubbles -->
    <div class="container">
        <div class="drop" style="--clr:#db4ef4;">
            <div class="content">
                <h2>01</h2>
                <h3>Fill The Guardian Form</h3>
                <p> First sign in to the Regirstro. Fill the Guardian Form</p>
                <a href="guardian_form2.php">Guardian Form</a>
            </div>
        </div>
        <div class="drop" style="--clr:#3a9cf1;">
            <div class="content">
                <h2>02</h2>
                <h3> Fill The Child Form</h3>
                <p>You will only have access to fill out this child form if you have filled out the guardian form.</p>
                <a href="child.php">Child Form</a>
            </div>
        </div>
    </div>
</body>
</html>
