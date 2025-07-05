<?php
session_start();
include('db.php');

// Only organizer can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header('Location: login.php');
    exit;
}

// Fetch all attendee registrations
$sql = "
    SELECT 
        r.id AS reg_id, 
        u.name AS attendee_name, 
        u.contact AS attendee_contact, 
        e.title AS event_title,
        r.payment_status,
        r.bank,
        r.created_at
    FROM registrations r
    JOIN users u ON r.attendee_id = u.id
    JOIN events e ON r.event_id = e.id
    ORDER BY r.created_at DESC
";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Attendees</title>
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
      text-decoration: none;
      transition: background 0.3s;
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
  <h4 class="text-center mb-4"><i class="bi bi-speedometer2 me-2"></i> Admin Panel</h4>
  <a href="admin_dashboard.php"><i class="bi bi-house me-2"></i> Dashboard</a>
  <a href="Manage_Events.php"><i class="bi bi-calendar-event me-2"></i> Manage Events</a>
  <a href="Manage_Attendees.php"><i class="bi bi-people me-2"></i> Manage Attendees</a>
  <a href="#"><i class="bi bi-chat-dots me-2"></i> Manage Chat</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<div class="content">
  <h2 class="mb-4"><i class="bi bi-people"></i> Manage Attendees</h2>

  <table class="table table-striped align-middle shadow-sm">
    <thead>
      <tr>
        <th>#</th>
        <th>Attendee Name</th>
        <th>Contact</th>
        <th>Event</th>
        <th>Payment</th>
        <th>Bank</th>
        <th>Registered At</th>
      </tr>
    </thead>
    <tbody>
    <?php 
    $count = 1;
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { ?>
      <tr>
        <td><?= $count++ ?></td>
        <td><?= htmlspecialchars($row['attendee_name']) ?></td>
        <td><?= htmlspecialchars($row['attendee_contact']) ?></td>
        <td><?= htmlspecialchars($row['event_title']) ?></td>
        <td><?= htmlspecialchars($row['payment_status']) ?></td>
        <td><?= htmlspecialchars($row['bank']) ?></td>
        <td><?= $row['created_at']->format('Y-m-d H:i') ?></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
