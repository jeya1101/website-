<?php
session_start();
include('db.php');

$role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Event Management Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <h1 class="mb-4 text-center">Event Management Portal</h1>
  <div class="mb-4 text-end">
    <strong>Role:</strong> <?= htmlspecialchars($role ?? 'Guest') ?> 
    <?php if ($role): ?>
      | <a href="logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
    <?php endif; ?>
  </div>

  <?php if ($role === 'organizer'): ?>
    <div class="row mb-5">
      <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3">Add New Event</h4>
            <form action="create_event.php" method="POST">
              <input name="title" placeholder="Event Title" class="form-control mb-2" required>
              <textarea name="description" placeholder="Event Description" class="form-control mb-2" required></textarea>
              <input type="datetime-local" name="event_date" class="form-control mb-2" required>
              <input name="location" placeholder="Location" class="form-control mb-2" required>
              <input type="number" name="capacity" placeholder="Capacity" class="form-control mb-2" required>
              <input type="number" step="0.01" name="fee" placeholder="Fee (optional)" class="form-control mb-3">
              <button class="btn btn-success w-100">Add Event</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <h3 class="mb-3">Upcoming Events</h3>
    <div class="row">
    <?php
      $sql = "SELECT * FROM events ORDER BY event_date";
      $stmt = sqlsrv_query($conn, $sql);
      if ($stmt === false) die(print_r(sqlsrv_errors(), true));

      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    ?>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
            <small class="text-muted">
              <?= $row['event_date']->format('Y-m-d H:i') ?><br>
              <?= htmlspecialchars($row['location']) ?>
            </small>
          </div>
          <div class="card-footer bg-white border-0 d-flex justify-content-between">
            <a href="edit_event.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_event.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?')">Delete</a>
          </div>
        </div>
      </div>
    <?php } ?>
    </div>

    <h3 class="mt-5 mb-3">Attendee Registrations</h3>
    <ul class="list-group mb-5">
    <?php
      $sql = "SELECT r.*, e.title as event_name, u.name as attendee_name
              FROM registrations r
              JOIN events e ON r.event_id = e.id
              JOIN users u ON r.attendee_id = u.id";
      $stmt = sqlsrv_query($conn, $sql);
      if ($stmt === false) die(print_r(sqlsrv_errors(), true));

      while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo "<li class='list-group-item'>"
             . htmlspecialchars($r['attendee_name'])
             . " registered for "
             . htmlspecialchars($r['event_name'])
             . "</li>";
      }
    ?>
    </ul>

  <?php elseif ($role === 'attendee'): ?>
    <h3 class="mb-4">Events You Can Register For</h3>
    <div class="row">
    <?php
      $sql = "SELECT * FROM events ORDER BY event_date";
      $stmt = sqlsrv_query($conn, $sql);
      if ($stmt === false) die(print_r(sqlsrv_errors(), true));

      while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    ?>
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
            <small class="text-muted">
              <?= $row['event_date']->format('Y-m-d H:i') ?><br>
              <?= htmlspecialchars($row['location']) ?>
            </small>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="register_event.php?id=<?= $row['id'] ?>" class="btn btn-primary w-100">Register</a>
          </div>
        </div>
      </div>
    <?php } ?>
    </div>

  <?php else: ?>
    <p class="text-center">
      <a href="login.php" class="btn btn-primary">Login</a> or 
      <a href="register_user.php" class="btn btn-outline-primary">Sign up as Attendee</a>
    </p>
  <?php endif; ?>
</div>
</body>
</html>
