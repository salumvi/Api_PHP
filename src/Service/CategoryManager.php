<?php
namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryManager
{

    private $em;
    private $categoryRepo;
    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepo)
    {
        $this->em = $em;
        $this->categoryRepo = $categoryRepo;
    }
    public function getRepository(): CategoryRepository
    {
        return $this->categoryRepo;
    }

    public function find(int $id): ?Category
    {
        return $this->categoryRepo->find($id);
    }

    /**
     * Undocumented function
     *
     * @return Category
     */
    public function create(): Category
    {
        return new Category();
    }
    public function persist(Category $category): Category
    {
        $this->em->persist($category);
        return $category;
    }
    public function saveChanges(Category $category): Category
    {
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }

    public function reload(Category $category): Category
    {
        $this->em->refresh($category);
        return $category;
    }


}
