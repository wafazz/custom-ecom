<?php
/**
 * REFACTORED FUNCTION SAMPLES
 * This file shows how to refactor the main patterns in function.php
 * 
 * Key improvements:
 * - Prepared statements (SQL injection prevention)
 * - Type hints and return types
 * - Consistent return patterns
 * - No direct echo statements
 * - Proper error handling
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ============================================================================
// EMAIL FUNCTIONS - REFACTORED
// ============================================================================

/**
 * Get configured PHPMailer instance for Brevo SMTP
 * 
 * @return PHPMailer
 * @throws Exception
 */
function getMailerBrevo(): PHPMailer
{
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = getenv('BREVO_SMTP_HOST') ?: 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('BREVO_SMTP_EMAIL');
        $mail->Password   = getenv('BREVO_SMTP_KEY');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = getenv('BREVO_SMTP_PORT') ?: 587;
        $mail->setFrom(getenv('NOREPLY_EMAIL') ?: 'noreply@rozeyana.com', 'Rozeyana');
        
        return $mail;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        throw $e;
    }
}

// ============================================================================
// DATABASE FUNCTIONS - REFACTORED
// ============================================================================

/**
 * Get category details by ID
 * 
 * @param int $id Category ID
 * @return array|null Category data or null if not found
 */
function getCategoryDetails(int $id): ?array
{
    $conn = getDbConnection();
    
    // ✅ SECURE: Using prepared statement
    $stmt = $conn->prepare("SELECT id, name, image, description FROM categories WHERE id = ? AND deleted_at IS NULL");
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return null;
    }
    
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return null;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if (!$row) {
        return null;
    }
    
    return [
        "id" => $row["id"],
        "name" => $row["name"],
        "image" => $row["image"],
        "description" => $row["description"]
    ];
}

/**
 * Get brand details by ID
 * 
 * @param int $id Brand ID
 * @return array|null Brand data or null if not found
 */
function getBrandDetails(int $id): ?array
{
    $conn = getDbConnection();
    
    // ✅ SECURE: Using prepared statement
    $stmt = $conn->prepare("SELECT id, name, image, description FROM brands WHERE id = ? AND deleted_at IS NULL");
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return null;
    }
    
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return null;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if (!$row) {
        return null;
    }
    
    return [
        "id" => $row["id"],
        "name" => $row["name"],
        "image" => $row["image"],
        "description" => $row["description"]
    ];
}

/**
 * Get cart count for current session
 * 
 * @return array Cart count data
 */
function getCartCount(): array
{
    $conn = getDbConnection();
    $sessionId = $_SESSION["session_id"] ?? null;
    
    if (!$sessionId) {
        return ["count" => 0];
    }
    
    // ✅ SECURE: Using prepared statement
    $stmt = $conn->prepare(
        "SELECT SUM(quantity) AS cartQTY FROM cart 
         WHERE session_id = ? AND deleted_at IS NULL AND status IN (0, 1)"
    );
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return ["count" => 0];
    }
    
    $stmt->bind_param("s", $sessionId);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return ["count" => 0];
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    $count = intval($row["cartQTY"]) ?? 0;
    
    return ["count" => max(0, $count)];
}

/**
 * Get total sales amount
 * 
 * @return float Total sales in MYR
 */
function getTotalSales(): float
{
    $conn = getDbConnection();
    
    // ✅ SECURE: Using prepared statement
    $stmt = $conn->prepare(
        "SELECT SUM(myr_value_include_postage) AS total_myr FROM customer_orders 
         WHERE status IN (1, 2, 3, 4) AND deleted_at IS NULL"
    );
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return 0.0;
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return 0.0;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    $total = floatval($row['total_myr']) ?? 0.0;
    return max(0, $total);
}

/**
 * Get total number of products
 * 
 * @return int Total product count
 */
function getTotalProducts(): int
{
    $conn = getDbConnection();
    
    // ✅ SECURE: Using COUNT in query
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE deleted_at IS NULL");
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return 0;
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return 0;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return intval($row['count']) ?? 0;
}

