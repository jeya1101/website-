<?php
session_start();
include('db.php');

// Only for organizer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header('Location: login.php');
    exit;
}

// Example: get admin name
$adminName = "Admin";
$user_id = $_SESSION['user_id'];
$stmt = sqlsrv_query($conn, "SELECT name FROM users WHERE id = ?", array($user_id));
if ($stmt && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
    $adminName = $row['name'];
}

// Get metrics
$totalEvents = 0;
$totalAttendees = 0;

$countEventsStmt = sqlsrv_query($conn, "SELECT COUNT(*) AS total FROM events");
if ($row = sqlsrv_fetch_array($countEventsStmt, SQLSRV_FETCH_ASSOC)) {
    $totalEvents = $row['total'];
}

$countAttendeesStmt = sqlsrv_query($conn, "SELECT COUNT(*) AS total FROM registrations");
if ($row = sqlsrv_fetch_array($countAttendeesStmt, SQLSRV_FETCH_ASSOC)) {
    $totalAttendees = $row['total'];
}

// Upcoming
$futureStmt = sqlsrv_query($conn, "SELECT COUNT(*) AS upcoming FROM events WHERE event_date > GETDATE()");
$upcoming = 0;
if ($r = sqlsrv_fetch_array($futureStmt, SQLSRV_FETCH_ASSOC)) {
    $upcoming = $r['upcoming'];
}

// Fetch events
$eventsStmt = sqlsrv_query($conn, "SELECT * FROM events ORDER BY event_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Event Organizer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      width: 240px;
      position: fixed;
      background: #343a40;
      color: #fff;
      padding-top: 2rem;
    }
    .sidebar a {
      color: #ddd;
      text-decoration: none;
      padding: 12px 20px;
      display: block;
      transition: background 0.3s;
    }
    .sidebar a:hover {
      background: #495057;
      color: #fff;
    }
    .main-content {
      margin-left: 240px;
      padding: 2rem;
    }
    .card-stat {
      border: none;
      color: #fff;
      padding: 1.5rem;
      border-radius: 12px;
    }
    .bg-events { background: #007bff; }
    .bg-registrations { background: #28a745; }
    .bg-upcoming { background: #ffc107; color: #212529; }
  </style>
</head>
<body>

<div class="sidebar">
  <h4 class="text-center mb-4"><i class="bi bi-speedometer2 me-2"></i> Admin Panel</h4>
  <a href="admin_dashboard.php"><i class="bi bi-house me-2"></i> Dashboard</a>
  <a href="Manage_Events.php"><i class="bi bi-calendar-event me-2"></i> Manage Event</a>
  <a href="Manage_Attendees.php"><i class="bi bi-people me-2"></i> Manage Attendees</a>
  <a href="#"><i class="bi bi-chat-dots me-2"></i> Manage Chat</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="main-content">
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $_SESSION['success'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <h2 class="mb-3">Welcome Back, <strong><?= htmlspecialchars($adminName) ?></strong></h2>

  <!-- Add New Event Button -->
  <div class="mb-4">
    <a href="create_event.php" class="btn btn-primary shadow-sm">
      <i class="bi bi-plus-circle"></i> Add New Event
    </a>
  </div>

  <!-- Metrics cards -->
  <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div class="card-stat bg-events">
        <h5><i class="bi bi-calendar-event me-2"></i> Total Events</h5>
        <h3><?= $totalEvents ?></h3>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card-stat bg-registrations">
        <h5><i class="bi bi-person-check me-2"></i> Total Registrations</h5>
        <h3><?= $totalAttendees ?></h3>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card-stat bg-upcoming">
        <h5><i class="bi bi-clock-history me-2"></i> Upcoming Events</h5>
        <h3><?= $upcoming ?></h3>
      </div>
    </div>
  </div>

  <h4>All Events</h4>
  <table class="table table-hover mt-3">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Capacity</th>
        <th>Fee (RM)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($event = sqlsrv_fetch_array($eventsStmt, SQLSRV_FETCH_ASSOC)) { ?>
      <tr>
        <td><?= htmlspecialchars($event['title']) ?></td>
        <td><?= $event['event_date']->format('Y-m-d H:i') ?></td>
        <td><?= htmlspecialchars($event['location']) ?></td>
        <td><?= htmlspecialchars($event['capacity']) ?></td>
        <td><?= 'RM ' . number_format($event['fee'], 2) ?></td>
        <td>
          <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> Update
          </a>
          <a href="delete_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?')">
            <i class="bi bi-trash"></i> Delete
          </a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
