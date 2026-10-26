<?php
/**
 * Weekly Progress Summary API Endpoint
 * GET /api/progress/weekly.php
 * 
 * Returns weekly progress summary data for charts.
 * Groups logs by week and shows completion rates.
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
    echo json_encode(["success" => false, "message" => "Please log in to view your weekly summary."]);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Get weekly summary using the database view or direct query
    // Shows the last 8 weeks of progress
    $sql = "SELECT
                YEARWEEK(pl.log_date, 1) AS year_week,
                MIN(pl.log_date) AS week_start,
                MAX(pl.log_date) AS week_end,
                COUNT(*) AS total_logs,
                SUM(CASE WHEN pl.completion_status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
                SUM(CASE WHEN pl.completion_status = 'skipped' THEN 1 ELSE 0 END) AS skipped_count,
                SUM(CASE WHEN pl.completion_status = 'partial' THEN 1 ELSE 0 END) AS partial_count
            FROM progress_logs pl
            WHERE pl.user_id = :user_id
              AND pl.log_date >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
            GROUP BY YEARWEEK(pl.log_date, 1)
            ORDER BY year_week ASC";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $weeks = $stmt->fetchAll();

    // Calculate completion rate for each week
    foreach ($weeks as &$week) {
        $week['total_logs']      = (int) $week['total_logs'];
        $week['completed_count'] = (int) $week['completed_count'];
        $week['skipped_count']   = (int) $week['skipped_count'];
        $week['partial_count']   = (int) $week['partial_count'];

        // Completion rate as percentage
        $week['completion_rate'] = $week['total_logs'] > 0
            ? round(($week['completed_count'] / $week['total_logs']) * 100, 1)
            : 0;
    }

    // Also get skin feeling distribution for the current week
    $feeling_sql = "SELECT skin_feeling, COUNT(*) as count
                    FROM progress_logs
                    WHERE user_id = :user_id
                      AND skin_feeling IS NOT NULL
                      AND log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    GROUP BY skin_feeling";
    $feeling_stmt = $db->prepare($feeling_sql);
    $feeling_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $feeling_stmt->execute();
    $feelings = $feeling_stmt->fetchAll();

    foreach ($feelings as &$f) {
        $f['count'] = (int) $f['count'];
    }

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "data" => [
            "weekly_summary"   => $weeks,
            "current_feelings" => $feelings
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to fetch weekly summary."]);
}
