<?php
/**
 * Admin - Manage Categories API Endpoint
 * GET    /api/admin/categories.php         - List all categories
 * POST   /api/admin/categories.php         - Create a new category
 * PUT    /api/admin/categories.php?id=123  - Update a category
 * DELETE /api/admin/categories.php?id=123  - Delete a category
 * 
 * Admin only — regular users cannot access this endpoint.
 */

// Start session
session_start();

// Include CORS headers and database connection
require_once '../../config/cors.php';
require_once '../../config/database.php';

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

// Connect to database
$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {

    // ============================================================
    // GET - List all categories
    // ============================================================
    if ($method === 'GET') {
        $sql = "SELECT c.category_id, c.category_name, c.description, c.created_at,
                       COUNT(p.product_id) AS product_count
                FROM categories c
                LEFT JOIN products p ON c.category_id = p.category_id AND p.is_active = 1
                GROUP BY c.category_id
                ORDER BY c.category_name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll();

        // Cast numeric fields
        foreach ($categories as &$cat) {
            $cat['category_id']   = (int) $cat['category_id'];
            $cat['product_count'] = (int) $cat['product_count'];
        }

        http_response_code(200);
        echo json_encode(["success" => true, "data" => $categories]);

    // ============================================================
    // POST - Create a new category
    // ============================================================
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        $category_name = trim($data['category_name'] ?? '');
        $description   = trim($data['description'] ?? '');

        if (empty($category_name)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Category name is required."]);
            exit();
        }

        // Check for duplicate category name
        $check_sql = "SELECT category_id FROM categories WHERE category_name = :name";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':name', $category_name);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["success" => false, "message" => "Category name already exists."]);
            exit();
        }

        $sql = "INSERT INTO categories (category_name, description)
                VALUES (:category_name, :description)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        $category_id = $db->lastInsertId();

        http_response_code(201);
        echo json_encode([
            "success" => true,
            "message" => "Category created successfully!",
            "data" => ["category_id" => (int) $category_id]
        ]);

    // ============================================================
    // PUT - Update a category
    // ============================================================
    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);
        $category_id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($data['category_id'] ?? 0);

        if ($category_id <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Category ID is required."]);
            exit();
        }

        $category_name = trim($data['category_name'] ?? '');
        $description   = trim($data['description'] ?? '');

        if (empty($category_name)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Category name is required."]);
            exit();
        }

        // Check for duplicate name (excluding current category)
        $check_sql = "SELECT category_id FROM categories
                      WHERE category_name = :name AND category_id != :id";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':name', $category_name);
        $check_stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["success" => false, "message" => "Category name already exists."]);
            exit();
        }

        $sql = "UPDATE categories
                SET category_name = :category_name, description = :description
                WHERE category_id = :category_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':category_name', $category_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Category not found."]);
            exit();
        }

        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Category updated successfully."]);

    // ============================================================
    // DELETE - Delete a category
    // ============================================================
    } elseif ($method === 'DELETE') {
        $category_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($category_id <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Category ID is required."]);
            exit();
        }

        // Check if category has products (prevent deletion if it does)
        $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = :id AND is_active = 1";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':id', $category_id, PDO::PARAM_INT);
        $check_stmt->execute();
        $product_count = (int) $check_stmt->fetch()['count'];

        if ($product_count > 0) {
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "Cannot delete category with {$product_count} active product(s). Remove products first."
            ]);
            exit();
        }

        $sql = "DELETE FROM categories WHERE category_id = :category_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Category not found."]);
            exit();
        }

        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Category deleted successfully."]);

    } else {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Operation failed. Please try again."]);
}
