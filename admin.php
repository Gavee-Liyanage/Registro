<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin Page </title>
    <link rel="stylesheet" href="Style_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body id="b2">
    <div class="navbar">
        <div class="left-nav">
            <a href="index.php"> Home </a>
            <a href="about.php"> About </a>
            <a href="contact.php"> Contact </a>
            <button id="darkModeToggle" class="toggle-btn" >
                <i class="fa-solid fa-circle-half-stroke fa-lg" ></i>
            </button>
        </div>
    
        <div class="right-nav">
            <a href='#' class="Registro"> Registro </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content" id="con1">
        <h1>Welcome  To  Registro </h1>
        
        <div class="grid-container">

            <div class="grid-item">
                <img src="image/search_1.jpg">
                <h3>Search</h3>
                <p> Search the Infomation </p>
                <button class="view-btn" onclick="redirectTo('search.php')">View</button>
            </div>
           
            <div class="grid-item">
                <img src="image/admin-view.jpg">
                <h3>Resources</h3>
                <p>View Admin's, Grama Niladhari's & panel member's information</p>
                <button class="view-btn" onclick="redirectTo('resources.php')">View</button>
            </div>

            <div class="grid-item">
                <img src="image/personal_3.jpg">
                <h3>My Personal Info</h3>
                <p>View your personal information.</p>
                <button class="view-btn" onclick="redirectTo('admin_profile.php')">View</button>
            </div>

        </div>
    </div>

    <script>
        // Function to redirect to a given URL
        function redirectTo(url) {
            window.location.href = url;
        }

        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        const navbar = document.querySelector('.navbar');
        const navbarLinks = document.querySelectorAll('.navbar a');
        const gridItems = document.querySelectorAll('.grid-item'); 

        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            navbar.classList.toggle('dark-mode');
            navbarLinks.forEach(link => link.classList.toggle('dark-mode'));
            darkModeToggle.classList.toggle('dark-mode');
            gridItems.forEach(item => item.classList.toggle('dark-mode'));
        });
    </script>

</body>
</html>