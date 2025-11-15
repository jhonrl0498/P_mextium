<?php

$host = "localhost";
$db = "mextium";
$user = "root";
$pass = "";
$charset = "utf8mb4";

class Database {
    private static $instance = null;
    private $connection;

    private $host = '82.197.82.93';
    private $dbname = 'u366162802_mextium';
    private $username = 'u366162802_santiago';
    private $password = 'vU7=5WEQXw';

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function __clone() {}
    public function __wakeup() {}
}



