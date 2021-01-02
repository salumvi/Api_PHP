<?php
namespace App\Controller\Api;

use App\Form\Model\CategoryDTO;
use App\Form\Type\CategoryFormType;
use App\Service\CategoryManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractFOSRestController
{

    /**
     * @Rest\Get(path="/categories")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(CategoryManager  $categoryManager)
    {
        return $categoryManager->getRepository()->findAll();
    }

    /**
     * @Rest\Post(path="/category")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function postAction(Request $request, CategoryManager $categoryManager)
    {
        $categeryDTO = new CategoryDTO();
        $form = $this->createForm(CategoryFormType::class, $categeryDTO);
        $form->handleRequest($request);
        
        // TODO: comprobar si el nombre ya existe
        if( $form->isSubmitted() && $form->isValid() ){
            $category = $categoryManager->create();
            $category->setName($categeryDTO->name);
            $categoryManager->saveChanges($category);

            return $category;

        }
        // devolvemos el formulario con los fallos
        return $form;
    }
}
