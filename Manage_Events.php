<?php
session_start();
include('db.php');

// Only organizer can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header('Location: login.php');
    exit;
}

// Fetch all events
$sql = "SELECT * FROM events ORDER BY event_date DESC";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Events</title>
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
      display: block;
      padding: 12px 20px;
      transition: background 0.3s;
      text-decoration: none;
    }
    .sidebar a:hover {
      background: #495057;
      color: #fff;
    }
    .content {
      margin-left: 240px;
      padding: 2rem;
    }
    .table th {
      background: #343a40;
      color: #fff;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h4><i class="bi bi-speedometer2 me-2"></i> Admin Panel</h4>
  <a href="admin_dashboard.php"><i class="bi bi-house me-2"></i> Dashboard</a>
  <a href="Manage_Events.php"><i class="bi bi-calendar-event me-2"></i> Manage Event</a>
  <a href="Manage_Attendees.php"><i class="bi bi-people me-2"></i>Manage Attendees</a>
  <a href="#"><i class="bi bi-chat-dots me-2"></i>Manage Chat</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="content">
  <h2 class="mb-4"><i class="bi bi-calendar-event"></i> Manage Events</h2>
  <div class="mb-3">
    <a href="create_event.php" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New Event
    </a>
  </div>

  <table class="table table-striped align-middle shadow-sm">
    <thead>
      <tr>
        <th>ID</th>
        <th>Event Name</th>
        <th>Date & Time</th>
        <th>Location</th>
        <th>Capacity</th>
        <th>Fee (RM)</th> <!-- NEW -->
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { ?>
      <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= $row['event_date']->format('Y-m-d H:i') ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['capacity']) ?></td>
        <td><?= 'RM ' . number_format($row['fee'], 2) ?></td> <!-- NEW -->
        <td>
          <a href="edit_event.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> Update
          </a>
          <a href="delete_event.php?id=<?= $row['id'] ?>"
             onclick="return confirm('Are you sure to delete this event?');"
             class="btn btn-sm btn-danger">
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
