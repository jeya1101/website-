<?php
session_start();
if ($_SESSION['role'] !== 'organizer') {
    die('Access denied');
}

include('db.php');

// Insert only if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $capacity = $_POST['capacity'];
    $fee = $_POST['fee'];

    // Convert date to standard format
    $dateTimeFormatted = date('Y-m-d H:i:s', strtotime($date));

    $sql = "INSERT INTO events (title, event_date, location, capacity, fee) VALUES (?, ?, ?, ?, ?)";
    $params = array($name, $dateTimeFormatted, $location, $capacity, $fee);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Set success flash message
    $_SESSION['success'] = "✅ Successfully created!";
    header('Location: Manage_Events.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Create Event</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <a href="Manage_Events.php" class="btn btn-outline-primary mb-4 shadow-sm">
    <i class="bi bi-arrow-left"></i> Back to Manage Events
  </a>

  <div class="card shadow p-4 mx-auto" style="max-width:600px;">
    <h3 class="mb-4 text-center">Create New Event</h3>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Event Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Date & Time</label>
        <input type="datetime-local" name="date" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Capacity</label>
        <input type="number" name="capacity" class="form-control" required min="1">
      </div>
      <div class="mb-3">
        <label class="form-label">Entrance Fee (RM)</label>
        <input type="number" step="0.01" name="fee" class="form-control" required>
      </div>
      <button class="btn btn-success w-100">Create Event</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
