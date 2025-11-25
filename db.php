<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sim_tugas";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($host, $user, $pass, $db);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
