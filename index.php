<?php
session_start();
include('db.php');

$role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
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
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    }
    .overlay {
      background: rgba(255, 255, 255, 0.85);
      padding: 3rem;
      border-radius: 15px;
      text-align: center;
      max-width: 700px;
      width: 90%;
      animation: fadeIn 1.5s ease;
      box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    h1 {
      font-size: 3rem;
      font-weight: 700;
      color: #2c3e50;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
    }
    p.tagline {
      font-size: 1.2rem;
      margin-top: 1rem;
      color: #555;
    }
    .btn-custom {
      background: transparent;
      border: 2px solid #007bff;
      color: #007bff;
      transition: all 0.3s ease;
    }
    .btn-custom:hover {
      background: #007bff;
      color: #fff;
    }
    .btn-outline-primary:hover {
      background: #007bff;
      color: #fff;
    }
    @keyframes fadeIn {
      from {opacity:0; transform: translateY(-20px);}
      to {opacity:1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="overlay">
    <h1>Event Management Portal</h1>
    <p class="tagline">Empowering EventHorizon Pty Ltd. to seamlessly plan, organize, and track all your events in one place.</p>

    <?php if ($role === 'organizer' || $role === 'attendee'): ?>
      <p class="mt-4">Welcome back, <?= htmlspecialchars($role) ?>!</p>
      <div class="mt-4">
        <a href="index_dashboard.php" class="btn btn-custom btn-lg me-3">Go to Dashboard</a>
        <a href="logout.php" class="btn btn-outline-primary btn-lg">Logout</a>
      </div>
    <?php else: ?>
      <div class="mt-5">
        <a href="login.php" class="btn btn-custom btn-lg me-3">Login</a>
        <a href="register_user.php" class="btn btn-outline-primary btn-lg">Sign up</a>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
