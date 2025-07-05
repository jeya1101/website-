<?php
$serverName = "server-admin.database.windows.net";
$connectionOptions = array(
    "Database" => "eventDB",
    "Uid" => "server",
    "PWD" => "Sathya@123",
    "Encrypt" => true,
    "TrustServerCertificate" => false
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>