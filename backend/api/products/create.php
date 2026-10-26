<?php
/**
 * Create Product API Endpoint (Admin Only)
 * POST /api/products/create.php
 * 
 * Adds a new product to the catalogue.
 * Only accessible by admin users.
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

// Validate required fields
$name        = trim($data['name'] ?? '');
$category_id = isset($data['category_id']) ? (int) $data['category_id'] : 0;
$price       = isset($data['price']) ? (float) $data['price'] : -1;

if (empty($name)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Product name is required."]);
    exit();
}

if ($category_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Valid category is required."]);
    exit();
}

if ($price < 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Valid price is required."]);
    exit();
}

// Optional fields
$description        = trim($data['description'] ?? '');
$ingredients        = trim($data['ingredients'] ?? '');
$usage_instructions = trim($data['usage_instructions'] ?? '');
$precautions        = trim($data['precautions'] ?? '');
$skin_type          = $data['skin_type'] ?? 'all';
$image_path         = trim($data['image_path'] ?? '');

// Validate skin type
$valid_skin_types = ['oily', 'dry', 'combination', 'normal', 'sensitive', 'all'];
if (!in_array($skin_type, $valid_skin_types)) {
    $skin_type = 'all';
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Insert the product
    $sql = "INSERT INTO products (name, category_id, price, description, ingredients,
                                  usage_instructions, precautions, skin_type, image_path)
            VALUES (:name, :category_id, :price, :description, :ingredients,
                    :usage_instructions, :precautions, :skin_type, :image_path)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':ingredients', $ingredients);
    $stmt->bindParam(':usage_instructions', $usage_instructions);
    $stmt->bindParam(':precautions', $precautions);
    $stmt->bindParam(':skin_type', $skin_type);
    $stmt->bindParam(':image_path', $image_path);
    $stmt->execute();

    $product_id = $db->lastInsertId();

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Product created successfully!",
        "data" => ["product_id" => (int) $product_id]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to create product."]);
}
