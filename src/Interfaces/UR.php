<?php

namespace Library\Interfaces;

interface UR
{
    public function createRequest($userId, $bookId);

    public function approveRequest($requestId);

    public function rejectRequest($requestId);

    public function getRequestByUser($userId);

    public function getRequestByStatus($status);

    public function getAllRequests();

    public function getRequestById($id);
}
