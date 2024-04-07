<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSGiTech Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('back1.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            font-size: 20px;
        }

        header img {
            width: 40px;
            margin-right: 10px;
        }

        nav {
            display: flex;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .box {
            width: 370px; 
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, select, textarea {
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>
            <img src="logo1 (1).png" alt="PSGiTech Logo">
            PSGiTech &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<span style="font-size:20px;">COMPLAINT MANAGEMENT SYSTEM</span>
        </h1>
        <div class="navbar">
            <div class="center">
            </div>
            <div class="right">
                <?php if (isset($_SESSION["username"])): ?>
                    <?php
                        $mysqli = require __DIR__ . "/database.php";
                        $username = $_SESSION["username"];
                        $sql = "SELECT name FROM user WHERE username = '$username'";
                        $result = $mysqli->query($sql);
                        $user = $result->fetch_assoc();
                    ?>
                    <h1>Welcome, <?= htmlspecialchars($user["name"]) ?></h1>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <h1>Logged out successfully</h1>
                    <a href="userlogin.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
        <nav>
            <a href="status.php">View Status</a>
        </nav>
        
    </header>

    <div class="box">
        <h1><span style="font-weight:lighter;text-align:center;">&nbsp  Complaint Registration</h1>
        <form action="process-form.php" method="post">
            <!-- Add hidden input fields for name and department -->
            <?php if (isset($_SESSION["username"])): ?>
                <?php
                    $mysqli = require __DIR__ . "/database.php";
                    $username = $_SESSION["username"];
                    $sql = "SELECT name, department FROM user WHERE username = '$username'";
                    $result = $mysqli->query($sql);
                    $user = $result->fetch_assoc();
                ?>
                <!-- Add hidden input fields for name and department -->
                <input type="hidden" name="name" value="<?= htmlspecialchars($user["name"]) ?>">
                <input type="hidden" name="department" value="<?= htmlspecialchars($user["department"]) ?>">
            <?php endif; ?>

            <label for="dropdown">Complaint Domain:</label>
               <select name="complaint_domain" id="dropdown">
                     <option value="1">Technical</option>
                     <option value="2">Administrative</option>
                     <option value="3">Civil</option>
                     <option value="4">Electrical</option>
    <!-- Add more options as needed -->
              </select> 

            <label for="complaint">Complaint Description:</label>
            <textarea name="complaint_description" id="complaint" rows="2" cols="50"></textarea>

            <input type="submit" value="Submit">
        </form>
    </div>
   
   
   

    
</body>
</html>
