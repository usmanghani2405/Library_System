<?php

namespace Library\Requests;

use Library\Database\Database;
use Library\Interfaces\UR;

class Request extends Database implements UR
{
    public $id;
    public $user_id;
    public $book_id;
    public $status;
    public $due_date;
    public $created_at;
    public $updated_at;

    public function createRequest($userId, $bookId)
    {
        $sql = 'SELECT available FROM books WHERE id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        $bookResult = $stmt->get_result();

        if ($bookResult->num_rows !== 1) {
            return 'No book found with this ID';
        }

        $book = $bookResult->fetch_assoc();
        if ($book['available'] == 0) {
            return 'Book is not available';
        }

        $sql = 'SELECT id FROM requests 
            WHERE user_id = ? AND book_id = ? 
            AND status IN ("pending", "approved")';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $userId, $bookId);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            return 'You have already requested this book';
        }

        $status = 'pending';
        $sql = 'INSERT INTO requests (user_id, book_id, status) VALUES (?,?,?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iis', $userId, $bookId, $status);

        if ($stmt->execute()) {
            return 'Request sent successfully';
        }

        return 'Failed to create request';
    }

    public function approveRequest($requestId)
    {
        try {
            $this->conn->begin_transaction();

            $sql = 'SELECT book_id FROM requests 
                WHERE id = ? AND status = "pending"';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $requestId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows !== 1) {
                $this->conn->rollback();

                return 'Invalid or already processed request';
            }

            $request = $result->fetch_assoc();
            $bookId = $request['book_id'];

            $dueDate = date('Y-m-d', strtotime('+14 days'));
            $status = 'approved';

            $sql = 'UPDATE requests 
                SET status = ?, due_date = ? 
                WHERE id = ?';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ssi', $status, $dueDate, $requestId);
            $stmt->execute();

            $sql = 'UPDATE books SET available = 0 WHERE id = ?';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $bookId);
            $stmt->execute();

            $this->conn->commit();

            return 'Request approved successfully';
        } catch (\Exception $e) {
            $this->conn->rollback();

            return 'Failed to approve request';
        }
    }

    public function rejectRequest($requestId)
    {
        $sql = 'UPDATE requests SET status = ? where id = ?';
        $stmt = $this->conn->prepare($sql);
        $status = 'rejected';
        $stmt->bind_param('si', $status, $requestId);
        $stmt->execute();
        if ($stmt->affected_rows === 1) {
            return 'request rejected';
        }
    }

    public function getRequestByUser($userId)
    {
        $sql = 'SELECT requests.*, books.title, books.author 
            FROM requests 
            JOIN books ON requests.book_id = books.id 
            WHERE requests.user_id = ?
            ORDER BY requests.created_at DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $requests = $result->fetch_all(MYSQLI_ASSOC);

            return $requests;
        } else {
            return [];
        }
    }

    public function getRequestByStatus($status)
    {
        $sql = 'SELECT requests.*, users.name as user_name, users.email, 
                   books.title, books.author 
            FROM requests 
            JOIN users ON requests.user_id = users.id 
            JOIN books ON requests.book_id = books.id 
            WHERE requests.status = ?
            ORDER BY requests.created_at DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $requests = $result->fetch_all(MYSQLI_ASSOC);

            return $requests;
        } else {
            return [];
        }
    }

    public function getAllRequests()
    {
        $sql = 'SELECT requests.*, users.name as user_name, 
                   books.title as book_title 
            FROM requests 
            JOIN users ON requests.user_id = users.id 
            JOIN books ON requests.book_id = books.id
            ORDER BY requests.created_at DESC';
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function getRequestById($id)
    {
        $sql = 'SELECT requests.*, users.name as user_name, 
                   books.title, books.author 
            FROM requests 
            JOIN users ON requests.user_id = users.id 
            JOIN books ON requests.book_id = books.id 
            WHERE requests.id = ?';

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }

        return null;
    }

    public function returnBook($requestId)
    {
        try {
            $this->conn->begin_transaction();

            $sql = 'SELECT book_id FROM requests WHERE id = ? AND status = "approved"';
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }
            
            $stmt->bind_param('i', $requestId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows !== 1) {
                $this->conn->rollback();
                return 'Invalid request or book not approved';
            }

            $request = $result->fetch_assoc();
            $bookId = $request['book_id'];

            $status = 'returned';
            $sql = 'UPDATE requests SET status = ? WHERE id = ?';
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }
            
            $stmt->bind_param('si', $status, $requestId);
            if (!$stmt->execute()) {
                throw new \Exception('Execute failed: ' . $stmt->error);
            }

            $sql = 'UPDATE books SET available = 1 WHERE id = ?';
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception('Prepare failed: ' . $this->conn->error);
            }
            
            $stmt->bind_param('i', $bookId);
            if (!$stmt->execute()) {
                throw new \Exception('Execute failed: ' . $stmt->error);
            }

            $this->conn->commit();

            return 'Book returned successfully';
        } catch (\Exception $e) {
            $this->conn->rollback();
            return 'Failed to return book: ' . $e->getMessage();
        }
    }
}
