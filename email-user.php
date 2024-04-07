<?php
require 'PHPMailer\PHPMailer\PHPMailerAutoload.php';
$mysqli = require __DIR__ . "/database.php";

$mail = new PHPMailer;

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = '22z134@psgitech.ac.in';            // SMTP username
$mail->Password = 'cpczppnryfqlrwoh';                 // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption
$mail->Port = 587;                                    // TCP port to connect to

$name = isset($_GET['name']) ? $_GET['name'] : 'User';
$unique_id = isset($_GET['unique_id']) ? $_GET['unique_id'] : 'N/A';
$complaintDomain = isset($_GET['complaintDomain']) ? $_GET['complaintDomain'] : 'N/A';
date_default_timezone_set('Asia/Kolkata');
$subDateTime = date("Y-m-d H:i:s", time()); // Get current date and time

$user_email = ""; // Initialize variable to store recipient's email address

$stmt = $mysqli->prepare("SELECT email FROM user WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result($user_email);
$stmt->fetch();
$stmt->close();
$mysqli->close();


$mail->setFrom('22z134@psgitech.ac.in', 'Mohithaa');
$mail->addAddress($user_email, $name); // Add recipient's email address
$mail->isHTML(true); // Set email format to HTML

$mail->Subject = 'Complaint Registered Successfully';
$mail->Body = 'Dear ' . $name . ',<br><br>
A new complaint with ID ' . $unique_id . ' has been raised successfully on ' . $subDateTime . '.<br><br>
You can view the status of your complaint by logging in at "websitename".<br><br>
Note: This is an auto-generated email. Please do not reply to this email.<br><br>
With regards,<br>
Maintenance Cell';


if (!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    header("Location: email-admin.php?unique_id=" . urlencode($unique_id) . "&name=" . urlencode($name) . "&complaintDomain=" . urlencode($complaintDomain));
    
}
?>
