<?php
session_start();
include('db.php');

// Must be logged in as attendee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
    header('Location: login.php');
    exit;
}

$attendee_id = $_SESSION['user_id'];
$msg = "";

// Check event ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p>Invalid event ID. <a href='index.php'>Go back</a></p>");
}
$event_id = (int)$_GET['id'];

// Fetch attendee existing info
$userSql = "SELECT * FROM users WHERE id = ?";
$userParams = array($attendee_id);
$userStmt = sqlsrv_query($conn, $userSql, $userParams);
$user = sqlsrv_fetch_array($userStmt, SQLSRV_FETCH_ASSOC);

// Fetch event details
$evSql = "SELECT * FROM events WHERE id = ?";
$evParams = array($event_id);
$evStmt = sqlsrv_query($conn, $evSql, $evParams);
$ev = sqlsrv_fetch_array($evStmt, SQLSRV_FETCH_ASSOC);

if (!$ev) {
    die("<p>Event not found. <a href='index.php'>Go back</a></p>");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];

    // Update user profile
    $updateSql = "UPDATE users SET name = ?, contact = ? WHERE id = ?";
    sqlsrv_query($conn, array($name, $contact, $attendee_id));

    // Check if already registered
    $checkSql = "SELECT * FROM registrations WHERE attendee_id = ? AND event_id = ?";
    $checkStmt = sqlsrv_query($conn, $checkSql, array($attendee_id, $event_id));

    if (sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
        $msg = "<div class='alert alert-warning shadow-sm text-center fs-5'>
                    ⚠ You are already registered for this event.
                </div>";
    } else {
        // ✅ Insert into registrations with contact snapshot
        $insertSql = "INSERT INTO registrations (attendee_id, event_id, contact) VALUES (?, ?, ?)";
        sqlsrv_query($conn, array($attendee_id, $event_id, $contact));

        $msg = "<div class='alert alert-success shadow-sm text-center fs-5'>
                    🎉 Successfully registered <strong>$name ($contact)</strong> for <strong>" . htmlspecialchars($ev['title']) . "</strong>!
                    <br>Thank you for signing up. We'll keep you updated.
                </div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register for Event</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .alert { margin-top: 1.5rem; }
    .card { margin-top: 1.5rem; }
  </style>
</head>
<body>

<div class="container">
  <h1 class="text-center mt-4">Event Registration</h1>

  <!-- Success or warning message outside card -->
  <?= $msg ?>

  <div class="card p-4 shadow-sm mt-3">
    <h3>Register for <?= htmlspecialchars($ev['title']) ?></h3>
    <p><strong>Date:</strong> <?= $ev['event_date']->format('Y-m-d H:i') ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($ev['location']) ?></p>
    <p><strong>Capacity:</strong> <?= htmlspecialchars($ev['capacity']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($ev['description'])) ?></p>

    <form method="post" class="mt-4">
      <div class="mb-3">
        <label class="form-label">Your Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Contact Number</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($user['contact']) ?>" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100">Confirm Registration</button>
    </form>

    <div class="mt-3"><a href="index_dashboard.php" class="btn btn-link">Back to events</a></div>
  </div>
</div>

</body>
</html>
