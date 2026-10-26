<?php
/**
 * Admin - List Users API Endpoint
 * GET /api/admin/users.php
 * 
 * Returns a list of all registered users.
 * Admin only — regular users cannot access this.
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in."]);
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Access denied. Admin only."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Fetch all users (never return password hashes!)
    $sql = "SELECT u.user_id, u.full_name, u.email, u.phone, u.date_of_birth,
                   u.gender, u.role, u.profile_image, u.created_at, u.updated_at,
                   sa.skin_type
            FROM users u
            LEFT JOIN skin_assessments sa ON u.user_id = sa.user_id
                AND sa.assessment_date = (
                    SELECT MAX(sa2.assessment_date)
                    FROM skin_assessments sa2
                    WHERE sa2.user_id = u.user_id
                )
            ORDER BY u.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll();

    // Cast numeric fields
    foreach ($users as &$user) {
        $user['user_id'] = (int) $user['user_id'];
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $users,
        "total" => count($users)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch users."]);
}
