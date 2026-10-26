<?php
/**
 * User Profile API Endpoint
 * GET  /api/users/profile.php - Get user profile
 * PUT  /api/users/profile.php - Update user profile
 * 
 * Requires authentication. Users can only access their own profile.
 */

// Start session
session_start();

// Include CORS headers and database connection
require_once '../../config/cors.php';
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in to access your profile."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Connect to database
$database = new Database();
$db = $database->getConnection();

// ============================================================
// GET - Fetch user profile
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $sql = "SELECT user_id, full_name, email, phone, date_of_birth, gender,
                       role, profile_image, created_at, updated_at
                FROM users WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch();

        if (!$user) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "User not found."]);
            exit();
        }

        // Cast numeric fields
        $user['user_id'] = (int) $user['user_id'];

        http_response_code(200);
        echo json_encode(["success" => true, "data" => $user]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to fetch profile."]);
    }

// ============================================================
// PUT - Update user profile
// ============================================================
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Fields that can be updated
    $full_name = trim($data['full_name'] ?? '');
    $phone     = trim($data['phone'] ?? '');
    $dob       = $data['date_of_birth'] ?? null;
    $gender    = $data['gender'] ?? null;
    $profile_image = $data['profile_image'] ?? null;

    // Validate full name
    if (empty($full_name)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Full name is required."]);
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
        $sql = "UPDATE users
                SET full_name = :full_name,
                    phone = :phone,
                    date_of_birth = :dob,
                    gender = :gender,
                    profile_image = :profile_image
                WHERE user_id = :user_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone', $phone ?: null);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':profile_image', $profile_image);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Profile updated successfully."]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update profile."]);
    }

} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use GET or PUT."]);
}
