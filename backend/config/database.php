<?php
/**
 * Database Configuration & Connection Class
 * 
 * Uses PDO (PHP Data Objects) for secure database access.
 * Configured for XAMPP default MySQL settings.
 */

class Database {
    // Database connection parameters
    private $host = "localhost";
    private $db_name = "glowcare";
    private $username = "root";
    private $password = "";  // Default XAMPP has no password

    // PDO connection instance
    private $conn = null;

    /**
     * Get the database connection
     * Creates a new PDO connection if one doesn't exist
     * 
     * @return PDO The database connection object
     */
    public function getConnection() {
        try {
            // Create new PDO connection with UTF-8 support
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );

            // Set PDO to throw exceptions on errors (helps with debugging)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Return results as associative arrays by default
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Disable emulated prepared statements for real security
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } catch (PDOException $e) {
            // Never expose database credentials in error messages
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Database connection failed. Please try again later."
            ]);
            exit();
        }

        return $this->conn;
    }
}
