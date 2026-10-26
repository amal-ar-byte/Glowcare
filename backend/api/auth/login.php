<?php
/**
 * Login API Endpoint
 * POST /api/auth/login.php
 * 
 * Authenticates a user with email and password.
 * Creates a session on successful login.
 */

// Start session
session_start();

// Include CORS headers and database connection
require_once '../../config/cors.php';
require_once '../../config/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use POST."]);
    exit();
}

// Read the JSON data sent from frontend
$data = json_decode(file_get_contents("php://input"), true);

// Get email and password from request
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validate inputs
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email and password are required."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Find user by email
    $sql = "SELECT user_id, full_name, email, password_hash, role, profile_image
            FROM users WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Check if user exists
    if ($stmt->rowCount() === 0) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        exit();
    }

    $user = $stmt->fetch();

    // Verify the password against the stored hash
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        exit();
    }

    // Password is correct — create session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['role']    = $user['role'];

    // Return user data (never return the password hash!)
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Login successful!",
        "data" => [
            "user_id"       => (int) $user['user_id'],
            "full_name"     => $user['full_name'],
            "email"         => $user['email'],
            "role"          => $user['role'],
            "profile_image" => $user['profile_image']
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Login failed. Please try again."]);
}
