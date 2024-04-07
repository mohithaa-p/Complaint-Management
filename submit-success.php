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
        <a href="temp.php">Back</a> 
    </nav>
        
    </header>
<body>
  <div class="container">
    <!-- Your container content goes here -->
  </div>
  <br><br><br><br><br><br><br><br><br>
  <div class="box">
    <h1>Complaint Registered Successfully</h1>
    <!-- Display the Unique ID here -->
    <p> Unique ID: <?php echo isset($_GET['unique_id']) ? $_GET['unique_id'] : 'N/A'; ?></p>
  </div>
  
</body>
</html>
