<?php
/**
 * Save Skin Assessment API Endpoint
 * POST /api/assessment/save.php
 * 
 * Saves a skin assessment and determines skin type based on answers.
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
    echo json_encode(["success" => false, "message" => "Please log in to save your assessment."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Read the JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Get assessment answers (supports both new diagnostic keys and legacy keys)
$raw_oil         = $data['q_oil'] ?? ($data['oily_feeling'] ?? '');
$raw_dry         = $data['q_dryness'] ?? ($data['dry_tight'] ?? '');
$raw_irritation  = $data['q_irritation'] ?? ($data['easily_irritated'] ?? '');
$concerns_raw    = $data['concerns'] ?? '';
$concerns        = is_array($concerns_raw) ? implode(',', $concerns_raw) : trim($concerns_raw);

// ============================================================
// Skin Type Determination Logic
// Accurately evaluates primary skin barrier profile
// ============================================================
function determineSkinType($oil, $dry, $irritated, $concerns_arr) {
    // 1. Combination skin: T-zone shine with normal cheeks OR tight cheeks with normal T-zone OR mixed answers
    if (
        $oil === 'afternoon' ||
        $dry === 'cheeks_only' ||
        (($oil === 'always_oily' || $oil === 'yes') && $dry === 'cheeks_only') ||
        (($oil === 'yes' || $oil === 'sometimes') && ($dry === 'yes' || $dry === 'sometimes'))
    ) {
        return 'combination';
    }

    // 2. Oily skin: Consistently oily / shiny across face, or oily with acne/breakouts
    if (
        $oil === 'always_oily' ||
        ($oil === 'yes' && $dry === 'no') ||
        ($oil === 'yes' && (in_array('acne', $concerns_arr) || in_array('large_pores', $concerns_arr)))
    ) {
        return 'oily';
    }

    // 3. Dry skin: Tight after wash, or never oily / visible flakiness
    if (
        $oil === 'never' ||
        ($dry === 'yes_tight' && $oil !== 'always_oily' && $oil !== 'afternoon') ||
        ($dry === 'yes' && $oil === 'no')
    ) {
        return 'dry';
    }

    // 4. Sensitive skin: Very easily reactive / burning / redness
    if ($irritated === 'easily' || $irritated === 'yes') {
        return 'sensitive';
    }

    // 5. Normal skin: Balanced & comfortable with minimal oil and no tight sensation
    if ($oil === 'comfortable' || ($oil === 'no' && $dry === 'no')) {
        return 'normal';
    }

    return 'normal';
}

$concerns_arr = is_array($concerns_raw) ? $concerns_raw : ($concerns_raw ? explode(',', $concerns_raw) : []);
$skin_type = determineSkinType($raw_oil, $raw_dry, $raw_irritation, $concerns_arr);

// Map to MySQL ENUM('yes','no','sometimes') to maintain strict RDBMS schema compatibility
$oily_feeling = 'no';
if ($raw_oil === 'always_oily' || $raw_oil === 'yes') {
    $oily_feeling = 'yes';
} elseif ($raw_oil === 'afternoon' || $raw_oil === 'sometimes') {
    $oily_feeling = 'sometimes';
}

$dry_tight = 'no';
if ($raw_dry === 'yes_tight' || $raw_dry === 'yes') {
    $dry_tight = 'yes';
} elseif ($raw_dry === 'cheeks_only' || $raw_dry === 'sometimes') {
    $dry_tight = 'sometimes';
}

$easily_irritated = 'no';
if ($raw_irritation === 'easily' || $raw_irritation === 'yes') {
    $easily_irritated = 'yes';
} elseif ($raw_irritation === 'sometimes') {
    $easily_irritated = 'sometimes';
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // Insert the assessment
    $sql = "INSERT INTO skin_assessments (user_id, skin_type, oily_feeling, dry_tight, easily_irritated, concerns)
            VALUES (:user_id, :skin_type, :oily_feeling, :dry_tight, :easily_irritated, :concerns)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':skin_type', $skin_type);
    $stmt->bindParam(':oily_feeling', $oily_feeling);
    $stmt->bindParam(':dry_tight', $dry_tight);
    $stmt->bindParam(':easily_irritated', $easily_irritated);
    $stmt->bindParam(':concerns', $concerns);
    $stmt->execute();

    $assessment_id = $db->lastInsertId();

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Assessment saved successfully!",
        "data" => [
            "assessment_id" => (int) $assessment_id,
            "skin_type"     => $skin_type,
            "oily_feeling"  => $oily_feeling,
            "dry_tight"     => $dry_tight,
            "easily_irritated" => $easily_irritated,
            "concerns"      => $concerns
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to save assessment: " . $e->getMessage()]);
}
