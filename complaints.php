<?php
session_start();

$mysqli = require __DIR__ . "/database.php";

// Set the timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

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

// Fetch data for the clicked complaint ID from the URL parameter
if (isset($_GET["id"])) {
    $complaintId = $_GET["id"];

    // Fetch data for the provided complaint ID
    $sql = "SELECT * FROM $tableName WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaint = $result->fetch_assoc();

    // Close prepared statement
    $stmt->close();
} else {
    // Redirect if no complaint ID is provided
    header("Location: homepage.php");
    exit();
}

function getTableName($username) {
    if ($username == '1admin') {
        return 'domain1';
    } elseif ($username == '2admin') {
        return 'domain2';
    } elseif ($username == '3admin') {
        return 'domain3';
    } elseif ($username == '4admin') {
        return 'domain4';
    } elseif ($username == 'admin') {
        return 'complaintreg';
    } else {
        return 'complaintreg';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="index1.css">
    <style>
        /* Adjust the width of form cells */
        td form {
            max-width: 500px; /* Adjust the width as needed */
        }
    </style>
    
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
        <div class="center"></div>
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
    <!-- Display details of the clicked complaint ID -->
    <section class="complaint-section">
        <?php if (isset($complaint)): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <td><?= htmlspecialchars($complaint["id"]) ?></td>
                </tr>
                <tr>
                    <th>NAME</th>
                    <td><?= htmlspecialchars($complaint["name"]) ?></td>
                </tr>
                <tr>
                    <th>DEPARTMENT</th>
                    <td><?= htmlspecialchars($complaint["dept"]) ?></td>
                </tr>
                <tr>
                    <th>COMPLAINT</th>
                    <td><?= htmlspecialchars($complaint["descri"]) ?></td>
                </tr>
                <tr>
                    <th>STATUS</th>
                    <td><?= htmlspecialchars($complaint["status"]) ?></td>
                </tr>
                <tr>
                    <th>ACTION</th>
                    <td>
                        <!-- Create a form with a dropdown to update status -->
                        <form method="post" action="">
                            <input type="hidden" name="complaint_id" value="<?= $complaint["id"] ?>">
                            <select name="new_status">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                            <input type="submit" name="update_status" value="Update">
                        </form>
                    </td>
                </tr>
                <tr>
                    <th>ENTER COMMENTS</th>
                    <td>
                        <!-- Add a comment text box -->
                        <form method="post" action="">
                            <input type="hidden" name="complaint_id" value="<?= $complaint["id"] ?>">
                            <textarea name="comments" rows="2" cols="20" placeholder="Add comments..."></textarea>
                            <input type="submit" name="update_comments" value="Add Comment">
                        </form>
                    </td>
                </tr>
                <tr>
                    <th>COMMENTS</th>
                    <td>
                        <?php
                        // Explode comments by semicolon
                        $commentsArray = explode(';', $complaint["comments"]);

                        // Display each comment on a new line with timestamp
                        foreach ($commentsArray as $comment) {
                            // Remove leading and trailing whitespaces and print the comment with timestamp
                            echo nl2br(htmlspecialchars(trim($comment))) . "<br>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>DOMAIN COMMENTS</th>
                    <td>
                        <?php
                        // Explode domain comments by semicolon
                        $domainCommentsArray = explode(';', $complaint["domaincomments"]);

                        // Display each domain comment on a new line with timestamp
                        foreach ($domainCommentsArray as $domainComment) {
                            // Remove leading and trailing whitespaces and print the domain comment with timestamp
                            echo nl2br(htmlspecialchars(trim($domainComment))) . "<br>";
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <p><a href="display.php">Back</a></p>
        <?php endif; ?>
    </section>
<?php else: ?>
    <!-- Content for users who are not logged in -->
    <section class="login-section">
    </section>
<?php endif; ?>
</body>
</html>

