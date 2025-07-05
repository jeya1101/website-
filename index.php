<?php
session_start();
include('db.php');

$role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Event Management Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-4">
    <h1 class="mb-4">Event Management Portal</h1>
    <p><strong>Role:</strong> <?= htmlspecialchars($role ?? 'Guest') ?> | <a href="logout.php">Logout</a></p>

    <?php if ($role === 'organizer'): ?>
    <h2>Add Event</h2>
    <form action="create_event.php" method="POST" class="mb-4">
      <input name="title" placeholder="Event Title" class="form-control mb-2" required>
      <textarea name="description" placeholder="Event Description" class="form-control mb-2" required></textarea>
      <input type="date" name="event_date" class="form-control mb-2" required>
      <input name="location" placeholder="Location" class="form-control mb-2" required>
      <input type="number" name="capacity" placeholder="Capacity" class="form-control mb-2" required>
      <input type="number" step="0.01" name="fee" placeholder="Fee" class="form-control mb-2">
      <button class="btn btn-success">Add Event</button>
    </form>

    <h2>Upcoming Events</h2>
    <ul class="list-group mb-4">
    <?php
    $sql = "SELECT * FROM events ORDER BY event_date";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
      echo "<li class='list-group-item d-flex justify-content-between align-items-center'>"
          . "<div><strong>" . htmlspecialchars($row['title']) . "</strong> ("
          . $row['event_date']->format('Y-m-d H:i') . ")<br>"
          . "Location: " . htmlspecialchars($row['location']) . "</div>"
          . "<div><a href='edit_event.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning me-2'>Edit</a>"
          . "<a href='delete_event.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this event?\");'>Delete</a></div>"
          . "</li>";
    }
    ?>
    </ul>

    <h2>Attendee Registrations</h2>
    <ul class="list-group">
    <?php
    $sql = "SELECT r.*, e.title as event_name, u.name as attendee_name
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            JOIN users u ON r.attendee_id = u.id";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));

    while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
      echo "<li class='list-group-item'>" 
         . htmlspecialchars($r['attendee_name']) 
         . " registered for " 
         . htmlspecialchars($r['event_name']) 
         . "</li>";
    }
    ?>
    </ul>

    <?php elseif ($role === 'attendee'): ?>
    <h2>Events Available</h2>
    <ul class="list-group">
    <?php
    $sql = "SELECT * FROM events ORDER BY event_date";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) die(print_r(sqlsrv_errors(), true));

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
      echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
              <div>
                <strong>" . htmlspecialchars($row['title']) . "</strong> on " 
              . $row['event_date']->format('Y-m-d H:i') . "<br>
                Location: " . htmlspecialchars($row['location']) . "
              </div>
              <a href='register_event.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Register</a>
            </li>";
    }
    ?>
    </ul>

    <?php else: ?>
    <p><a href="login.php">Login</a> or <a href="register_user.php">Sign up as Attendee</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
