<?php
/**
 * Product Detail API Endpoint
 * GET /api/products/detail.php?id=123
 * 
 * Returns full details for a single product including category name.
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

    // Fetch product with category name
    $sql = "SELECT p.product_id, p.name, p.category_id, c.category_name,
                   p.price, p.description, p.ingredients, p.usage_instructions,
                   p.precautions, p.skin_type, p.image_path, p.is_active,
                   p.created_at, p.updated_at
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_id = :product_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Product not found."]);
        exit();
    }

    // Cast numeric fields
    $product['product_id']  = (int) $product['product_id'];
    $product['category_id'] = (int) $product['category_id'];
    $product['price']       = (float) $product['price'];
    $product['is_active']   = (bool) $product['is_active'];

    http_response_code(200);
    echo json_encode(["success" => true, "data" => $product]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch product details."]);
}
