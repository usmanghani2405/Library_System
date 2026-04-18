<?php

namespace Library\Database;

abstract class Database
{
    protected \mysqli $conn;

    private $host;
    private $cuser;
    private $pass;
    private $name;
    private $port;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'db';
        $this->cuser = getenv('DB_USER') ?: 'db';
        $this->pass = getenv('DB_PASS') ?: 'db';
        $this->name = getenv('DB_NAME') ?: 'db';
        $this->port = getenv('DB_PORT') ?: '20637';

        // 1. Initialize mysqli
        $this->conn = mysqli_init();

        // 2. Set SSL before connecting (Required for Aiven)
        mysqli_ssl_set($this->conn, null, null, null, null, null);

        // 3. Connect using real_connect with the SSL flag
        $result = mysqli_real_connect(
            $this->conn,
            $this->host,
            $this->cuser,
            $this->pass,
            $this->name,
            $this->port,
            null,
            MYSQLI_CLIENT_SSL
        );

        if (!$result) {
            throw new \Exception('Database connection failed: '.mysqli_connect_error());
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
