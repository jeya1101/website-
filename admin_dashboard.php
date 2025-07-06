<?php
session_start();
include('db.php');

// Only for organizer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header('Location: login.php');
    exit;
}

// Fetch organizer name
$adminName = '';
$adminStmt = sqlsrv_query($conn, "SELECT name FROM users WHERE id = ?", array($_SESSION['user_id']));
if ($adminStmt && ($row = sqlsrv_fetch_array($adminStmt, SQLSRV_FETCH_ASSOC))) {
    $adminName = $row['name'];
}

// Metrics
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
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
      transition: all 0.3s;
    }
    .card-stat i {
      opacity: 0.8;
    }
    .card-stat:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h4><i class="bi bi-speedometer2 me-2"></i> Admin Panel</h4>
  <a href="admin_dashboard.php"><i class="bi bi-house me-2"></i> Dashboard</a>
  <a href="Manage_Events.php"><i class="bi bi-calendar-event me-2"></i> Manage Event</a>
  <a href="Manage_Attendees.php"><i class="bi bi-people me-2"></i> Manage Attendees</a>
  <a href="#"><i class="bi bi-chat-dots me-2"></i> Manage Chat</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="main-content">
  <h2 class="mb-4">Welcome Back, <strong><?= htmlspecialchars($adminName) ?></strong></h2>
  

  <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div class="card card-stat p-3 text-white" style="background: #007bff;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>Total Events</h5>
            <h3><?= $totalEvents ?></h3>
          </div>
          <i class="bi bi-calendar-event" style="font-size:2rem;"></i>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-stat p-3 text-white" style="background: #28a745;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>Total Registrations</h5>
            <h3><?= $totalAttendees ?></h3>
          </div>
          <i class="bi bi-people" style="font-size:2rem;"></i>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card card-stat p-3 text-white" style="background: #ffc107;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h5>Upcoming Events</h5>
            <h3><?= $upcoming ?></h3>
          </div>
          <i class="bi bi-clock-history" style="font-size:2rem;"></i>
        </div>
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
      </tr>
    </thead>
    <tbody>
    <?php while ($event = sqlsrv_fetch_array($eventsStmt, SQLSRV_FETCH_ASSOC)) { ?>
      <tr>
        <td><?= htmlspecialchars($event['title']) ?></td>
        <td><?= $event['event_date']->format('Y-m-d H:i') ?></td>
        <td><?= htmlspecialchars($event['location']) ?></td>
        <td><?= htmlspecialchars($event['capacity']) ?></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
