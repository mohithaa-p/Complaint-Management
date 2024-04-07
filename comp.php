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

// Fetch data from the appropriate table based on username
$tableName = isset($user["username"]) ? getTableName($user["username"]) : 'complaintreg';
$sql = "SELECT * FROM $tableName";
$result = $mysqli->query($sql);

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
    <style>
        /* Add your CSS styles here to match the design of index.css and index1.css provided */

        /* Example styles, adjust as needed */
        header {
            background-color: #f2f2f2;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin: 0;
        }

        .company-name {
            font-size: 20px;
        }

        .system-name {
            font-size: 20px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .navbar a {
            text-decoration: none;
            color: #333;
            margin-left: 10px;
        }

        .complaint-section {
            margin-top: 20px;
        }

        .login-section {
            margin-top: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 8px 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "login");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set the timezone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');

// Reverse mapping of numeric values to domain names
$domainReverseMapping = [
    1 => 'Technical',
    2 => 'Administrative',
    3 => 'Civil',
    4 => 'Electrical'
];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_status"])) {
        // Code for updating status remains unchanged
    } elseif (isset($_POST["update_domain"])) {
        $complaintId = $_POST["complaint_id"];
        $newDomain = $_POST["new_domain"]; // Get the new domain from the form

        // Fetch the current domain of the complaint
        $selectDomainSql = "SELECT domain FROM complaintreg WHERE id = ?";
        $stmt = $mysqli->prepare($selectDomainSql);
        $stmt->bind_param("i", $complaintId);
        $stmt->execute();
        $stmt->bind_result($currentDomain);
        $stmt->fetch();
        $stmt->close();

        // Move the complaint to the new domain
        moveComplaint($complaintId, $newDomain, $currentDomain, $mysqli);
    } elseif (isset($_POST["update_comments"])) {
        $complaintId = $_POST["complaint_id"];
        $newComment = $_POST["new_comment"]; // Get the new comment from the form
        $newCommentWithTime = $newComment . " - " . date("Y-m-d H:i:s"); // Append current time to the new comment

        // Update the domain comments in the complaintreg table
        $updateDomainCommentsSql = "UPDATE complaintreg SET domaincomments = CONCAT_WS('\n', domaincomments, ?) WHERE id = ?";
        $stmt = $mysqli->prepare($updateDomainCommentsSql);
        $stmt->bind_param("si", $newCommentWithTime, $complaintId);
        $stmt->execute();
        $stmt->close();

        // Fetch the current domain of the complaint
        $selectDomainSql = "SELECT domain FROM complaintreg WHERE id = ?";
        $stmt = $mysqli->prepare($selectDomainSql);
        $stmt->bind_param("i", $complaintId);
        $stmt->execute();
        $stmt->bind_result($currentDomain);
        $stmt->fetch();
        $stmt->close();

        // Get the name of the domain table
        $domainTableName = 'domain' . $currentDomain;

        // Update the domain comments in the respective domain table
        $updateDomainCommentsSql = "UPDATE $domainTableName SET domaincomments = CONCAT_WS('\n', domaincomments, ?) WHERE id = ?";
        $stmt = $mysqli->prepare($updateDomainCommentsSql);
        $stmt->bind_param("si", $newCommentWithTime, $complaintId);
        $stmt->execute();
        $stmt->close();
    }
}

// Function to move complaint to the appropriate domain table
function moveComplaint($complaintId, $newDomain, $currentDomain, $mysqli) {
    global $domainReverseMapping;

    // Update the domain column in the complaintreg table
    $updateDomainSql = "UPDATE complaintreg SET domain = ? WHERE id = ?";
    $stmt = $mysqli->prepare($updateDomainSql);
    $stmt->bind_param("ii", $newDomain, $complaintId);
    $stmt->execute();
    $stmt->close();

    // No need to generate a new ID, keep the existing ID
    // Fetch the complaint details
    $selectComplaintSql = "SELECT * FROM complaintreg WHERE id = ?";
    $stmt = $mysqli->prepare($selectComplaintSql);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $result = $stmt->get_result();
    $complaint = $result->fetch_assoc();
    $stmt->close();

    // Delete the complaint from the current domain table
    $currentDomainTableName = 'domain' . $currentDomain;
    $deleteComplaintSql = "DELETE FROM $currentDomainTableName WHERE id = ?";
    $stmt = $mysqli->prepare($deleteComplaintSql);
    $stmt->bind_param("i", $complaintId);
    $stmt->execute();
    $stmt->close();

    // Insert the complaint into the new domain table with the same ID
    $newDomainTableName = 'domain' . $newDomain;
    $insertComplaintSql = "INSERT INTO $newDomainTableName (id, name, dept, descri, status, comments, domaincomments) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($insertComplaintSql);
    $stmt->bind_param("issssss", $complaintId, $complaint["name"], $complaint["dept"], $complaint["descri"], 
                      $complaint["status"], $complaint["comments"], $complaint["domaincomments"]);
    $stmt->execute();
    $stmt->close();
}

