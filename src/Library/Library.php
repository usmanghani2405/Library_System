<?php

namespace Library\Library;

use Library\Books\Books;
use Library\Requests\Request;
use Library\User\User;

class Library
{
    private $userObj;
    private $bookObj;
    private $requestObj;

    public function __construct()
    {
        $this->userObj = new User();
        $this->bookObj = new Books();
        $this->requestObj = new Request();
    }

    public function registerUser($name, $email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $result = $this->userObj->register($name, $email, $hashedPassword);

        return $result;
    }

    public function loginUser($email, $password)
    {
        $result = $this->userObj->login($email, $password);

        return $result;
    }

    public function getPendingRegistrations()
    {
        $result = $this->userObj->getPendingUsers();

        return $result;
    }

    public function approveUserRegistration($userId)
    {
        $result = $this->userObj->approveUser($userId);

        return $result;
    }

    public function rejectUserRegistration($userId)
    {
        $result = $this->userObj->rejectUser($userId);

        return $result;
    }

    public function addBook($title, $author, $description)
    {
        $result = $this->bookObj->addBook($title, $author, $description);

        return $result;
    }

    public function editBook($id, $title, $author, $description, $available)
    {
        $result = $this->bookObj->editBook($id, $title, $author, $description, $available);

        return $result;
    }

    public function deleteBook($bookId)
    {
        $result = $this->bookObj->deleteBook($bookId);

        return $result;
    }

    public function searchBooks($keyword)
    {
        $result = $this->bookObj->searchBooks($keyword);

        return $result;
    }

    public function getAvailableBooks()
    {
        $result = $this->bookObj->getAvailableBooks();

        return $result;
    }

    public function getAllBooks()
    {
        $result = $this->bookObj->getAllBooks();

        return $result;
    }

    public function requestBook($userId, $bookId)
    {
        $result = $this->requestObj->createRequest($userId, $bookId);

        return $result;
    }

    public function approveRequest($requestId)
    {
        $result = $this->requestObj->approveRequest($requestId);

        return $result;
    }

    public function rejectRequest($requestId)
    {
        $result = $this->requestObj->rejectRequest($requestId);

        return $result;
    }

    public function getUserRequest($userId)
    {
        $result = $this->requestObj->getRequestByUser($userId);

        return $result;
    }

    public function getPendingRequests()
    {
        $result = $this->requestObj->getRequestByStatus('pending');

        return $result;
    }

    public function sendDueReminder()
    {
        $result = $this->requestObj->getRequestByStatus('approved');

        $dueRequests = [];
        $overDue = [];

        $today = strtotime(date('Y-m-d'));

        $twoDaysFromNow = strtotime('+2 days', $today);

        foreach ($result as $request) {
            $dueDate = strtotime($request['due_date']);

            if ($dueDate < $today) {
                $overDue[] = $request;
            } elseif ($dueDate <= $twoDaysFromNow) {
                $dueRequests[] = $request;
            }
        }

        return [
            'overdue' => $overDue,
            'due_soon' => $dueRequests,
        ];
    }

    public function getAdminDashboard()
    {
        $totalUsers = count($this->userObj->getAllUsers());
        $totalPendingUsers = count($this->userObj->getPendingUsers());
        $totalAvailBooks = count($this->bookObj->getAvailableBooks());
        $totalPendingRequests = count($this->requestObj->getRequestByStatus('pending'));

        return [
            'totalUsers' => $totalUsers,
            'totalPendingUsers' => $totalPendingUsers,
            'totalAvailBooks' => $totalAvailBooks,
            'totalPendingRequests' => $totalPendingRequests,
        ];
    }

    public function getBookById($id)
    {
        $result = $this->bookObj->getBookById($id);

        return $result;
    }

    public function returnBook($requestId)
    {
        $result = $this->requestObj->returnBook($requestId);

        return $result;
    }

    public function getApprovedRequests()
    {
        $result = $this->requestObj->getRequestByStatus('approved');

        return $result;
    }
}
