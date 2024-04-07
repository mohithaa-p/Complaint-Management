<?php
require 'PHPMailer\PHPMailer\PHPMailerAutoload.php';
$mysqli = require __DIR__ . "/database.php";

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = '22z134@psgitech.ac.in';                 // SMTP username
$mail->Password = 'cpczppnryfqlrwoh';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;   

$name = isset($_GET['name']) ? $_GET['name'] : 'User';
$unique_id = isset($_GET['unique_id']) ? $_GET['unique_id'] : 'N/A';
$complaintDomain= isset($_GET['complaintDomain']) ? $_GET['complaintDomain'] : 'N/A';
date_default_timezone_set('Asia/Kolkata');
$subDateTime = date("Y-m-d H:i:s"); 

$user_email = ""; // Initialize variable to store recipient's email address

$stmt = $mysqli->prepare("SELECT email FROM adminlogin WHERE id = ?");
$stmt->bind_param("i", $complaintDomain);
$stmt->execute();
$stmt->bind_result($user_email);
$stmt->fetch();
$stmt->close();
$mysqli->close();// TCP port to connect to
if (empty($user_email)) {
    echo 'Recipient email address is empty or invalid.';
    exit; // Stop execution
}


$mail->setFrom('22z134@psgitech.ac.in', 'Mohithaa');
$mail->addAddress($user_email, 'Admin');     // Add a recipient
// $mail->addAddress('ellen@example.com');               // Name is optional cpczppnryfqlrwoh
// $mail->addReplyTo('info@example.com', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');

// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML  ccujlaystaaxtsxm

$mail->Subject = 'Complaint Registered';
$mail->Body = 'Dear Admin,<br><br>
A new complaint with ID ' . $unique_id . ' has been raised successfully by ' . $name. ' on ' . $subDateTime . '.<br><br>
Your kind action is required to look into the complaint and update the status  by logging in at "websitename".<br><br>
Note: This is an auto-generated email. Please do not reply to this email.<br><br>
With regards,<br>
Maintenance Cell';
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';xgao ohsz coui kmrt    

//header("Location: email-admin.php?unique_id=" . urlencode($uniqueId) . "&name=" . urlencode($name));
if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    
    

} else {
    echo 'Message has been sent';
    header("Location: submit-success.php?unique_id=" . urlencode($unique_id));
}

?>