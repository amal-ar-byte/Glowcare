<?php
/**
 * Complete Routine Step API Endpoint
 * POST /api/routines/complete_step.php
 * 
 * Marks a routine step as completed for today.
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
    echo json_encode(["success" => false, "message" => "Please log in to complete a step."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Read the JSON data
$data = json_decode(file_get_contents("php://input"), true);

$step_id = isset($data['step_id']) ? (int) $data['step_id'] : 0;
$is_completed = isset($data['is_completed']) ? (bool) $data['is_completed'] : true;

if ($step_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Step ID is required."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Verify the step belongs to this user's routine
    $check_sql = "SELECT rs.step_id
                  FROM routine_steps rs
                  JOIN routines r ON rs.routine_id = r.routine_id
                  WHERE rs.step_id = :step_id AND r.user_id = :user_id";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':step_id', $step_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Step not found."]);
        exit();
    }

    // Update the step's completion status
    $today = date('Y-m-d');
    $completed_val = $is_completed ? 1 : 0;
    $completed_date = $is_completed ? $today : null;

    $sql = "UPDATE routine_steps
            SET is_completed = :is_completed, completed_date = :completed_date
            WHERE step_id = :step_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':is_completed', $completed_val, PDO::PARAM_INT);
    $stmt->bindParam(':completed_date', $completed_date);
    $stmt->bindParam(':step_id', $step_id, PDO::PARAM_INT);
    $stmt->execute();

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => $is_completed ? "Step completed!" : "Step marked as incomplete.",
        "data" => [
            "step_id"       => $step_id,
            "is_completed"  => $is_completed,
            "completed_date" => $completed_date
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to update step."]);
}
