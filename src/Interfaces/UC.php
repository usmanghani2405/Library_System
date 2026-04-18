<?php

namespace Library\Interfaces;

interface UC
{
    public function register($name, $email, $password);

    public function login($email, $password);

    public function getRole($email);

    // New methods for user verification
    public function getPendingUsers();

    public function approveUser($user_id);

    public function rejectUser($user_id);

    public function getAllUsers();
}
