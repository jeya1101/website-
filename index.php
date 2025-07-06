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
    background: linear-gradient(135deg, #e0eafc, #cfdef3);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .overlay {
    background: rgba(255, 255, 255, 0.9);
    padding: 2.5rem;
    border-radius: 18px;
    text-align: center;
    max-width: 600px;
    margin-top: 3rem;
    box-shadow: 0 12px 30px rgba(0,0,0,0.2);
    animation: fadeIn 1.2s ease;
  }
  .overlay h1 {
    font-size: 2.2rem;
    font-weight: 700;
    color: #34495e;
    letter-spacing: 1px;
  }
  p.tagline {
    font-size: 1.1rem;
    margin-top: 0.8rem;
    color: #555;
  }
  .btn-custom, .btn-outline-primary {
    border: 2px solid #3498db;
    color: #3498db;
    transition: all 0.3s ease;
  }
  .btn-custom:hover, .btn-outline-primary:hover {
    background: #3498db;
    color: #fff;
  }
  .events-section {
    background: rgba(255,255,255,0.93);
    padding: 2rem;
    border-radius: 18px;
    margin: 3rem auto;
    max-width: 900px;
    width: 90%;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    animation: fadeInUp 1.5s ease;
  }
  .events-section h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
  }
  .event-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .event-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
  }
  .event-card .card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2980b9;
  }
  .event-card .card-text {
    font-size: 0.95rem;
    color: #555;
  }
  @keyframes fadeIn {
    from {opacity:0; transform: translateY(-20px);}
    to {opacity:1; transform: translateY(0);}
  }
  @keyframes fadeInUp {
    from {opacity:0; transform: translateY(30px);}
    to {opacity:1; transform: translateY(0);}
  }
</style>
</head>
<body>

<div class="overlay">
  <h1>EVENT MANAGEMENT PORTAL</h1>
  <p class="tagline">Plan, organize & track all your events with EventHorizon Pty Ltd.</p>

  <?php if ($role === 'organizer' || $role === 'attendee'): ?>
    <p class="mt-4">Welcome, <strong><?= htmlspecialchars($name) ?></strong>!</p>
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

<div class="events-section">
  <h2>Upcoming Events</h2>
  <div class="row g-4">
    <?php if (count($events) > 0): ?>
      <?php foreach ($events as $event): ?>
        <div class="col-md-4">
          <div class="card event-card h-100">
            <div class="card-body">
                <center>
              <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
              <p class="card-text"><small class="text-muted"><?= htmlspecialchars($event['location']) ?> | <?= $event['event_date']->format('M d, Y') ?></small></p>
            </center>
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
