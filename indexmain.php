<?php
session_start();

$mysqli = require __DIR__ . "/database.php";

if (isset($_SESSION["user_id"])) {
    $sql = "SELECT * FROM adminmain WHERE id = {$_SESSION["user_id"]}";
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
    $tableName = isset($user["username"]) ? getTableName($user["username"]) : 'complaintreg';
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_status"])) {
        $complaintId = $_POST["complaint_id"];
        $newStatus = $_POST["new_status"];

        // Update the status in the appropriate table based on username
        $updateSql = "UPDATE $tableName SET status = '$newStatus' WHERE id = $complaintId";
        $mysqli->query($updateSql);
    } elseif (isset($_POST["update_comments"])) {
        $complaintId = $_POST["complaint_id"];
        $newComments = $_POST["comments"];

        // Fetch the existing comments
        $selectCommentsSql = "SELECT comments FROM $tableName WHERE id = $complaintId";
        $selectCommentsResult = $mysqli->query($selectCommentsSql);
        $existingComments = $selectCommentsResult->fetch_assoc()["comments"];

        // Concatenate the existing comments with the new comments, separated by a semicolon
        $updatedComments = $existingComments . '; ' . $newComments;

        // Update the comments in the appropriate table based on username
        $updateCommentsSql = "UPDATE $tableName SET comments = '$updatedComments' WHERE id = $complaintId";
        $mysqli->query($updateCommentsSql);
        $updateCommentsSql1 = "UPDATE complaintreg SET comments = '$updatedComments' WHERE id = $complaintId";
        $mysqli->query($updateCommentsSql1);
    }
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
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="index1.css">
</head>
<body>
    <header>
        <h1>
            <img src="logo1 (1).png" alt="PSGiTech Logo" >
            <span style="font-size:20px;">PSGiTech &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp <span style="font-size:20px;">COMPLAINT MANAGEMENT SYSTEM</span>
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
    </header>

    <?php if (isset($user)): ?>
        <!-- Rest of your content for logged-in users -->
        <section class="complaint-section">
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>DEPARTMENT</th>
                    <th>COMPLAINT</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                    <th>UPDATE COMMENTS</th>
                    <th>COMMENTS</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= htmlspecialchars($row["dept"]) ?></td>
                        <td><?= htmlspecialchars($row["descri"]) ?></td>
                        <td><?= htmlspecialchars($row["status"]) ?></td>
                        <td>
                            <!-- Create a form with a dropdown to update status -->
                            <form method="post" action="">
                                <input type="hidden" name="complaint_id" value="<?= $row["id"] ?>">
                                <select name="new_status">
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                                <input type="submit" name="update_status" value="Update">
                            </form>
                        </td>
                        <td>
                            <!-- Add a comment text box -->
                            <form method="post" action="">
                                <input type="hidden" name="complaint_id" value="<?= $row["id"] ?>">
                                <textarea name="comments" rows="2" cols="20" placeholder="Add comments..."></textarea>
                                <input type="submit" name="update_comments" value="Add Comment">
                            </form>
                        </td>
                        <td>
                            <?php
                            // Explode comments by semicolon
                            $commentsArray = explode(';', $row["comments"]);

                            // Display each comment on a new line
                            foreach ($commentsArray as $comment) {
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
