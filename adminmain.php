<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $mysqli = require __DIR__ . "/database.php";
    
    $sql = sprintf("SELECT * FROM adminmain
                    WHERE username = '%s'",
                   $mysqli->real_escape_string($_POST["userid"]));
    
    $result = $mysqli->query($sql);
    
    $user = $result->fetch_assoc();
    if ($user) {
        
      if ($_POST["password"]== $user["password"]) {
          
        session_start();
            
        session_regenerate_id();
        
        $_SESSION["user_id"] = $user["id"];
        
        header("Location: temporary.php");
        exit;
      }
  }
  
  $is_invalid = true;
} 
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
        <nav>
        <a href="homepage.php">Home</a> 
    </nav>
    </header>
<head>
  <meta charset="UTF-8">
  <title> Admin login</title>
  <link rel="stylesheet" href="newhomepage.css">
  
</head>
<body>
 <br><br><br><br>
<div class="box">
  <h1><span style="font-weight:lighter;text-align:center;">&nbsp &nbsp &nbsp &nbsp &nbsp  Admin Login</h1>
 

  <?php if ($is_invalid): ?>
        <em>Invalid login</em>
    <?php endif; ?>

  
  <form method="post">
    <label for="userid">Username:</label>
    <input type="text" id="userid" name="userid"><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required value="<?= htmlspecialchars($_POST["userid"] ?? "") ?>"><br><br>

    <input type="submit" value="Login">
  </form>
</div>

</body>
</html>