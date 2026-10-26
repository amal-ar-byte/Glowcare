<?php
/**
 * Wishlist List API Endpoint
 * GET /api/wishlist/list.php
 * 
 * Returns the user's wishlist with full product details.
 * Requires authentication.
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Please log in to view your wishlist."]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Get wishlist items with product and category details
    $sql = "SELECT w.wishlist_id, w.date_added,
                   p.product_id, p.name, p.price, p.description,
                   p.skin_type, p.image_path,
                   c.category_name
            FROM wishlists w
            JOIN products p ON w.product_id = p.product_id
            JOIN categories c ON p.category_id = c.category_id
            WHERE w.user_id = :user_id
            ORDER BY w.date_added DESC";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $wishlist = $stmt->fetchAll();

    // Cast numeric fields
    foreach ($wishlist as &$item) {
        $item['wishlist_id'] = (int) $item['wishlist_id'];
        $item['product_id']  = (int) $item['product_id'];
        $item['price']       = (float) $item['price'];
    }

    http_response_code(200);
    echo json_encode(["success" => true, "data" => $wishlist]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch wishlist."]);
}
