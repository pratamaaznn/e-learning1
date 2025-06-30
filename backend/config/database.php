<?php
class Database
{
    private $host = 'localhost';
    private $db_name = 'elearning_db';
    private $username = 'root';
    private $password = '';
    private $socket = '/data/data/com.termux/files/usr/var/run/mysqld.sock';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            // Coba dengan socket dulu
            if (file_exists($this->socket)) {
                $dsn = "mysql:unix_socket=" . $this->socket . ";dbname=" . $this->db_name;
            } else {
                // Fallback ke TCP
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            }

            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
