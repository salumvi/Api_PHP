<?php
namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookManager
{

    private $em;
    private $bookRepo;
    public function __construct(EntityManagerInterface $em, BookRepository $bookRepo)
    {
        $this->em = $em;
        $this->bookRepo = $bookRepo;
    }

    public function getRepository(): BookRepository
    {
        return $this->bookRepo;
    }
    public function find(int $id): ?Book
    {
        return $this->bookRepo->find($id);
    }

    /**
     * Undocumented function
     *
     * @return Book
     */
    public function create(): Book
    {
        return new Book();
    }

    public function saveChanges(Book $book): Book
    {
        $this->em->persist($book);
        $this->em->flush();
        return $book;
    }

    public function reload(Book $book): Book
    {
        $this->em->refresh($book);
        return $book;
    }
    public function delete(Book $book)
    {
        $this->em->remove($book);
        $this->em->flush();
    }
}
