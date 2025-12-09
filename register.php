<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

// DB connection
$host = "sql313.infinityfree.com";
$user = "if0_40626247";
$pass = "oByQjd2iPx1SPLj";
$dbname = "if0_40626247_db_new";

$db = new mysqli($host, $user, $pass, $dbname);

if ($db->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Read POST body
$data = json_decode(file_get_contents("php://input"), true);

// InfinityFree blocks JSON → fallback to form-data
if (!$data || !is_array($data)) {
    $data = $_POST;
}

if (empty($data["name"]) || empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit();
}

$name = htmlspecialchars($data["name"]);
$email = htmlspecialchars($data["email"]);
$password = password_hash($data["password"], PASSWORD_DEFAULT);

// Insert user
$stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User registered!"]);
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit();
}
?>
