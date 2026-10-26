<?php
/**
 * List Routines API Endpoint
 * GET /api/routines/list.php
 * 
 * Returns all routines for the logged-in user with their steps.
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
    echo json_encode(["success" => false, "message" => "Please log in to view your routines."]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Optional filter by routine type (morning/evening)
    $type_filter = $_GET['type'] ?? null;

    // Get all routines for this user
    $sql = "SELECT routine_id, routine_name, routine_type, created_at, updated_at
            FROM routines
            WHERE user_id = :user_id";

    if ($type_filter && in_array($type_filter, ['morning', 'evening'])) {
        $sql .= " AND routine_type = :type";
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    if ($type_filter && in_array($type_filter, ['morning', 'evening'])) {
        $stmt->bindParam(':type', $type_filter);
    }
    $stmt->execute();

    $routines = $stmt->fetchAll();

    // For each routine, get its steps with product names
    foreach ($routines as &$routine) {
        $routine['routine_id'] = (int) $routine['routine_id'];

        $step_sql = "SELECT rs.step_id, rs.product_id, rs.step_name, rs.step_order,
                            rs.is_completed, rs.completed_date, p.name AS product_name
                     FROM routine_steps rs
                     LEFT JOIN products p ON rs.product_id = p.product_id
                     WHERE rs.routine_id = :routine_id
                     ORDER BY rs.step_order ASC";

        $step_stmt = $db->prepare($step_sql);
        $step_stmt->bindParam(':routine_id', $routine['routine_id'], PDO::PARAM_INT);
        $step_stmt->execute();

        $steps = $step_stmt->fetchAll();

        // Cast numeric fields in steps
        foreach ($steps as &$step) {
            $step['step_id'] = (int) $step['step_id'];
            $step['product_id'] = $step['product_id'] ? (int) $step['product_id'] : null;
            $step['step_order'] = (int) $step['step_order'];
            $step['is_completed'] = (bool) $step['is_completed'];
        }

        $routine['steps'] = $steps;
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $routines
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch routines."]);
}
