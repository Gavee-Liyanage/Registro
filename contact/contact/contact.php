<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Higher Education Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Navbar styles */
        .navbar {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background for navbar */
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            padding: 10px;
        }

        .navbar .left-nav a, .navbar .dropdown .dropbtn {
            color: #333;
            text-decoration: none;
            padding: 14px 16px;
            display: inline-block;
        }

        .navbar a:hover, .dropdown:hover .dropbtn {
            background-color: #94c8f2;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown .dropbtn {
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0; /* Align dropdown menu to the right */
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #d7ecfc;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Apply background image to the main content */
        .main-content {
            background-image: url('contact.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            padding: 50px;
            min-height: 100vh; /* Make sure it covers the viewport height */
        }

        .header {
            text-align: center;
            color: #333;
            padding: 50px 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background for header */
            margin: 20px;
            border-radius: 10px;
        }

        .header h1 {
            margin-bottom: 20px;
        }

        .header p {
            font-size: 16px;
            line-height: 1.5;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Contact Info Styles */
        .contact-info {
            font-size: 26px;
            color: #333;
            margin-top: 20px;
        }

        .contact-info p {
            margin: 20px 0;
            display: flex;
            align-items: center;
        }

        .contact-info i {
            margin-right: 20px;
            color: #4f555f;
        }

        /* Map Styles */
        .map-container {
            margin: 30px auto;
            max-width: 800px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .map-container iframe {
            width: 100%;
            height: 400px;
            border: 0;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="left-nav">
            <a href="#">Home</a>
            <a href="#">About</a>
            <div class="dropdown">
                <button class="dropbtn" aria-label="Users dropdown">Users <i class="fa-solid fa-angle-down" style="color: #4f555f;"></i></button>
                <div class="dropdown-content">
                    <a href="#">Admin</a>
                    <a href="#">Grama Niladhari</a>
                    <a href="#">Panel Members</a>
                    <a href="#">Applicant</a>
                </div>
            </div>
            <a href="contact.php">Contact</a>
        </div>
        <div class="right-nav">
            <a href="#" class="Registro">Registro</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Contact Us</h1>
            <p>If you have any questions or need further information,
                please feel free to reach out to us.
                Our team is here to assist you with any inquiries you may have regarding our services or your application process. We look forward to hearing from you!</p><br><br>

            <!-- Contact Information -->
            <div class="contact-info">
                <p><i class="fa-solid fa-location-dot"></i> Address: NIBM Galle</p>
                <p><i class="fa-solid fa-phone"></i> Telephone: (123) 456-7890</p>
                <p><i class="fa-solid fa-envelope"></i> Email: registro@gmail.com</p>
                <p><i class="fa-solid fa-fax"></i> Fax: (123) 456-7891</p>
            </div>

            <!-- Google Map Embed -->
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12267.160379682945!2d80.22087691813654!3d6.0278354015295355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2595185a89b4b%3A0xd139a4e0d146ae60!2sNIBM%20Galle!5e0!3m2!1sen!2slk!4v1695801123585!5m2!1sen!2slk" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</body>
</html>
