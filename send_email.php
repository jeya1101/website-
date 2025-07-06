<?php
session_start();
if ($_SESSION['role'] !== 'organizer') {
    die('Access denied');
}

include('db.php');

$msg = "";
$emails = [];


 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = (int)$_POST['event_id'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Fetch attendee emails
    $sql = "SELECT u.email FROM registrations r JOIN users u ON r.attendee_id = u.id WHERE r.event_id = ?";
    $stmt = sqlsrv_query($conn, $sql, array($eventId));
    $emails = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $emails[] = $row['email'];
    }

    // Fetch organizer email for Reply-To
    $organizerId = $_SESSION['user_id'];
    $orgStmt = sqlsrv_query($conn, "SELECT email FROM users WHERE id = ?", array($organizerId));
    $org = sqlsrv_fetch_array($orgStmt, SQLSRV_FETCH_ASSOC);
    $replyToEmail = $org['email'] ?? 'jeyasakthi01@gmail.com';

    // Headers
    $headers = "From: Admin <jeyasakthi01@gmail.com>\r\n";
    $headers .= "Reply-To: {$replyToEmail}\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // Send emails
    foreach ($emails as $to) {
        mail($to, $subject, $message, $headers);
    }

    $msg = "<div class='alert alert-success text-center'>
            ✅ Emails sent to ".count($emails)." attendees.
            </div>";
}

   
// Fetch events for dropdown
$eventsStmt = sqlsrv_query($conn, "SELECT id, title FROM events ORDER BY event_date DESC");
if ($eventsStmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Email to Attendees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <a href="admin_dashboard.php" class="btn btn-outline-primary mb-4">← Back to Dashboard</a>

    <div class="card shadow p-4 mx-auto" style="max-width:600px;">
        <h3 class="mb-4 text-center">Send Email to Attendees</h3>

        <?= $msg ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Select Event</label>
                <select name="event_id" class="form-select" required>
                    <option value="">-- Choose Event --</option>
                    <?php while ($e = sqlsrv_fetch_array($eventsStmt, SQLSRV_FETCH_ASSOC)) { ?>
                        <option value="<?= $e['id'] ?>">
                            <?= htmlspecialchars($e['title']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="message" class="form-control" rows="5" required></textarea>
            </div>

            <button class="btn btn-success w-100">Send Emails</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
