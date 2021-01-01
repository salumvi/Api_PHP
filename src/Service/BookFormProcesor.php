<?php
namespace App\Service;

use App\Entity\Book;
use App\Form\Model\BookDTO;
use App\Form\Model\CategoryDTO;
use App\Form\Type\BookFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class BookFormProcesor
{

    private $bookManager;
    private $categoryManager;
    private $fileUploader;
    private $formFactory;

    public function __construct(
        BookManager $bookManager,
        CategoryManager $categoryManager,
        FileUploader $fileUploader,
        FormFactoryInterface $formFactory
    ) {
        $this->bookManager = $bookManager;
        $this->categoryManager = $categoryManager;
        $this->fileUploader = $fileUploader;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Book $book, Request $request): array
    {

        $bookDTO = BookDTO::createFromBook($book);

        $originalCategories = new ArrayCollection();

        foreach ($book->getCategories() as $category) {

            $categoryDto = CategoryDTO::createFromCategory($category);
            $bookDTO->categories[] = $categoryDto;
            $originalCategories->add($categoryDto);
        }

        $form = $this->formFactory->create(BookFormType::class, $bookDTO);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            // TODO mejorar esta respuesta con un objeto
            return [null, 'Form is not submited']; // el null hace referencia el objeto en caso de todo bien, y el segundo parametro en caso de mal
        }

        if ($form->isValid()) {
            // eliminar categorias
            foreach ($originalCategories as $catDto) {

                if (!in_array($catDto, $bookDTO->categories)) {
                    $category = $this->categoryManager->find($catDto->id);
                    $book->removeCategory($category);

                }
            }

            // añadir categorias seleccionadas
            foreach ($bookDTO->categories as $newCatDto) {
                if (!$originalCategories->contains($newCatDto)) {
                    $category = $this->categoryManager->find($newCatDto->id ?? 0);
                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCatDto->name);
                        $this->categoryManager->persist($category);

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
                $fileImage = $this->fileUploader->uploadBse64File($bookDTO->imageBase64);
                $book->setImage($fileImage);
            }

            $this->bookManager->saveChanges($book);

            $this->bookManager->reload($book); // para devolver un array no un objeto

            return [$book, null];
        }
        return [null, $form];

    }
}
