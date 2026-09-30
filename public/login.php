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

if (empty($data['email']) || empty($data['password'])) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Email and password required."]);
  exit();
}

$email = trim($data['email']);
$password = $data['password'];

try {
  $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ? LIMIT 1");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    http_response_code(200);
    echo json_encode([
      "status" => "success",
      "message" => "Login successful.",
      "user" => [
        "id" => $user['id'],
        "email" => $user['email']
      ]
    ]);
  } else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid credentials."]);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Server transaction error."]);
}
