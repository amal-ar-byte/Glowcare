<?php
/**
 * Delete Routine API Endpoint
 * DELETE /api/routines/delete.php?id=123
 * 
 * Deletes a routine and its steps (cascade).
 * Requires authentication. Users can only delete their own routines.
 */

// Start session
session_start();

// Include CORS headers and database connection
require_once '../../config/cors.php';
require_once '../../config/database.php';

// Only allow DELETE requests
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use DELETE."]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in to delete a routine."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get routine ID from query string
$routine_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($routine_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Routine ID is required."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Verify the routine belongs to this user
    $sql = "DELETE FROM routines WHERE routine_id = :routine_id AND user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Routine not found."]);
        exit();
    }

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Routine deleted successfully."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to delete routine."]);
}
