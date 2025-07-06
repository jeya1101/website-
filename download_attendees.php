<?php
session_start();
include('db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    die('Access denied.');
}

// Correct query using registrations, users, events
$sql = "
    SELECT 
        r.id AS reg_id, 
        u.name AS attendee_name, 
        u.contact AS attendee_contact, 
        e.title AS event_title,
        r.payment_status,
        r.bank,
        r.created_at
    FROM registrations r
    JOIN users u ON r.attendee_id = u.id
    JOIN events e ON r.event_id = e.id
    ORDER BY r.created_at DESC
";
$stmt = sqlsrv_query($conn, $sql);

if (!$stmt) {
    die(print_r(sqlsrv_errors(), true)); // will print actual SQL error
}

// Send headers to force download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=attendees_list_' . date('Ymd_His') . '.csv');

// Open output stream
$output = fopen('php://output', 'w');

// CSV headers
fputcsv($output, ['ID', 'Attendee Name', 'Contact', 'Event Title', 'Payment Status', 'Bank', 'Registered At']);

// Output data
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $createdAt = $row['created_at'] instanceof DateTime ? $row['created_at']->format('Y-m-d H:i') : '';
    fputcsv($output, [
        $row['reg_id'],
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
