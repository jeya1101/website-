<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include('db.php');

$msg = "";

// Fetch all attendee emails
$sql = "SELECT DISTINCT u.email, u.name FROM users u 
        JOIN registrations r ON u.id = r.attendee_id 
        JOIN events e ON r.event_id = e.id";
$stmt = sqlsrv_query($conn, $sql);

if (isset($_POST['send_email'])) {
    $mail = new PHPMailer(true);
    try {
        // SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your@gmail.com';     // 🔥 change
        $mail->Password = 'your-app-password';  // 🔥 change
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your@gmail.com', 'Event Admin');
        $mail->isHTML(true);
        $mail->Subject = 'Thank you for registering!';

        $sentCount = 0;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $toEmail = $row['email'];
            $toName  = $row['name'];

            $mail->clearAddresses();
            $mail->addAddress($toEmail, $toName);
            $mail->Body = "Hi {$toName},<br><br>Thank you for registering. We look forward to seeing you!";

            $mail->send();
            $sentCount++;
        }

        $msg = "<div class='alert alert-success mt-3'>✅ Emails sent to {$sentCount} attendees.</div>";

    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger mt-3'>❌ Email failed: {$mail->ErrorInfo}</div>";
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
  <a href="Manage_Attendees.php" class="btn btn-outline-primary mb-4">
    <i class="bi bi-arrow-left"></i> Back
  </a>
  <h3>Send Email to Registered Attendees</h3>
  <?= $msg ?>
  <form method="POST">
    <button type="submit" name="send_email" class="btn btn-primary mt-3">
      <i class="bi bi-envelope-fill"></i> Send Email
    </button>
  </form>
</div>
</body>
</html>
