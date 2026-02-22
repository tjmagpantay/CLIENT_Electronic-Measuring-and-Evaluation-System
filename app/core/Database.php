<?php

/**
 * Database Core Class
 * Handles database connections using PDO
 */

class Database
{
    private $host;
    private $database;
    private $username;
    private $password;
    private $charset;
    private $options;
    private $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';

        $this->host = $config['host'];
        $this->database = $config['database'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->charset = $config['charset'];
        $this->options = $config['options'];
    }

    /**
     * Get PDO connection
     */
    public function connect()
    {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
                $this->pdo = new PDO($dsn, $this->username, $this->password, $this->options);
            } catch (PDOException $e) {
                die('Database Connection Failed: ' . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    /**
     * Execute a query
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die('Query Failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch single row
     */
    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId()
    {
        return $this->connect()->lastInsertId();
    }

    /**
     * Get row count
     */
    public function rowCount($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}
