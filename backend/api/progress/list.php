<?php
/**
 * List Progress API Endpoint
 * GET /api/progress/list.php
 * 
 * Returns the user's progress history with optional date range filter.
 * Requires authentication.
 * 
 * Query Parameters:
 *   start_date - Filter from this date (YYYY-MM-DD)
 *   end_date   - Filter to this date (YYYY-MM-DD)
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
    echo json_encode(["success" => false, "message" => "Please log in to view your progress."]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Get optional date range filters
    $start_date = $_GET['start_date'] ?? null;
    $end_date   = $_GET['end_date'] ?? null;

    // Build query with optional date filters
    $sql = "SELECT pl.log_id, pl.routine_step_id, pl.completion_status,
                   pl.log_date, pl.notes, pl.skin_feeling, pl.created_at,
                   rs.step_name
            FROM progress_logs pl
            LEFT JOIN routine_steps rs ON pl.routine_step_id = rs.step_id
            WHERE pl.user_id = :user_id";

    $params = [':user_id' => $user_id];

    if ($start_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
        $sql .= " AND pl.log_date >= :start_date";
        $params[':start_date'] = $start_date;
    }

    if ($end_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        $sql .= " AND pl.log_date <= :end_date";
        $params[':end_date'] = $end_date;
    }

    $sql .= " ORDER BY pl.log_date DESC, pl.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $logs = $stmt->fetchAll();

    // Cast numeric fields
    foreach ($logs as &$log) {
        $log['log_id']          = (int) $log['log_id'];
        $log['routine_step_id'] = $log['routine_step_id'] ? (int) $log['routine_step_id'] : null;
    }

    http_response_code(200);
    echo json_encode(["success" => true, "data" => $logs]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch progress history."]);
}
