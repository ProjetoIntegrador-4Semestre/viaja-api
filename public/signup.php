<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(["status" => "error", "message" => "Method not allowed."]);
  exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Missing required fields."]);
  exit();
}

$name = trim($data['name']);
$email = trim($data['email']);
$password = $data['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Invalid email format."]);
  exit();
}

try {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
  $stmt->execute([$email]);

  if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(["status" => "error", "message" => "Email already taken."]);
    exit();
  }

  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

  $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  if ($insertStmt->execute([$name, $email, $hashedPassword])) {
    http_response_code(201);
    echo json_encode(["status" => "success", "message" => "User registered successfully."]);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Server transaction error."]);
}
