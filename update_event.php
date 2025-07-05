<?php
session_start();
if ($_SESSION['role'] !== 'organizer') {
    die('Access denied');
}

include('db.php');

$id = $_POST['id'];
$name = $_POST['name'];
$date = $_POST['date'];

$sql = "UPDATE events SET title = ?, event_date = ? WHERE id = ?";
$params = array($name, $date, $id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

header('Location: index.php');
exit;
?>
