<?php
session_start();
include('db.php');

// Optional: restrict to organizers only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    die('Access denied.');
}

// Fetch attendees
$sql = "
    SELECT a.id, a.name AS attendee_name, a.contact AS attendee_contact, e.title AS event_title, 
           a.payment_status, a.bank, a.created_at
    FROM attendees a
    JOIN events e ON a.event_id = e.id
    ORDER BY a.created_at DESC
";
$stmt = sqlsrv_query($conn, $sql);

if (!$stmt) {
    die('Query failed.');
}

// Send headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=attendees_list_' . date('Ymd_His') . '.csv');

// Open PHP output stream
$output = fopen('php://output', 'w');

// CSV headers
fputcsv($output, ['ID', 'Attendee Name', 'Contact', 'Event Title', 'Payment Status', 'Bank', 'Registered At']);

// Loop and output data
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $createdAt = $row['created_at'] instanceof DateTime ? $row['created_at']->format('Y-m-d H:i') : '';
    fputcsv($output, [
        $row['id'],
        $row['attendee_name'],
        $row['attendee_contact'],
        $row['event_title'],
        $row['payment_status'],
        $row['bank'],
        $createdAt
    ]);
}

fclose($output);
exit;
?>
