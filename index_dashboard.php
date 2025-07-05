<?php
session_start();
include('db.php');

$role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$name = '';

if ($user_id) {
    $userSql = "SELECT name FROM users WHERE id = ?";
    $userStmt = sqlsrv_query($conn, $userSql, array($user_id));
    $userData = sqlsrv_fetch_array($userStmt, SQLSRV_FETCH_ASSOC);
    $name = $userData ? $userData['name'] : '';
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Event Management Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: url('index.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
    }
    .overlay {
      background: rgba(255,255,255,0.9);
      min-height: 100vh;
      padding: 2rem;
    }
    .navbar-brand {
      font-weight: bold;
      font-size: 1.5rem;
    }
    .btn-custom:hover {
      background-color: #007bff;
      color: #fff;
    }
  </style>
</head>
<body>
<div class="overlay">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">EventHorizon Dashboard</a>
      <div>
        <?php if ($role): ?>
          <span class="me-3">Welcome, <?= htmlspecialchars($name) ?></span>
          <a href="logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-primary btn-sm me-2">Login</a>
          <a href="register_user.php" class="btn btn-primary btn-sm">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container">
    <?php if ($role === 'attendee'): ?>
      <div class="text-end mb-3 ms-5">
        <a href="my-events.php" class="btn btn-outline-primary">
          <i class="bi bi-calendar-check"></i> View My Registered Events
        </a>
      </div>
    <?php endif; ?>

    <h1 class="mb-4 text-center">Upcoming Events</h1>
    <p class="text-center mb-5">Discover, join, and manage events all in one place.</p>

    <div class="row justify-content-center">
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
            <?php if ($role === 'organizer'): ?>
              <a href="edit_event.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm w-100 mb-2">Edit Event</a>
            <?php elseif ($role === 'attendee'): ?>
              <a href="register_event.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm w-100">Register</a>
            <?php else: ?>
              <small><a href="login.php">Login to register</a></small>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php } ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
