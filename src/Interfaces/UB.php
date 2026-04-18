<?php

namespace Library\Interfaces;

interface UB
{
    public function addBook($title, $author, $description);

    public function editBook($id, $title, $author, $description, $available);

    public function deleteBook($id);

    public function getAvailableBooks();
}
