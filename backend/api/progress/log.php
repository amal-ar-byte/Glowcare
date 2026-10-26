<?php
/**
 * Log Progress API Endpoint
 * POST /api/progress/log.php
 * 
 * Logs daily skincare progress (how the user's skin feels, notes, etc.).
 * Requires authentication.
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in to log your progress."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Read the JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Get progress data
$routine_step_id   = isset($data['routine_step_id']) ? (int) $data['routine_step_id'] : null;
$completion_status = $data['completion_status'] ?? 'completed';
$log_date          = $data['log_date'] ?? date('Y-m-d');
$notes             = trim($data['notes'] ?? '');
$skin_feeling      = $data['skin_feeling'] ?? null;

// Validate completion status
$valid_statuses = ['completed', 'skipped', 'partial'];
if (!in_array($completion_status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid completion status."]);
    exit();
}

// Validate skin feeling if provided
$valid_feelings = ['great', 'good', 'okay', 'bad', 'terrible'];
if ($skin_feeling && !in_array($skin_feeling, $valid_feelings)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid skin feeling value."]);
    exit();
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $log_date)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid date format. Use YYYY-MM-DD."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // If a routine_step_id is provided, verify it belongs to this user
    if ($routine_step_id) {
        $check_sql = "SELECT rs.step_id
                      FROM routine_steps rs
                      JOIN routines r ON rs.routine_id = r.routine_id
                      WHERE rs.step_id = :step_id AND r.user_id = :user_id";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':step_id', $routine_step_id, PDO::PARAM_INT);
        $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $check_stmt->execute();

        if ($check_stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Routine step not found."]);
            exit();
        }
    }

    // Insert progress log
    $sql = "INSERT INTO progress_logs (user_id, routine_step_id, completion_status, log_date, notes, skin_feeling)
            VALUES (:user_id, :routine_step_id, :completion_status, :log_date, :notes, :skin_feeling)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':routine_step_id', $routine_step_id, PDO::PARAM_INT);
    $stmt->bindParam(':completion_status', $completion_status);
    $stmt->bindParam(':log_date', $log_date);
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':skin_feeling', $skin_feeling);
    $stmt->execute();

    $log_id = $db->lastInsertId();

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Progress logged successfully!",
        "data" => ["log_id" => (int) $log_id]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to log progress."]);
}
