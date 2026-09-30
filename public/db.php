<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '6543';
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

try {
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
$pdo = new PDO($dsn, $username, $password);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
http_response_code(500);
echo json_encode(["status" => "error", "message" => "Database connection failed."]);
exit();
}