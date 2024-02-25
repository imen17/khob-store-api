<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use phpDocumentor\Reflection\Types\Integer;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }
    public function save(Category $category): void {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     */
    public function getById(int $id): Category {
        $category = $this->categoryRepository->findOneBy(['id' => $id]);
        if (is_null($category)) throw new EntityNotFoundException("No category found with this id: " . $id);
        return $category;
    }

    public function delete(int $id):void
    {
        $category= $this->categoryRepository ->findOneBy(['id' => $id]);
        if (is_null($category)) throw new EntityNotFoundException("No category found with this id: " . $id);
        $this ->entityManager->remove($category);
        $this->entityManager->flush();
    }
}