?>
<header>
    <h1>
        <img src="logo1 (1).png" alt="PSGiTech Logo">
        <span class="company-name">PSGiTech</span>
        <span class="system-name">COMPLAINT MANAGEMENT SYSTEM</span>
    </h1>
    <div class="navbar">
        <div class="center">
        </div>
        <div class="right">
            <?php if (isset($_SESSION["user_id"])): ?>
                <h1>Welcome</h1>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <h1>Logged out successfully</h1>
                <a href="admin.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (isset($_SESSION["user_id"])): ?>
    <section class="complaint-section">
        <?php if (isset($_GET['view_id'])): ?>
            <!-- Detailed view for a single complaint -->
            <?php
            $complaintId = $_GET['view_id'];
            $sql = "SELECT * FROM complaintreg WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $complaintId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()): ?>
                <!-- Display the detailed information -->
                <table border="1">
                    <tr><th>ID</th><td><?= htmlspecialchars($row["id"]) ?></td></tr>
                    <tr><th>Name</th><td><?= htmlspecialchars($row["name"]) ?></td></tr>
                    <tr><th>Department</th><td><?= htmlspecialchars($row["dept"]) ?></td></tr>
                    <tr><th>Status</th><td><?= htmlspecialchars($row["status"]) ?></td></tr>
                    <tr><th>Domain</th><td> <!-- Create a form with a dropdown to update domain -->
                        <form method="post" action="">
                            <input type="hidden" name="complaint_id" value="<?= $row["id"] ?>">
                            <select name="new_domain">
                                <?php
                                // Fetch remaining domains from the database
                                $currentDomain = $row["domain"];
                                foreach ($domainReverseMapping as $numericDomain => $domain) {
                                    $selected = ($numericDomain == $currentDomain) ? "selected" : "";
                                    echo "<option value=\"$numericDomain\" $selected>$domain</option>";
                                }
                                ?>
                            </select>
                            <input type="submit" name="update_domain" value="Update">
                        </form></td></tr>
                    <tr><th>Description</th><td><?= htmlspecialchars($row["descri"]) ?></td></tr> <!-- Display the description -->
                    <tr><th>Comments</th><td><?= nl2br(htmlspecialchars($row["comments"])) ?></td></tr>
                    <tr><th>Enter Domain Comments</th><td><!-- Form for updating comments -->
                        <form method="post" action="">
                            <input type="hidden" name="complaint_id" value="<?= $row["id"] ?>">
                            <textarea name="new_comment" rows="4" cols="40" placeholder="Enter new comment"></textarea>
                            <br>
                            <input type="submit" name="update_comments" value="Update Comments">
                        </form></td></tr>
                    <tr><th>Domain Comments</th><td><?= nl2br(htmlspecialchars($row["domaincomments"])) ?></td></tr>
                </table>
                <!-- Link to go back to the list view -->
                <p><a href="temporary.php">Back</a></p>
            <?php endif; ?>
        <?php else: ?>
            <!-- List view -->
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Domain</th> <!-- Added Domain column -->
                    <th>Description</th> <!-- Added Description column -->
                </tr>
                <?php
                $sql = "SELECT id, name, dept, status, domain, descri FROM complaintreg"; // Include 'descri' column in the query
                $result = $mysqli->query($sql);
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><a href="?view_id=<?= $row["id"] ?>"><?= htmlspecialchars($row["id"]) ?></a></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= htmlspecialchars($row["dept"]) ?></td>
                        <td><?= htmlspecialchars($row["status"]) ?></td>
                        <td><?= htmlspecialchars($domainReverseMapping[$row["domain"]]) ?></td>
                        <td><?= htmlspecialchars($row["descri"]) ?></td> <!-- Display the description -->
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </section>
<?php else: ?>
    <!-- Content for users who are not logged in remains unchanged -->
<?php endif; ?>
</body>
</html>
