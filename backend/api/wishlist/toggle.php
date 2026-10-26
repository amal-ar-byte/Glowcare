<?php
/**
 * Wishlist Toggle API Endpoint
 * POST /api/wishlist/toggle.php
 * 
 * Adds a product to the wishlist if not already there,
 * or removes it if it is (toggle behavior).
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
    echo json_encode(["success" => false, "message" => "Please log in to manage your wishlist."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Read the JSON data
$data = json_decode(file_get_contents("php://input"), true);

$product_id = isset($data['product_id']) ? (int) $data['product_id'] : 0;

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
    $check_product = $db->prepare("SELECT product_id FROM products WHERE product_id = :pid AND is_active = 1");
    $check_product->bindParam(':pid', $product_id, PDO::PARAM_INT);
    $check_product->execute();

    if ($check_product->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Product not found."]);
        exit();
    }

    // Check if already in wishlist
    $check_sql = "SELECT wishlist_id FROM wishlists
                  WHERE user_id = :user_id AND product_id = :product_id";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        // Product is in wishlist — REMOVE it
        $del_sql = "DELETE FROM wishlists WHERE user_id = :user_id AND product_id = :product_id";
        $del_stmt = $db->prepare($del_sql);
        $del_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $del_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $del_stmt->execute();

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Product removed from wishlist.",
            "data" => ["in_wishlist" => false]
        ]);

    } else {
        // Product is NOT in wishlist — ADD it
        $add_sql = "INSERT INTO wishlists (user_id, product_id) VALUES (:user_id, :product_id)";
        $add_stmt = $db->prepare($add_sql);
        $add_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $add_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $add_stmt->execute();

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Product added to wishlist!",
            "data" => ["in_wishlist" => true]
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to update wishlist."]);
}
