<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService){
    }
    #[Route('/category',methods: ["GET"])]
    public function categoryList(){
        return $this->categoryService->getAllCategories();
    }
    #[Route('/addCategory',methods: ["POST"])]
    public function addCategory(Request $request): JsonResponse {
            $requestData = json_decode($request->getContent(), true);

            // Validate and handle the data
            if (isset($requestData['name'])) {
                $category = new Category();
                $category->setName($requestData['name']);

                // Check if parentCategory is provided and exists
                if (isset($requestData['parentCategory'])) {
                    $parentCategory = $this->getDoctrine()->getRepository(Category::class)->find($requestData['parentCategory']);
                    if (!$parentCategory) {
                        return new JsonResponse(['error' => 'Parent category not found'], Response::HTTP_BAD_REQUEST);
                    }
                    $category->setParentCategory($parentCategory);
                }

                $this->categoryService->save($category);

                return new JsonResponse(['message' => 'Category created successfully'], Response::HTTP_CREATED);
            } else {
                return new JsonResponse(['error' => 'Category name is required'], Response::HTTP_BAD_REQUEST);
            }
        }

    #[Route('/deleteCategory/{id}',methods: ["DELETE"])]
    public function deleteCategory(int $id): JsonResponse
    {
        try {
            $this->categoryService->delete($id);
            return new JsonResponse(['message' => 'Category deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete category: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/Category/{id}',methods: ["GET"])]
    public function getCategory(int $id): JsonResponse
    {
        $category = $this->categoryService->getById($id);

        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $category->getId(),
            'name' => $category->getName(),
            'parentCategory'=> $category->getParentCategory()
        ]);
    }


}
