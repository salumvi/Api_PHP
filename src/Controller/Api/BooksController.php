<?php
namespace App\Controller\Api;

use App\Repository\BookRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Form\Model\BookDTO;
use App\Form\Type\BookFormType;
use Symfony\Component\HttpFoundation\Request;
use League\Flysystem\FilesystemInterface;

class BooksController extends AbstractFOSRestController {

   
   /**
    * @Rest\Get(path="/books")
    * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
    */
   public function getAction( BookRepository $bookRepo){
      return $bookRepo->findAll();
   }


   /**
    * @Rest\Post(path="/books")
    * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
    */
    public function postAction( 
         EntityManagerInterface $em, 
         Request $request,
         FilesystemInterface $defaultStorage){
      
      $bookDTO = new BookDTO();
      $form = $this->createForm(BookFormType::class, $bookDTO);
      $form->handleRequest($request);
      
      if($form->isSubmitted() && $form->isValid())
      {
         $extension =explode('/',explode( ';', $bookDTO->imageBase64)[0])[1];
         $data = explode(',', $bookDTO->imageBase64)[1];
         $filename = sprintf('%s.%s',uniqid('book_', true), $extension);
         // guardamos la imagen en lacarpeta definida en la configuracion de flysystem.yaml
         $defaultStorage->write($filename, base64_decode($data));
         
         $book = new Book();
         $book->setTitle($bookDTO->title);
         $book->setImage($filename);
         $em->persist($book);
         $em->flush();
         return $book;
      }
      return $form;
   }
}