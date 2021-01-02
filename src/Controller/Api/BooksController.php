<?php
namespace App\Controller\Api;

use App\Service\BookFormProcesor;
use App\Service\BookManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends AbstractFOSRestController
{

    /**
     * @Rest\Get(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(BookManager $bookManager)
    {
        return $bookManager->getRepository()->findAll();
    }

    /**
     * @Rest\Post(path="/book")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function postAction(
        BookFormProcesor $bookFormProcesor,
        BookManager $bookManager,
        Request $request
    ) {

        $book = $bookManager->create();

        // para llamar al método __invoque de la clase bookFormProcesor
        [$book, $error] = ($bookFormProcesor)($book, $request);
        // ahora manejamos la respuesta
        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ?? $error;
        return View::create($data, $statusCode);

    }

    /**
     * @Rest\Post(path="/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function editAction(
        int $id,
        BookFormProcesor $bookFormProcesor,
        BookManager $bookManager,
        Request $request
    ) {
        $book = $bookManager->find($id);

        if (!$book) {
            return View::create("Book nit found", Response::HTTP_BAD_REQUEST);
        }

        // para llamar al método __invoque de la clase bookFormProcesor
        [$book, $error] = ($bookFormProcesor)($book, $request);
        // ahora manejamos la respuesta

        $statusCode = $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST;
        $data = $book ?? $error;
        return View::create($data, $statusCode);

    }
    /**
     * @Rest\Get(path="/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function getSingleAction(int $id, BookManager $bookManager)
    {
        $book = $bookManager->find($id);

        if (!$book) {
            return View::create("Book not found", Response::HTTP_BAD_REQUEST);
        }
        return View::create($book, Response::HTTP_OK);

    }

    /**
     * @Rest\Delete(path="/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function deleteAction(
        int $id,
        BookManager $bookManager,
        Request $request
    ) {
        $book = $bookManager->find($id);

        if (!$book) {
            return View::create("Book not found", Response::HTTP_BAD_REQUEST);
        }
        $bookManager->delete($book);

        return View::create(null, Response::HTTP_NO_CONTENT);

    }
}
