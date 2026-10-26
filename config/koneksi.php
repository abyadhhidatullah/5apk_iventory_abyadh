<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$database = 'iventory';
$username = 'root';
$password = '';
$port = 3306;

try {
    $koneksi = new mysqli($host, $username, $password, $database, $port);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log('IVENTORI database connection error: ' . $exception->getMessage());
    http_response_code(500);
    exit('Koneksi database gagal. Pastikan MySQL aktif, database iventory tersedia, dan pengaturan di config/koneksi.php benar.');
}
