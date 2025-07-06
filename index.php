<?php
session_start();
include('db.php');

$role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$name = '';

if ($user_id) {
    $stmt = sqlsrv_query($conn, "SELECT name FROM users WHERE id = ?", array($user_id));
    if ($stmt !== false) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($row) $name = $row['name'];
    }
}

// Fetch upcoming events
$events = [];
$sql = "SELECT TOP 6 id, title, description, event_date, location FROM events ORDER BY event_date ASC";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt !== false) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Management Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: url('index.jpg') center center no-repeat;
      background-size: cover;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    .overlay {
      background: rgba(255, 255, 255, 0.85);
      padding: 3rem;
      border-radius: 15px;
      text-align: center;
      max-width: 800px;
      margin: 3rem auto;
      animation: fadeIn 1.5s ease;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    h1 {
      font-size: 2rem;
      font-weight: 700;
      color: #2c3e50;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
    }
    p.tagline {
      font-size: 1.2rem;
      margin-top: 1rem;
      color: #555;
    }
    .btn-custom, .btn-outline-primary {
      border: 2px solid #007bff;
      color: #007bff;
      transition: all 0.3s ease;
    }
    .btn-custom:hover, .btn-outline-primary:hover {
      background: #007bff;
      color: #fff;
    }
    .events-section {
      background: rgba(255, 255, 255, 0.85);
      padding: 3rem;
      border-radius: 15px;
      max-width: 900px;
      margin: 3rem auto;
      animation: fadeIn 1.5s ease;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    .event-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    @keyframes fadeIn {
      from {opacity:0; transform: translateY(-20px);}
      to {opacity:1; transform: translateY(0);}
    }
  </style>
</head>
<body>

  <!-- Welcome area first -->
  <div class="overlay">
    <h1>EVENT MANAGEMENT PORTAL</h1>
    <p class="tagline">Empowering EventHorizon Pty Ltd. to seamlessly plan, organize, and track all your events in one place.</p>

    <?php if ($role === 'organizer' || $role === 'attendee'): ?>
      <p class="mt-4">Welcome back, <strong><?= htmlspecialchars($name) ?></strong>!</p>
      <div class="mt-4">
        <?php if ($role === 'organizer'): ?>
          <a href="admin_dashboard.php" class="btn btn-custom btn-lg me-3">Go to Dashboard</a>
        <?php elseif ($role === 'attendee'): ?>
          <a href="index_dashboard.php" class="btn btn-custom btn-lg me-3">Go to Dashboard</a>
        <?php endif; ?>
        <a href="logout.php" class="btn btn-outline-primary btn-lg">Logout</a>
      </div>
    <?php else: ?>
      <div class="mt-5">
        <a href="login.php" class="btn btn-custom btn-lg me-3">Login</a>
        <a href="register_user.php" class="btn btn-outline-primary btn-lg">Sign up</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Upcoming events below -->
  <div class="events-section">
    <h2 class="text-center mb-4">Upcoming Events</h2>
    <div class="row g-4 justify-content-center">
      <?php if (count($events) > 0): ?>
        <?php foreach ($events as $event): ?>
          <div class="col-md-4">
            <div class="card event-card h-100">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                <p class="card-text"><small class="text-muted"><?= $event['location'] ?> | <?= $event['event_date']->format('M d, Y') ?></small></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <p class="text-center">No upcoming events at the moment. Please check back later!</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
