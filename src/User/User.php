<?php

namespace Library\User;

use Library\Database\Database;
use Library\Interfaces\UC;

class User extends Database implements UC
{
    public $id;
    public $name;
    public $email;
    private $password;
    public $role;
    public $status;

    public function register($name, $email, $password)
    {
        $sql = 'INSERT INTO users (name, email, password) VALUES (?,?,?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $name, $email, $password);

        if ($stmt->execute()) {
            return 'Registered Successfully';
        }
    }

    public function login($email, $password)
    {
        $sql = 'SELECT * FROM users WHERE email = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password']) && $user['status'] === 'active') {
                return $user;
            } elseif ($user['status'] === 'pending') {
                return 'Your account is still yet to be approved by admin';
            } elseif ($user['status'] === 'blocked') {
                return 'Your account is blocked by admin';
            } else {
                return false;
            }
        }

        return false;
    }

    public function getRole($email)
    {
        $sql = 'SELECT role FROM users WHERE email = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            return $row['role'];
        }

        return null;
    }

    public function getPendingUsers()
    {
        $sql = 'SELECT id, name, email, role, created_at FROM users WHERE status = ?';
        $stmt = $this->conn->prepare($sql);
        $status = 'pending';
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function approveUser($userId)
    {
        $sql = "UPDATE users SET status = 'active' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return 'User Approved Successfully';
        } else {
            return "User doesn't exist or already approved";
        }
    }

    public function rejectUser($userId)
    {
        $sql = "UPDATE users SET status = 'blocked' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        if ($stmt->execute()) {
            return 'User rejected';
        } else {
            return "User Doesn't exists";
        }
    }

    public function getAllUsers()
    {
        $sql = 'SELECT id, name, email, role, status, created_at FROM users';
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function getUserById($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function getUserByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}