/**
 * Get total number of orders
 * 
 * @return int Total order count
 */
function getTotalOrders(): int
{
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM customer_orders WHERE deleted_at IS NULL");
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return 0;
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return 0;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return intval($row['count']) ?? 0;
}

/**
 * Get total number of returned orders
 * 
 * @return int Total returned order count
 */
function getTotalReturnedOrders(): int
{
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM customer_orders WHERE status = 5 AND deleted_at IS NULL");
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return 0;
    }
    
    $stmt->bind_param("i", $status = 5);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return 0;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return intval($row['count']) ?? 0;
}

/**
 * Role verification using prepared statement
 * 
 * @param string $url Page URL to check
 * @param int $id User ID
 * @return bool True if access allowed
 */
function roleVerify(string $url, int $id): bool
{
    $conn = getDbConnection();
    
    // ✅ SECURE: Using prepared statement
    $stmt = $conn->prepare(
        "SELECT id FROM role_access WHERE page_url = ? AND allowed_user LIKE ?"
    );
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    $pattern = "%[$id]%";
    $stmt->bind_param("ss", $url, $pattern);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result->num_rows > 0;
}

/**
 * Log activity to database
 * 
 * @param int $userid User ID
 * @param string $description Activity description
 * @param string $table Table name
 * @param string $activity Activity type
 * @return bool Success status
 */
function logActivity(int $userid, string $description, string $table, string $activity): bool
{
    $conn = getDbConnection();
    $now = dateNow();
    
    // ✅ SECURE: Using prepared statement
    $stmt = $conn->prepare(
        "INSERT INTO activities (user_id, created_at, updated_at, description, table_name, activities) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("isssss", $userid, $now, $now, $description, $table, $activity);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    $stmt->close();
    return true;
}

// ============================================================================
// UTILITY FUNCTIONS - REFACTORED
// ============================================================================

/**
 * Format date with proper return (not echo)
 * 
 * @param string $date Date string
 * @return string Formatted date
 */
function formatDate(string $date): string
{
    try {
        $newdate = new DateTime($date);
        return $newdate->format('j F, Y h:i A');
    } catch (Exception $e) {
        error_log("Date format error: " . $e->getMessage());
        return $date;
    }
}

/**
 * Get current date/time in Asia/Kuala_Lumpur timezone
 * 
 * @return string Current date-time in Y-m-d H:i:s format
 */
function dateNow(): string
{
    $timezone = new DateTimeZone('Asia/Kuala_Lumpur');
    $datetime = new DateTime('now', $timezone);
    return $datetime->format('Y-m-d H:i:s');
}

/**
 * Get current year in Asia/Kuala_Lumpur timezone
 * 
 * @return string Current year
 */
function currentYear(): string
{
    $timezone = new DateTimeZone('Asia/Kuala_Lumpur');
    $datetime = new DateTime('now', $timezone);
    return $datetime->format('Y');
}

/**
 * Get user's IP address
 * 
 * @return string User IP address
 */
function getUserIP(): string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function csrf_token(): string
{
    if (!isset($_SESSION['_token'])) {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid
 */
function verify_csrf(string $token): bool
{
    return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
}

// ============================================================================
// MIGRATION NOTES
// ============================================================================

/**
 * TO IMPLEMENT THESE CHANGES:
 * 
 * 1. Create .env file:
 *    BREVO_SMTP_HOST=smtp-relay.brevo.com
 *    BREVO_SMTP_EMAIL=your-email@example.com
 *    BREVO_SMTP_KEY=your-api-key
 *    BREVO_SMTP_PORT=587
 *    NOREPLY_EMAIL=noreply@rozeyana.com
 * 
 * 2. Install vlucas/phpdotenv via composer:
 *    composer require vlucas/phpdotenv
 * 
 * 3. Load .env at top of application:
 *    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
 *    $dotenv->load();
 * 
 * 4. Replace functions in production code gradually
 * 
 * 5. Test thoroughly before deploying
 */
