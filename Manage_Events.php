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

// Fetch events
$eventsStmt = sqlsrv_query($conn, "SELECT * FROM events ORDER BY event_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Events - Admin</title>
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
    .table-container {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .table thead {
      background-color: #0d6efd;
      color: #fff;
    }
    .table td, .table th {
      vertical-align: middle;
    }
    .btn-action {
      margin-right: 5px;
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
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $_SESSION['success'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <h2 class="mb-4"><i class="bi bi-calendar-event"></i> Manage Events</h2>
 
  <div class="mb-4">
    <a href="create_event.php" class="btn btn-primary shadow-sm">
      <i class="bi bi-plus-circle"></i> Add New Event
    </a>
  </div>

  <div class="table-container">
    <table class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>  
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
          <td><?= htmlspecialchars($event['description']) ?></td>
          <td><?= $event['event_date']->format('Y-m-d H:i') ?></td>
          <td><?= htmlspecialchars($event['location']) ?></td>
          <td><?= htmlspecialchars($event['capacity']) ?></td>
          <td><?= 'RM ' . number_format($event['fee'], 2) ?></td>
          <td>
            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-warning btn-action">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="delete_event.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Delete this event?')">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
