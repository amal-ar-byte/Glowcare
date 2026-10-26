<?php
/**
 * CORS (Cross-Origin Resource Sharing) Configuration
 * 
 * Allows the React frontend (running on localhost:3000 or localhost:5173)
 * to communicate with this PHP backend.
 */

// Allow requests from the React dev server
header("Access-Control-Allow-Origin: http://localhost:3000");

// Allow these HTTP methods
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Allow these headers in requests
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Allow cookies/sessions to be sent cross-origin
header("Access-Control-Allow-Credentials: true");

// Set response content type to JSON
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
// Browsers send OPTIONS before actual requests to check CORS permissions
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
