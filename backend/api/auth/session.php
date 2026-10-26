<?php
/**
 * Session Check API Endpoint
 * GET /api/auth/session.php
 * 
 * Checks if the user is currently logged in.
 * Returns user data if session is active.
 * Used by the React frontend to persist login state.
 */

// Start session
session_start();

// Include CORS headers and database connection
require_once '../../config/cors.php';
require_once '../../config/database.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use GET."]);
    exit();
}

// Check if user is logged in (session has user_id)
if (!isset($_SESSION['user_id'])) {
    http_response_code(200);
    echo json_encode([
        "success" => false,
        "message" => "Not logged in.",
        "data" => null
    ]);
    exit();
}

try {
    // Connect to database to get fresh user data
    $database = new Database();
    $db = $database->getConnection();

    $sql = "SELECT user_id, full_name, email, role, profile_image
            FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user) {
        // User was deleted but session still exists — clean up
        session_destroy();
        http_response_code(200);
        echo json_encode(["success" => false, "message" => "Session expired.", "data" => null]);
        exit();
    }

    // Return current user data
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Session active.",
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
    echo json_encode(["success" => false, "message" => "Could not verify session."]);
}
