<?php
/**
 * Create Routine API Endpoint
 * POST /api/routines/create.php
 * 
 * Creates a new skincare routine with steps.
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
    echo json_encode(["success" => false, "message" => "Please log in to create a routine."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Read the JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
$routine_name = trim($data['routine_name'] ?? '');
$routine_type = $data['routine_type'] ?? '';
$steps        = $data['steps'] ?? [];

if (empty($routine_name)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Routine name is required."]);
    exit();
}

if (!in_array($routine_type, ['morning', 'evening'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Routine type must be 'morning' or 'evening'."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Use a transaction to ensure routine + steps are created together
    $db->beginTransaction();

    // Insert the routine
    $sql = "INSERT INTO routines (user_id, routine_name, routine_type)
            VALUES (:user_id, :routine_name, :routine_type)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':routine_name', $routine_name);
    $stmt->bindParam(':routine_type', $routine_type);
    $stmt->execute();

    $routine_id = $db->lastInsertId();

    // Insert the steps (if any were provided)
    if (!empty($steps) && is_array($steps)) {
        $step_sql = "INSERT INTO routine_steps (routine_id, product_id, step_name, step_order)
                     VALUES (:routine_id, :product_id, :step_name, :step_order)";
        $step_stmt = $db->prepare($step_sql);

        foreach ($steps as $index => $step) {
            $step_name = trim($step['step_name'] ?? '');
            if (empty($step_name)) continue; // Skip empty steps

            $product_id = isset($step['product_id']) ? (int) $step['product_id'] : null;
            $step_order = isset($step['step_order']) ? (int) $step['step_order'] : ($index + 1);

            $step_stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
            $step_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $step_stmt->bindParam(':step_name', $step_name);
            $step_stmt->bindParam(':step_order', $step_order, PDO::PARAM_INT);
            $step_stmt->execute();
        }
    }

    // Commit the transaction
    $db->commit();

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Routine created successfully!",
        "data" => [
            "routine_id"   => (int) $routine_id,
            "routine_name" => $routine_name,
            "routine_type" => $routine_type
        ]
    ]);

} catch (PDOException $e) {
    // Rollback on error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to create routine."]);
}
