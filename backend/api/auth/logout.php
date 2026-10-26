<?php
/**
 * Logout API Endpoint
 * POST /api/auth/logout.php
 * 
 * Destroys the user session to log them out.
 */

// Start session so we can destroy it
session_start();

// Include CORS headers
require_once '../../config/cors.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed. Use POST."]);
    exit();
}

// Clear all session data
$_SESSION = [];

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Return success
http_response_code(200);
echo json_encode([
    "success" => true,
    "message" => "Logged out successfully."
]);
