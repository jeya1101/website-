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

// Delete event
$sql = "DELETE FROM events WHERE id = ?";
$params = array($id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Set success flash message
$_SESSION['success'] = "✅ Event deleted successfully!";
header('Location: Manage_Events.php');
exit;
?>
