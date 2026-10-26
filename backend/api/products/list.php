<?php
/**
 * List Products API Endpoint
 * GET /api/products/list.php
 * 
 * Returns a paginated list of products with optional filters.
 * Public endpoint — no authentication required.
 * 
 * Query Parameters:
 *   category   - Filter by category_id
 *   skin_type  - Filter by skin type
 *   search     - Search in product name and description
 *   sort       - Sort order: price_asc, price_desc, name_asc, name_desc, newest
 *   page       - Page number (default: 1)
 *   limit      - Items per page (default: 12)
 */

// Start session (optional for this endpoint)
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

    // ---- Read filter parameters ----
    $category  = isset($_GET['category']) ? (int) $_GET['category'] : null;
    $skin_type = $_GET['skin_type'] ?? null;
    $search    = trim($_GET['search'] ?? '');
    $sort      = $_GET['sort'] ?? 'newest';
    $page      = max(1, (int) ($_GET['page'] ?? 1));
    $limit     = min(50, max(1, (int) ($_GET['limit'] ?? 12)));
    $offset    = ($page - 1) * $limit;

    // ---- Build the WHERE clause dynamically ----
    $where_clauses = ["p.is_active = 1"];
    $params = [];

    if ($category) {
        $where_clauses[] = "p.category_id = :category";
        $params[':category'] = $category;
    }

    $valid_skin_types = ['oily', 'dry', 'combination', 'normal', 'sensitive', 'all'];
    if ($skin_type && in_array($skin_type, $valid_skin_types)) {
        // Show products for the specific skin type OR products for 'all' skin types
        $where_clauses[] = "(p.skin_type = :skin_type OR p.skin_type = 'all')";
        $params[':skin_type'] = $skin_type;
    }

    if (!empty($search)) {
        $where_clauses[] = "(p.name LIKE :search OR p.description LIKE :search2)";
        $params[':search'] = "%{$search}%";
        $params[':search2'] = "%{$search}%";
    }

    $where_sql = implode(' AND ', $where_clauses);

    // ---- Sort order ----
    $order_sql = match ($sort) {
        'price_asc'  => "p.price ASC",
        'price_desc' => "p.price DESC",
        'name_asc'   => "p.name ASC",
        'name_desc'  => "p.name DESC",
        default      => "p.created_at DESC",  // newest
    };

    // ---- Count total results (for pagination info) ----
    $count_sql = "SELECT COUNT(*) as total FROM products p WHERE {$where_sql}";
    $count_stmt = $db->prepare($count_sql);
    $count_stmt->execute($params);
    $total = (int) $count_stmt->fetch()['total'];

    // ---- Fetch products with category name ----
    $sql = "SELECT p.product_id, p.name, p.category_id, c.category_name,
                   p.price, p.description, p.skin_type, p.image_path,
                   p.created_at
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            WHERE {$where_sql}
            ORDER BY {$order_sql}
            LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll();

    // Cast numeric fields
    foreach ($products as &$product) {
        $product['product_id']  = (int) $product['product_id'];
        $product['category_id'] = (int) $product['category_id'];
        $product['price']       = (float) $product['price'];
    }

    // Return products with pagination metadata
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => $products,
        "pagination" => [
            "current_page" => $page,
            "per_page"     => $limit,
            "total_items"  => $total,
            "total_pages"  => ceil($total / $limit)
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch products."]);
}
