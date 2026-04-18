<?php

namespace Library\Database;

abstract class Database
{
    protected \mysqli $conn;

    private $host;
    private $cuser;
    private $pass;
    private $name;

    public function __construct()
    {
        // Assign values inside the constructor
        $this->host = getenv('DB_HOST') ?: 'db';
        $this->cuser = getenv('DB_USER') ?: 'db';
        $this->pass = getenv('DB_PASS') ?: 'db';
        $this->name = getenv('DB_NAME') ?: 'db';

        // Note: I left out port for the simple mysqli call,
        // but Render usually uses the default 3306 anyway.
        $this->conn = new \mysqli(
            $this->host,
            $this->cuser,
            $this->pass,
            $this->name
        );

        if ($this->conn->connect_error) {
            throw new \Exception('Database connection failed: '.$this->conn->connect_error);
        }
    }

    public function __destruct()
    {
        // Safety check to ensure $conn exists before closing
        if (isset($this->conn) && $this->conn instanceof \mysqli) {
            $this->conn->close();
        }
    }
}
