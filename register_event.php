<?php
session_start();
include('db.php');

// Must be logged in as attendee
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
    header('Location: login.php');
    exit;
}

// Check event ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p>Invalid event ID. <a href='index.php'>Go back</a></p>");
}

$event_id = (int)$_GET['id'];
$msg = "";

// Insert registration if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for existing registration
    $checkSql = "SELECT * FROM registrations WHERE attendee_id = ? AND event_id = ?";
    $checkParams = array($_SESSION['user_id'], $event_id);
    $checkStmt = sqlsrv_query($conn, $checkSql, $checkParams);

    if ($checkStmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC)) {
        $msg = "You are already registered for this event.";
    } else {
        // Insert new registration
        $insertSql = "INSERT INTO registrations (attendee_id, event_id) VALUES (?, ?)";
        $insertParams = array($_SESSION['user_id'], $event_id);
        $insertStmt = sqlsrv_query($conn, $insertSql, $insertParams);

        if ($insertStmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $msg = "You have successfully registered!";
    }
}

// Fetch event details
$evSql = "SELECT * FROM events WHERE id = ?";
$evParams = array($event_id);
$evStmt = sqlsrv_query($conn, $evSql, $evParams);

if ($evStmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$ev = sqlsrv_fetch_array($evStmt, SQLSRV_FETCH_ASSOC);

if (!$ev) {
    die("<p>Event not found. <a href='index.php'>Go back</a></p>");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register for Event</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <div class="card p-4 shadow-sm">
      <h3>Register for <?= htmlspecialchars($ev['title']) ?></h3>
      <?= $msg ? "<div class='alert alert-info'>$msg</div>" : "" ?>
      <p><strong>Date:</strong> <?= $ev['event_date']->format('Y-m-d H:i') ?></p>
      <p><strong>Location:</strong> <?= htmlspecialchars($ev['location']) ?></p>
      <p><strong>Capacity:</strong> <?= htmlspecialchars($ev['capacity']) ?></p>
      <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($ev['description'])) ?></p>
      <form method="post">
        <button class="btn btn-primary w-100">Confirm Registration</button>
      </form>
      <div class="mt-3"><a href="index.php" class="btn btn-link">Back to events</a></div>
    </div>
  </div>
</body>
</html>
