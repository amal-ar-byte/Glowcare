<?php
/**
 * Admin - Dashboard Statistics API Endpoint
 * GET /api/admin/stats.php
 * 
 * Returns dashboard statistics for the admin panel:
 * total users, products, assessments, etc.
 * Admin only.
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
    echo json_encode(["success" => false, "message" => "Please log in."]);
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Access denied. Admin only."]);
    exit();
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Total users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $total_users = (int) $stmt->fetch()['count'];

    // Total products (active only)
    $stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
    $total_products = (int) $stmt->fetch()['count'];

    // Total categories
    $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
    $total_categories = (int) $stmt->fetch()['count'];

    // Total assessments
    $stmt = $db->query("SELECT COUNT(*) as count FROM skin_assessments");
    $total_assessments = (int) $stmt->fetch()['count'];

    // Total routines
    $stmt = $db->query("SELECT COUNT(*) as count FROM routines");
    $total_routines = (int) $stmt->fetch()['count'];

    // Total progress logs
    $stmt = $db->query("SELECT COUNT(*) as count FROM progress_logs");
    $total_logs = (int) $stmt->fetch()['count'];

    // Total wishlist items
    $stmt = $db->query("SELECT COUNT(*) as count FROM wishlists");
    $total_wishlists = (int) $stmt->fetch()['count'];

    // New users this month
    $stmt = $db->query("SELECT COUNT(*) as count FROM users
                        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
                        AND YEAR(created_at) = YEAR(CURRENT_DATE())");
    $new_users_month = (int) $stmt->fetch()['count'];

    // Skin type distribution
    $stmt = $db->query("SELECT skin_type, COUNT(*) as count
                        FROM skin_assessments
                        GROUP BY skin_type
                        ORDER BY count DESC");
    $skin_types = $stmt->fetchAll();
    foreach ($skin_types as &$st) {
        $st['count'] = (int) $st['count'];
    }

    // Recent users (last 5)
    $stmt = $db->query("SELECT user_id, full_name, email, created_at
                        FROM users ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt->fetchAll();
    foreach ($recent_users as &$ru) {
        $ru['user_id'] = (int) $ru['user_id'];
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => [
            "total_users"       => $total_users,
            "total_products"    => $total_products,
            "total_categories"  => $total_categories,
            "total_assessments" => $total_assessments,
            "total_routines"    => $total_routines,
            "total_logs"        => $total_logs,
            "total_wishlists"   => $total_wishlists,
            "new_users_month"   => $new_users_month,
            "skin_type_distribution" => $skin_types,
            "recent_users"      => $recent_users
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch statistics."]);
}
