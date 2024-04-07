<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: userlogin.php");
    exit();
}

// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "login";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the name of the logged-in user from the database
$username = $_SESSION['username'];
$sql = "SELECT name FROM user WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
} else {
    // Handle the case where the user's name is not found
    echo "Error: User name not found.";
    exit();
}

// Retrieve logged-in user's complaints using the fetched name
$sql = "SELECT id, descri, status, comments FROM complaintreg WHERE name = '$name'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Status</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
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


        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .right a {
            color: #fff;
            text-decoration: none;
            margin-left: 10px;
        }

        .complaint-section {
            margin: 20px;
        }

        table {
            width: 98%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        h2 {
            text-align: center; /* Align the text to the center */
            margin-top: 0px; /* Add some space between the heading and the top */
        }

        form {
            margin: 0;
        }

        textarea {
            width: 80%;
            padding: 8px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #7f8480;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0b0c0b;
        }

        .login-section {
            margin: 20px;
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
<h2><br>Complaint Status</h2>

<?php
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Description</th><th>Status</th><th>Comments</th></tr>";
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["descri"] . "</td>";
        echo "<td>" . $row["status"] . "</td>";
        echo "<td>";
        // Explode comments by semicolon and display each comment on a new line
        $commentsArray = explode(';', $row['comments']);
        foreach ($commentsArray as $comment) {
            echo htmlspecialchars(trim($comment)) . "<br>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No complaints found for the logged-in user.";
}

$conn->close();
?>

</body>
</html>
