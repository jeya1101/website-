<?php
session_start();
include('db.php');

// Only attendee can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
    header('Location: login.php');
    exit;
}

$attendee_id = $_SESSION['user_id'];

// Fetch registered events
$sql = "
    SELECT e.title, e.description, e.event_date, e.location, r.payment_status
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    WHERE r.attendee_id = ?
    ORDER BY e.event_date DESC
";
$stmt = sqlsrv_query($conn, $sql, array($attendee_id));
if ($stmt === false) die(print_r(sqlsrv_errors(), true));

?>
<!DOCTYPE html>
<html>
<head>
  <title>My Registered Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      min-height: 100vh;
      padding: 2rem;
    }
    .card {
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body>
<div class="container">
  <h1 class="mb-4 text-center">My Registered Events</h1>

  <?php if (sqlsrv_has_rows($stmt)): ?>
    <div class="row justify-content-center">
      <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { ?>
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
              <p class="mb-1"><strong>Date & Time:</strong> <?= $row['event_date']->format('Y-m-d H:i') ?></p>
              <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
              <p>
                <strong>Payment Status:</strong>
                <?php if ($row['payment_status'] === 'pending'): ?>
                  <span class="badge bg-danger">Pending</span>
                <?php elseif ($row['payment_status'] === 'paid'): ?>
                  <span class="badge bg-success">Paid</span>
                <?php else: ?>
                  <span class="badge bg-secondary"><?= htmlspecialchars($row['payment_status']) ?></span>
                <?php endif; ?>
              </p>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center fs-5">
      You have not registered for any events yet.
    </div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="index_dashboard.php" class="btn btn-outline-primary">
      <i class="bi bi-arrow-left"></i> Back to Events
    </a>
  </div>
</div>
</body>
</html>
