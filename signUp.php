<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> User Login </title>
    <link rel="stylesheet" href="Style_signUp.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <div class="container">    

        <div class="left-section">            
            
            <h2>sign Up as</h2>
            <p>Select the user type you wish to login</p>

            <div class="grid-container">
                <!-- Admin Search -->
                <div class="grid-item">
                    <img src="image/applicant_login.png" alt="">
                    <h3>Applicant</h3>
                    <p>Sign up as Applicant</p>
                    <a href="guardian_Register.php">
                        <button class="login-btn">Sign Up</button>
                    </a>
                </div>

                <!-- Admin Search -->
                <div class="grid-item">
                    <img src="image/admin_login.png" alt="">
                    <h3>Admin</h3>
                    <p>Sign Up as Admin</p>
                    <a href="admin_Register.php">
                        <button class="login-btn">Sign Up</button>
                    </a>
                </div>

                <!-- Panel Members Search -->
                <div class="grid-item">
                    <img src="image/pm3_login.png" alt="">
                    <h3>Panel Member</h3>
                    <p>Sign Up as panel member</p>
                    <a href="panel_register.php">
                        <button class="login-btn">Sign Up</button>
                    </a>
                </div>

                <!-- Grama Niladhari Search -->
                <div class="grid-item">
                    <img src="image/gn_login.png" alt="">
                    <h3>Grama Niladhari</h3>
                    <p>Sign Up as Grama Niladhari</p>
                    <a href="grama_register.php">
                        <button class="login-btn">Sign Up</button>
                    </a>
                </div>

            </div>

        </div>
            <div class="right-section">
                <img src="image/login-02.png" alt="Illustration">
            </div>
        
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("fa-eye-slash");
        });
    </script>

</body>
</html>

            

            