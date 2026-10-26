<?php
/**
 * List Categories API Endpoint
 * GET /api/categories/list.php
 * 
 * Returns all product categories.
 * Public endpoint — no authentication required.
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

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Fetch all categories ordered by name
    $sql = "SELECT category_id, category_name, description, created_at
            FROM categories
            ORDER BY category_name ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $categories = $stmt->fetchAll();

    // Cast numeric fields
    foreach ($categories as &$cat) {
        $cat['category_id'] = (int) $cat['category_id'];
    }

    http_response_code(200);
    echo json_encode(["success" => true, "data" => $categories]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch categories."]);
}
