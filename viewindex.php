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
    1 => 'technical',
    2 => 'administrative',
    3 => 'civil',
    4 => 'electrical'
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
    <!-- Rest of your content for logged-in users -->
    <section class="complaint-section">
        <table border="1">
            <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>DEPARTMENT</th>
                <th>COMPLAINT</th>
                <th>STATUS</th>
                <th>DOMAIN</th> <!-- Added Domain column -->
                <th>COMMENTS</th>
                <th>ENTER DOMAIN COMMENTS</th> <!-- Added Update Comments column -->
                <th>DOMAIN COMMENTS</th> <!-- Added Domain Comments column -->
            </tr>
            <?php
            // Fetch data from the complaintreg table
            $sql = "SELECT * FROM complaintreg";
            $result = $mysqli->query($sql);
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["id"]) ?></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["dept"]) ?></td>
                    <td><?= htmlspecialchars($row["descri"]) ?></td>
                    <td><?= htmlspecialchars($row["status"]) ?></td>
                    <td>
                        <!-- Create a form with a dropdown to update domain -->
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
                        </form>
                    </td>
                    <td>
                        <!-- Display existing comments -->
                        <?php
                        // Explode comments by semicolon and display each comment on a new line
                        $commentsArray = explode(';', $row['comments']);
                        foreach ($commentsArray as $comment) {
                            echo htmlspecialchars(trim($comment)) . "<br>";
                        }
                        ?>
                    </td>
                    <td>
                        <!-- Form for updating comments -->
                        <form method="post" action="">
                            <input type="hidden" name="complaint_id" value="<?= $row["id"] ?>">
                            <textarea name="new_comment" rows="4" cols="40" placeholder="Enter new comment"></textarea>
                            <br>
                            <input type="submit" name="update_comments" value="Update Comments">
                        </form>
                    </td>
                    <td>
                        <!-- Display Domain Comments -->
                        <?php
                        // Explode domain comments by newline
                        $domainCommentsArray = explode("\n", $row["domaincomments"]);

                        // Display each comment on a new line
                        foreach ($domainCommentsArray as $comment) {
                            // Remove leading and trailing whitespaces and print the comment
                            echo nl2br(htmlspecialchars(trim($comment))) . "<br>";
                        }
                        ?>
                    </td>
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

