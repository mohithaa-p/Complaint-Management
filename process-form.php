<?php
session_start();

// Establish the database connection
$mysqli = require __DIR__ . "/database.php"; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    
    header("Location: userlogin.php");
    exit; 
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data including domain and complaint description
    $name = $_POST["name"]; // Assuming it's properly sanitized
    $department = $_POST["department"];
    $complaintDomain = $_POST["complaint_domain"]; // Assuming it's properly sanitized
    $complaintDescription = $_POST["complaint_description"]; // Assuming it's properly sanitized
    $status = "Requested"; // Assuming you have a default status

    // Fetch the current date from the dates table
    $fetchDateSql = "SELECT `date` FROM dates LIMIT 1";
    $result = $mysqli->query($fetchDateSql);

    if ($result && $result->num_rows > 0) {
        // Fetch the current date from the database
        $row = $result->fetch_assoc();
        $currentDateFromTable = $row['date'];

        // Extract the date parts
        $tableDate = substr($currentDateFromTable, 0, 8); // Extracts only the date part (ddmmyyyy)
        $lastThreeDigits = intval(substr($currentDateFromTable, -3)); // Extracts last 3 digits

        // Get the current date
        $currentDate = date("dmY");

        // Check if the current date matches the date in the table
        if ($currentDate == $tableDate) {
            // Increment the last 3 digits
            $lastThreeDigits++;

            // Pad the last 3 digits with leading zeros if necessary
            $paddedLastThreeDigits = str_pad($lastThreeDigits, 3, '0', STR_PAD_LEFT);

            // Construct the unique ID
            $uniqueId = $tableDate . $paddedLastThreeDigits;

            // Update the date and last three digits in the database
            $updateDateSql = "UPDATE dates SET date = '$uniqueId'";
            $mysqli->query($updateDateSql);
        } else {
            // If it's a new date, set the ID to the current date followed by '001'
            $uniqueId = $currentDate . '001';

            // Update the date in the database
            $updateDateSql = "UPDATE dates SET `date` = '$uniqueId'";
            $mysqli->query($updateDateSql);
        }
    } else {
        // Handle error if no date is found in the dates table
        die("Error fetching date from the database.");
    }

    // Insert data into the complaintreg table
    $complaintRegSql = "INSERT INTO complaintreg (name, dept, domain, descri, status, id) VALUES (?, ?, ?, ?, ?, ?)";
    $complaintRegStmt = $mysqli->prepare($complaintRegSql);

    // Bind parameters and execute the statement for complaintreg table
    $complaintRegStmt->bind_param("ssisss", $name, $department, $complaintDomain, $complaintDescription, $status, $uniqueId);
    $complaintRegStmt->execute();

    // Check for errors in complaintreg table
    if ($complaintRegStmt->errno) {
        die("Error executing complaintreg statement: " . $complaintRegStmt->error);
    }

    // Insert data into the appropriate domain table
    $domainTable = 'domain' . $complaintDomain;
    $domainComplaintSql = "INSERT INTO $domainTable (name, dept, descri, status, id) VALUES (?, ?, ?, ?, ?)";
    $domainComplaintStmt = $mysqli->prepare($domainComplaintSql);

    // Bind parameters and execute the statement for domain table
    $domainComplaintStmt->bind_param("sssss", $name, $department, $complaintDescription, $status, $uniqueId);
    $domainComplaintStmt->execute();

    // Check for errors in domain table
    if ($domainComplaintStmt->errno) {
        die("Error executing $domainTable statement: " . $domainComplaintStmt->error);
    }
    
    // Redirect to success page with unique ID
    //header("Location: submit-success.php?unique_id=" . urlencode($uniqueId));
    header("Location: email-user.php?unique_id=" . urlencode($uniqueId) . "&name=" . urlencode($name) . "&complaintDomain=" . urlencode($complaintDomain));
    
    exit();
} else {
    // If the request method is not POST, redirect to the form page or handle the situation accordingly
    header("Location: homepage.php");
    exit();
}
?>
