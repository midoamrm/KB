<?php
session_start(); // Must start session

// Redirect if already logged in
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
    header("Location: main.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? "";
    $password = $_POST["password"] ?? "";

    // Dummy login credentials
    if ($name === "admin" && $password === "12345") {
        $_SESSION["logged_in"] = true;
        header("Location: main.php");
        exit;
    } else {
        $error = "Invalid name or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login Page</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #e6e6e6;
    }

    .container {
        display: flex;
        height: 100vh;
        justify-content: center;
        align-items: center;
        background: #e6e6e6;
    }

    .box {
        width: 85%;
        height: 75vh;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    /* LEFT BLUE PART */
    .left {
        width: 55%;
        padding: 60px;
        color: white;
        background: #1d3557;
        position: relative;
    }

    .left h1 {
        font-size: 35px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .left h3 {
        font-weight: 500;
        margin-bottom: 25px;
        letter-spacing: 1px;
    }

    .left p {
        max-width: 350px;
        line-height: 1.6;
        font-size: 15px;
        opacity: 0.9;
    }

    .circle1, .circle2 {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
    }
    .circle1 {
        width: 250px;
        height: 250px;
        bottom: -70px;
        left: -50px;
    }
    .circle2 {
        width: 180px;
        height: 180px;
        top: 40px;
        right: 40px;
    }

    /* RIGHT LOGIN CARD */
    .right {
        width: 45%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f9f9f9;
    }

    .card {
        width: 70%;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        text-align: left;
    }

    .card h2 {
        margin-bottom: 20px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .extra-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        margin-bottom: 20px;
    }

    button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 25px;
        font-size: 16px;
        cursor: pointer;
        background:#1d3557;
        color: white;
    }

    .signup {
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
    }

    .error {
        color: red;
        margin-bottom: 15px;
        font-size: 14px;
    }
</style>
</head>

<body>

<div class="container">
    <div class="box">

        <!-- LEFT BLUE SECTION -->
        <div class="left">
            <h1>Knowledge Base</h1>
            <h3>Welcome To Your Knowledge Base</h3>
            <p>
                Welcome to the Knowledge Base Portal.  
Our goal is to provide fast, reliable access to essential information, documentation, and best practices to empower your work and improve productivity.  
Use the search or browse categories to find step-by-step guides, troubleshooting articles, and helpful resources.

            </p>

            <div class="circle1"></div>
            <div class="circle2"></div>
        </div>

        <!-- RIGHT LOGIN SECTION -->
        <div class="right">
            <div class="card">
                <h2>Sign in</h2>

                <p style="font-size: 13px; margin-bottom: 20px;">
                    Please Enter Your Credentials
                </p>

                <!-- Show error message -->
                <?php if (!empty($error)) { ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php } ?>

                <!-- FORM -->
                <form method="POST">
                    <input type="text" name="name" placeholder="User Name">
                    <input type="password" name="password" placeholder="Password">

                    <div class="extra-row">
                        <label><input type="checkbox" name="remember"> Remember me</label>
                        <a href="#" style="text-decoration:none;">Forgot Password?</a>
                    </div>

                    <button type="submit" name="login">Sign in</button>
                </form>

                <div class="signup">Don't have an account? <a href="#">Sign Up</a></div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
