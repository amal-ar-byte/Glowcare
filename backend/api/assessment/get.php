<?php
/**
 * Get Skin Assessment API Endpoint
 * GET /api/assessment/get.php
 * 
 * Returns the user's latest skin assessment.
 * Requires authentication.
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
    echo json_encode(["success" => false, "message" => "Please log in to view your assessment."]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Get the latest assessment for this user (ordered by date, limit 1)
    $sql = "SELECT assessment_id, skin_type, oily_feeling, dry_tight,
                   easily_irritated, concerns, assessment_date
            FROM skin_assessments
            WHERE user_id = :user_id
            ORDER BY assessment_date DESC
            LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $assessment = $stmt->fetch();

    if (!$assessment) {
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "No assessment found. Please take the skin quiz.",
            "data" => null
        ]);
        exit();
    }

    $assessment['assessment_id'] = (int) $assessment['assessment_id'];

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $assessment
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch assessment."]);
}
