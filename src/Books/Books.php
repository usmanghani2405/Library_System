<?php

namespace Library\Books;

use Library\Database\Database;
use Library\Interfaces\UB;

class Books extends Database implements UB
{
    public $id;
    public $title;
    public $author;
    public $description;
    public $available;

    public function addBook($title, $author, $description)
    {
        $sql = 'INSERT INTO books(title, author, description) VALUES (?,?,?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $title, $author, $description);
        if ($stmt->execute()) {
            return 'Book added Successfully';
        }
    }

    public function editBook($id, $title, $author, $description, $available)
    {
        $sql = 'SELECT * FROM books WHERE id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $sql = 'UPDATE books SET title = ?, author = ?, description = ?, available = ? WHERE id = ?';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('sssii', $title, $author, $description, $available, $id);
            $stmt->execute();

            return 'Book updated Successfully';
        } else {
            return "Book doesn't exists";
        }
    }

    public function deleteBook($id)
    {
        $sql = 'DELETE FROM books WHERE id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            return 'Deleted Successfully';
        }

        return 'Failed to delete book';
    }

    public function getAvailableBooks()
    {
        $sql = 'SELECT * FROM books WHERE available = ?';
        $stmt = $this->conn->prepare($sql);
        $available = 1;
        $stmt->bind_param('i', $available);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public function getBookById($id)
    {
        $sql = 'SELECT * FROM books WHERE id = ?';
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

    public function searchBooks($keyword)
    {
        $sql = 'SELECT * FROM books WHERE title LIKE ? OR author LIKE ?';
        $stmt = $this->conn->prepare($sql);
        $searchTerm = "%$keyword%";
        $stmt->bind_param('ss', $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function updateAvailability($id, $status)
    {
        $sql = 'UPDATE books SET available = ? WHERE id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $status, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return 'Book availability updated successfully';
        }

        return 'Book not found or already has this status';
    }

    public function getAllBooks()
    {
        $sql = 'SELECT * FROM books ORDER BY created_at DESC';
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }
}
