<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

$host = "sql313.infinityfree.com";
$user = "if0_40626247";
$pass = "oByQjd2iPx1SPLj";
$dbname = "if0_40626247_db_new";

$db = new mysqli($host, $user, $pass, $dbname);

if ($db->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Read JSON or form-data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !is_array($data)) {
    $data = $_POST;
}

if (empty($data["email"]) || empty($data["password"])) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit();
}

$email = $data["email"];
$password = $data["password"];

$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (!$userData) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit();
}

if (password_verify($password, $userData["password"])) {

    // Generate login token
    $token = bin2hex(random_bytes(16));

    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "user" => [
            "id" => $userData["id"],
            "name" => $userData["name"],
            "email" => $userData["email"]
        ],
        "token" => $token
    ]);
    exit();

} else {
    echo json_encode(["status" => "error", "message" => "Invalid password"]);
    exit()
}

?>
