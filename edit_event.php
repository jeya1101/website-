<?php
session_start();
if ($_SESSION['role'] !== 'organizer') {
    die('Access denied');
}

include('db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid event ID.');
}

$id = (int)$_GET['id'];

// Fetch event details
$sql = "SELECT * FROM events WHERE id = ?";
$params = array($id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$e = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$e) {
    die('Event not found.');
}

// Prepare datetime-local format (Y-m-d\TH:i)
$eventDateTime = $e['event_date']->format('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Event</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card p-4 shadow mx-auto" style="max-width:600px;">
    <h3 class="mb-4 text-center">Edit Event</h3>
    <form action="update_event.php" method="POST">
      <input type="hidden" name="id" value="<?= $e['id'] ?>">

      <div class="mb-3">
        <label class="form-label">Event Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($e['title']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Date & Time</label>
        <input type="datetime-local" name="date" value="<?= $eventDateTime ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($e['location']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Capacity</label>
        <input type="number" name="capacity" value="<?= htmlspecialchars($e['capacity']) ?>" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Entrance Fee (RM)</label>
        <input type="number" step="0.01" name="fee" value="<?= htmlspecialchars($e['fee']) ?>" class="form-control" required>
      </div>

      <button class="btn btn-success w-100">Update Event</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
