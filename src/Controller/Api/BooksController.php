<?php
namespace App\Controller\Api;

use App\Repository\BookRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Form\Type\BookFormType;
use Symfony\Component\HttpFoundation\Request;

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
    public function postAction( EntityManagerInterface $em, Request $request){
      
      $book = new Book();
      $form = $this->createForm(BookFormType::class, $book);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()){
         $em->persist($book);
         $em->flush();
         return $book;
      }
      return $form;
   }
}