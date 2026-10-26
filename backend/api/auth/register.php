<?php
/**
 * Register API Endpoint
 * POST /api/auth/register.php
 * 
 * Creates a new user account with hashed password.
 * Validates all required fields before inserting.
 */

// Start session for authentication
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

// ---- Validate required fields ----
$full_name = trim($data['full_name'] ?? '');
$email     = trim($data['email'] ?? '');
$password  = $data['password'] ?? '';
$phone     = trim($data['phone'] ?? '');
$dob       = $data['date_of_birth'] ?? null;
$gender    = $data['gender'] ?? null;

// Check that required fields are not empty
if (empty($full_name) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Full name, email, and password are required."]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Please enter a valid email address."]);
    exit();
}

// Password must be at least 6 characters
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Password must be at least 6 characters long."]);
    exit();
}

// Validate gender if provided
$valid_genders = ['Male', 'Female', 'Other', 'Prefer not to say'];
if ($gender && !in_array($gender, $valid_genders)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid gender value."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Check if email already exists
    $check_sql = "SELECT user_id FROM users WHERE email = :email";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        http_response_code(409); // Conflict
        echo json_encode(["success" => false, "message" => "An account with this email already exists."]);
        exit();
    }

    // Hash the password securely using bcrypt
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (full_name, email, password_hash, phone, date_of_birth, gender)
            VALUES (:full_name, :email, :password_hash, :phone, :dob, :gender)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':phone', $phone ?: null);
    $stmt->bindParam(':dob', $dob);
    $stmt->bindParam(':gender', $gender);
    $stmt->execute();

    // Get the new user's ID
    $user_id = $db->lastInsertId();

    // Auto-login: Set session after registration
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = 'user';

    // Return success response
    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Account created successfully!",
        "data" => [
            "user_id"   => (int) $user_id,
            "full_name" => $full_name,
            "email"     => $email,
            "role"      => "user"
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Registration failed. Please try again."]);
}
