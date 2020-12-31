<?php

namespace App\Form\Model;

use App\Entity\Book;

class BookDTO
{

    public $title;
    public $imageBase64;
    public $categories;

    public function __construct()
    {
        $this->categories = [];
    }

    public static function createFromBook(Book $book): self{

        $dto = new self();
        $dto->title = $book->getTitle();
        return $dto;
    }

}
