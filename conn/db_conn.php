<?php
// conn/db_conn.php

require_once __DIR__ . '/config.php';

class Database {
    private $conn;
    private static $instance = null;

    // Private constructor for Singleton pattern
    private function __construct() {
        $this->conn = new mysqli(
            DB_HOST,
            DB_USER,
            DB_PASS,
            DB_NAME
        );

        if ($this->conn->connect_error) {
            // Log error in production instead of displaying
            error_log('Database connection failed: ' . $this->conn->connect_error);
            throw new Exception('Database connection failed');
        }

        // Set charset to utf8mb4 for security and compatibility
        $this->conn->set_charset('utf8mb4');
    }

    // Get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::$instance = new Database();
            } catch (Exception $e) {
                // Handle error (could show a maintenance page in production)
                die('Service temporarily unavailable');
            }
        }
        return self::$instance;
    }

    // Get the MySQLi connection
    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}