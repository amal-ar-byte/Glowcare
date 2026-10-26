<?php
/**
 * Update Routine API Endpoint
 * PUT /api/routines/update.php
 * 
 * Updates a routine's name/type and replaces its steps.
 * Requires authentication. Users can only update their own routines.
 */

// Start session
session_start();

// Include CORS headers and database connection
require_once '../../config/cors.php';
require_once '../../config/database.php';

// Only allow PUT requests
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use PUT."]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in to update your routine."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Read the JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Get routine ID (from query string or body)
$routine_id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($data['routine_id'] ?? 0);

if ($routine_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Routine ID is required."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Verify the routine belongs to this user (prevent accessing other users' data)
    $check_sql = "SELECT routine_id FROM routines WHERE routine_id = :routine_id AND user_id = :user_id";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Routine not found."]);
        exit();
    }

    // Begin transaction
    $db->beginTransaction();

    // Update routine details if provided
    $routine_name = trim($data['routine_name'] ?? '');
    $routine_type = $data['routine_type'] ?? '';

    if (!empty($routine_name) || !empty($routine_type)) {
        $update_parts = [];
        $params = [':routine_id' => $routine_id];

        if (!empty($routine_name)) {
            $update_parts[] = "routine_name = :routine_name";
            $params[':routine_name'] = $routine_name;
        }
        if (in_array($routine_type, ['morning', 'evening'])) {
            $update_parts[] = "routine_type = :routine_type";
            $params[':routine_type'] = $routine_type;
        }

        if (!empty($update_parts)) {
            $update_sql = "UPDATE routines SET " . implode(', ', $update_parts) . " WHERE routine_id = :routine_id";
            $update_stmt = $db->prepare($update_sql);
            $update_stmt->execute($params);
        }
    }

    // Replace steps if provided
    $steps = $data['steps'] ?? null;
    if (is_array($steps)) {
        // Delete existing steps
        $del_sql = "DELETE FROM routine_steps WHERE routine_id = :routine_id";
        $del_stmt = $db->prepare($del_sql);
        $del_stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        $del_stmt->execute();

        // Insert new steps
        $step_sql = "INSERT INTO routine_steps (routine_id, product_id, step_name, step_order)
                     VALUES (:routine_id, :product_id, :step_name, :step_order)";
        $step_stmt = $db->prepare($step_sql);

        foreach ($steps as $index => $step) {
            $step_name = trim($step['step_name'] ?? '');
            if (empty($step_name)) continue;

            $product_id = isset($step['product_id']) ? (int) $step['product_id'] : null;
            $step_order = isset($step['step_order']) ? (int) $step['step_order'] : ($index + 1);

            $step_stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
            $step_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $step_stmt->bindParam(':step_name', $step_name);
            $step_stmt->bindParam(':step_order', $step_order, PDO::PARAM_INT);
            $step_stmt->execute();
        }
    }

    // Commit transaction
    $db->commit();

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Routine updated successfully."]);

} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to update routine."]);
}
