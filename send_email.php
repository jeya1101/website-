<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include('db.php');

$msg = "";

// Example: Fetch attendees
$sql = "SELECT u.email, u.name FROM users u 
        JOIN registrations r ON u.id = r.attendee_id 
        JOIN events e ON r.event_id = e.id";
$stmt = sqlsrv_query($conn, $sql);

if (isset($_POST['send_email'])) {
    $mail = new PHPMailer(true);
    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your@gmail.com'; // YOUR GMAIL
        $mail->Password = 'your-app-password'; // YOUR APP PASSWORD
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your@gmail.com', 'Event Admin');
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for registering!';
        
        $sentCount = 0;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $toEmail = $row['email'];
            $toName = $row['name'];
            $mail->clearAddresses(); 
            $mail->addAddress($toEmail, $toName);
            $mail->Body = "Hi {$toName},<br><br>Thank you for registering. We look forward to seeing you!";
            $mail->send();
            $sentCount++;
        }
        $msg = "<div class='alert alert-success'>✅ Emails sent to {$sentCount} attendees.</div>";

    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>❌ Email failed: {$mail->ErrorInfo}</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send Email</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h3>Send Email to Registered Attendees</h3>
  <?= $msg ?>
  <form method="POST">
    <button name="send_email" class="btn btn-primary mt-3">Send Email</button>
  </form>
</div>
</body>
</html>
