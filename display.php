<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$mysqli = new mysqli("localhost", "root", "", "login");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set the timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

// Function to get table name based on username
function getTableName($username) {
    switch ($username) {
        case '1admin':
            return 'domain1';
        case '2admin':
            return 'domain2';
        case '3admin':
            return 'domain3';
        case '4admin':
            return 'domain4';
        default:
            return 'complaintreg';
    }
}

if (isset($_SESSION["user_id"])) {
    $sql = "SELECT * FROM adminlogin WHERE id = {$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
    $tableName = isset($user["username"]) ? getTableName($user["username"]) : 'complaintreg';
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_status"])) {
        $complaintId = $_POST["complaint_id"];
        $newStatus = $_POST["new_status"]; // Get the selected status value from the form

        // Update the status and timestamp in the appropriate table based on username
        $updateStatusSql = "UPDATE $tableName SET status = CONCAT('$newStatus', ' - ', NOW()) WHERE id = $complaintId";
        $mysqli->query($updateStatusSql);

        // Update the status and timestamp in the complaintreg table
        $updateStatusSql1 = "UPDATE complaintreg SET status = CONCAT('$newStatus', ' - ', NOW()) WHERE id = $complaintId";
        $mysqli->query($updateStatusSql1);
    } elseif (isset($_POST["update_comments"])) {
        $complaintId = $_POST["complaint_id"];
        $newComments = $_POST["comments"];
        $newCommentsWithTime = $newComments . " - " . date("Y-m-d H:i:s"); // Append current time to the new comments

        // Update the comments in the complaintreg table
        $updateCommentsSql = "UPDATE complaintreg SET comments = CONCAT_WS(';', comments, ?) WHERE id = ?";
        $stmt = $mysqli->prepare($updateCommentsSql);
        $stmt->bind_param("si", $newCommentsWithTime, $complaintId);
        $stmt->execute();
        $stmt->close();

        // Update the comments in the respective domain's table
        updateDomainComments($tableName, $complaintId, $newCommentsWithTime);
    }
}

// Function to update comments in the respective domain's table
function updateDomainComments($tableName, $complaintId, $newComments) {
    global $mysqli;

    // Extract the domain number from the table name
    $domainNumber = intval(substr($tableName, -1));

    // Build the domain table name
    $domainTableName = 'domain' . $domainNumber;

    // Update the comments in the respective domain's table
    $updateDomainCommentsSql = "UPDATE $domainTableName SET comments = CONCAT_WS(';', comments, ?) WHERE id = ?";
    $stmt = $mysqli->prepare($updateDomainCommentsSql);
    $stmt->bind_param("si", $newComments, $complaintId);
    $stmt->execute();
    $stmt->close();
}

// Fetch data from the appropriate table based on username
$tableName = isset($user["username"]) ? getTableName($user["username"]) : 'complaintreg';
$sql = "SELECT * FROM $tableName";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="index1.css">
</head>
<body>
<header>
    <h1>
        <img src="logo1 (1).png" alt="PSGiTech Logo">
        <span style="font-size:20px;">PSGiTech &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
            &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
            <span style="font-size:20px;">COMPLAINT MANAGEMENT SYSTEM</span>
        </span>
    </h1>
    <div class="navbar">
        <div class="center">
        </div>
        <div class="right">
            <?php if (isset($user)): ?>
                <h1>Welcome, <?= isset($user) ? htmlspecialchars($user["name"]) : "Guest" ?></h1>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <h1>Logged out successfully</h1>
                <a href="admin.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <nav>
        <a href="homepage.php">Home</a> 
    </nav>
</header>

<?php if (isset($user)): ?>
    <!-- Rest of your content for logged-in users -->
    <section class="complaint-section">
        <table border="1">
            <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>DEPARTMENT</th>
                <th>STATUS</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><a href="complaints.php?id=<?= $row["id"] ?>"><?= htmlspecialchars($row["id"]) ?></a></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["dept"]) ?></td>
                    <td><?= htmlspecialchars($row["status"]) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </section>
<?php else: ?>
    <!-- Content for users who are not logged in -->
    <section class="login-section">
    </section>
<?php endif; ?>
</body>
</html>


