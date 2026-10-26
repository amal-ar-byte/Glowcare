<?php
/**
 * Delete Product API Endpoint (Admin Only)
 * DELETE /api/products/delete_product.php?id=123
 * 
 * Soft-deletes a product by setting is_active = 0.
 * Only accessible by admin users.
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
    echo json_encode(["success" => false, "message" => "Please log in."]);
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Access denied. Admin only."]);
    exit();
}

// Get product ID from query string
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Product ID is required."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Soft delete: set is_active to 0 instead of removing the row
    // This preserves data integrity for existing orders/routines
    $sql = "UPDATE products SET is_active = 0 WHERE product_id = :product_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Product not found."]);
        exit();
    }

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Product deleted successfully."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to delete product."]);
}
