<?php

namespace Library\Database;

abstract class Database
{
    protected \mysqli $conn;

    private string $host = 'db';
    private string $user = 'db';
    private string $password = 'db';
    private string $db = 'db';

    public function __construct()
    {
        $this->conn = new \mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->db
        );

        if ($this->conn->connect_error) {
            throw new \Exception('Database connection failed');
        }
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}
