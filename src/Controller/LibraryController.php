<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{

    // public function __construct(LoggerInterface $logger)
    // {

    // }

    /**
     * @Route("/library/listJS", name="library_listJS")
     */
    public function listJS(Request $request, LoggerInterface $logger)
    {
        $title = $request->get('title', 'título');

        $logger->info($title);
        $response = new JsonResponse();

        $response->setData([
            'success' => true,
            'data' => [
                ['id' => '1', 'titulo' => 'asdffg'],
                ['id' => '2', 'titulo' => 'asdffg'],
                ['id' => '3', 'titulo' => 'asdffg'],
                ['id' => '4', 'titulo' => $title],
            ],
        ]);

        return $response;
    }

/**
 * @Route("/book/create",name="create_book")
 *
 * @param Request $request
 * @param EntityManagerInterface $em
 * @return void
 */
    public function createBook(Request $request, EntityManagerInterface $em)
    {
        $book = new Book();

        $title = $request->get('title', null);

        $response = new JsonResponse();

        if (empty($title)) {

            $response->setData([
                'success' => false,
                'error' => 'Title can not empty',
                'data' => null,
            ]);
            return $response;
        }

        $book->setTitle($title);
        $book->setImage($request->get('image', null));
        $em->persist($book);
        $em->flush();

        $response->setData([
            'success' => true,
            'data' => [
                'title' => $book->getTitle(),
                'image' => $book->getImage(),
            ],
        ]);

        return $response;

    }

    /**
     * @Route("/books",name="book")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return void
     */
    function list(Request $request, BookRepository $bookRepository) {
        $books = $bookRepository->findAll();

        $arrayBooks = [];

        foreach ($books as $book) {
            $arrayBooks[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'image' => $book->getImage(),
            ];
        }

        $response = new JsonResponse();

        $response->setData([
            'success' => true,
            'data' => $arrayBooks,
        ]);

        return $response;

    }
}
