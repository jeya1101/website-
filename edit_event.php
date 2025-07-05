<?php
session_start();
if ($_SESSION['role'] !== 'organizer') {
    die('Access denied');
}

include('db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid event ID.');
}

$id = (int)$_GET['id'];

// Fetch event
$sql = "SELECT * FROM events WHERE id = ?";
$params = array($id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$e = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$e) {
    die('Event not found.');
}

// Format date for input type="date" (Y-m-d)
$eventDate = $e['event_date']->format('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Event</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Edit Event</h2>
<form action="update_event.php" method="POST">
  <input type="hidden" name="id" value="<?= $e['id'] ?>">
  <input name="name" value="<?= htmlspecialchars($e['title']) ?>" required>
  <input type="date" name="date" value="<?= $eventDate ?>" required>
  <button>Update</button>
</form>
</body>
</html>
