<?php
namespace App\Controller\Api;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\Model\BookDTO;
use App\Form\Model\CategoryDTO;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends AbstractFOSRestController
{

    /**
     * @Rest\Get(path="/books")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(BookRepository $bookRepo)
    {
        return $bookRepo->findAll();
    }

    /**
     * @Rest\Post(path="/book")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function postAction(
        EntityManagerInterface $em,
        Request $request,
        FileUploader $fileUploader,
        CategoryRepository $categoryRepo) {

        $bookDTO = new BookDTO();
        $form = $this->createForm(BookFormType::class, $bookDTO);
        $form->handleRequest($request);
        // deberiamos buscar el libro por nombre...

        if (!$form->isSubmitted()) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted() && $form->isValid()) {
           
            // ponemos el título
            $book = new Book();
            $book->setTitle($bookDTO->title);
            // ponemos la imagen
            if ($bookDTO->imageBase64) {
                $filename = $fileUploader->uploadBse64File($bookDTO->imageBase64);
                $book->setImage($filename);
            }
            // ponemos las categorias que vienen en el libro
            // estoe n un codigo que se repite.habría que ponerlo en un servicio
            foreach ($bookDTO->categories as $newCatDto) {
                $category = $categoryRepo->find($newCatDto->id ?? 0);
                    if (!$category) {
                        $category = new Category();
                        $category->setName($newCatDto->name);
                        $em->persist($category);
                    }
                    $book->addCategory($category);
            }
           

            $em->persist($book);
            $em->flush();
            return $book;
        }
        return $form;
    }

    /**
     * @Rest\Post(path="/book/{id}", requirements={"id"="\d+"})
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function editAction(
        int $id,
        EntityManagerInterface $em,
        BookRepository $bookRepo,
        CategoryRepository $categoryRepo,
        Request $request,
        FileUploader $fileUploader
    ) {
        $book = $bookRepo->find($id);

        if (!$book) {
            throw $this->createNotFoundException('libro no encontrado');
        }

        $bookDTO = BookDTO::createFromBook($book);

        $originalCategories = new ArrayCollection();

        foreach ($book->getCategories() as $category) {

            $categoryDto = CategoryDTO::createFromCategory($category);
            $bookDTO->categories[] = $categoryDto;
            $originalCategories->add($categoryDto);
        }

        $form = $this->createForm(BookFormType::class, $bookDTO);
        $form->handleRequest($request);
        
        if (!$form->isSubmitted()) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
        
        if ($form->isValid()) {
            // eliminar categorias
            foreach ($originalCategories as $catDto) {

                if (!in_array($catDto, $bookDTO->categories)) {
                    $category = $categoryRepo->find($catDto->id);
                    $book->removeCategory($category);

                }
            }

            // añadir categorias seleccionadas
            foreach ($bookDTO->categories as $newCatDto) {
                if (!$originalCategories->contains($newCatDto)) {
                    $category = $categoryRepo->find($newCatDto->id ?? 0);
                    if (!$category) {
                        $category = new Category();
                        $category->setName($newCatDto->name);
                        $em->persist($category);
                        
                    }
                    $book->addCategory($category);
                }
            }
            // le ponemos el titulo que queramos
            if ($bookDTO->title) {
                $book->setTitle($bookDTO->title);
                }
            if ($bookDTO->imageBase64) {
                // TODO: borrar la anterior omagen
                $fileImage = $fileUploader->uploadBse64File($bookDTO->imageBase64);
                $book->setImage($fileImage);
            }

            $em->persist($book);
            $em->flush();
            $em->refresh($book); // para devolver un array no un objeto

            return $book;
        }

        return $form;

    }
}
