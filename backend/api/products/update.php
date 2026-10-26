<?php
/**
 * Update Product API Endpoint (Admin Only)
 * PUT /api/products/update.php?id=123
 * 
 * Updates an existing product.
 * Only accessible by admin users.
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
    echo json_encode(["success" => false, "message" => "Please log in."]);
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Access denied. Admin only."]);
    exit();
}

// Read the JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Get product ID
$product_id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($data['product_id'] ?? 0);

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Product ID is required."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Check if product exists
    $check_sql = "SELECT product_id FROM products WHERE product_id = :product_id";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Product not found."]);
        exit();
    }

    // Build dynamic update query (only update fields that were sent)
    $update_parts = [];
    $params = [':product_id' => $product_id];

    // Map of field => type for validation
    $fields = [
        'name'               => 'string',
        'category_id'        => 'int',
        'price'              => 'float',
        'description'        => 'string',
        'ingredients'        => 'string',
        'usage_instructions' => 'string',
        'precautions'        => 'string',
        'skin_type'          => 'string',
        'image_path'         => 'string',
        'is_active'          => 'bool'
    ];

    foreach ($fields as $field => $type) {
        if (isset($data[$field])) {
            $update_parts[] = "{$field} = :{$field}";
            $value = $data[$field];

            // Cast values to correct types
            if ($type === 'int') $value = (int) $value;
            elseif ($type === 'float') $value = (float) $value;
            elseif ($type === 'bool') $value = (int) (bool) $value;
            else $value = trim($value);

            $params[":{$field}"] = $value;
        }
    }

    if (empty($update_parts)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "No fields to update."]);
        exit();
    }

    $sql = "UPDATE products SET " . implode(', ', $update_parts) . " WHERE product_id = :product_id";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Product updated successfully."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to update product."]);
}
