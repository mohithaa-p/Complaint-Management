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
            width:100%;
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

        input, select {
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
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Adjust as needed to center vertically */
        }
.button {
    background-color: initial;
            background-image: linear-gradient(#464d55, #25292e);
            border-radius: 8px;
            border-width: 0;
            margin: 16px; 
            box-shadow: 0 10px 20px rgba(0, 0, 0, .1), 0 3px 6px rgba(0, 0, 0, .05);
            box-sizing: border-box;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            flex-direction: column;
            font-family: expo-brand-demi, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 18px;
            height: 52px;
            justify-content: center;
            line-height: 1;
            outline: none;
            overflow: hidden;
            padding: 0 32px;
            text-align: center;
            text-decoration: none;
            transform: translate3d(0, 0, 0);
            transition: all 150ms;
            vertical-align: middle;
            white-space: nowrap;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
}

.button:hover {
  box-shadow: rgba(0, 1, 0, .2) 0 2px 8px;
  opacity: .85;
}

.button:active {
  outline: 0;
}

.button:focus {
  box-shadow: rgba(0, 0, 0, .5) 0 0 0 3px;
}


@media (max-width: 420px) {
  .button {
    height: 48px;
  }
}
    </style>
</head>
<body>
    <header>
        <h1>
        <img src="logo1 (1).png" alt="PSGiTech Logo">
            PSGiTech &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<span style="font-size:20px;">COMPLAINT MANAGEMENT SYSTEM</span>
        </h1>
        
    </header>
    

    <div class="button-container">
    <a class="button" href="userlogin.php">User Login</a><br><br>
    <a class="button" href="adminmain.php">Admin Login</a><br><br>
    <a class="button" href="admin.php">Domain Admin Login</a><br><br><br>
</div>
    
</body>
</html>
