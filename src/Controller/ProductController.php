<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AddressController
{
    public function __construct(
        private readonly ProductRepository   $productRepository,
        private readonly CategoryRepository   $categoryRepository,
        private readonly SerializerInterface $serializer,

    )
    {
    }

    #[Route('/products/{id}', methods: ["GET"])]
    public function getProductById(Product $product): JsonResponse
    {
        return new JsonResponse($this->serializer
            ->serialize(
                $product,
                "json",
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'photos' => ["url"],
                    'category' => ["id", "name"],
                    'productVariants' => ["id", "color","size"]
                ]
                ]
            ), Response::HTTP_OK, [], true);
    }

    #[Route('/products', methods: ["GET"])]
    public function queryProducts(
        #[MapQueryParameter] string                                                                                         $q = null,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int                                                              $page = 1,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int                                                              $size = 10,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_REGEXP, options: ['regexp' => '/^\w+,{1}(?>asc)|(?>desc)$/i'])] string $sort = null,
        #[MapQueryParameter] string                                                                                         $productCategory = null,
        #[MapQueryParameter] string                                                                                         $productSize = null,
        #[MapQueryParameter] string                                                                                         $productColor = null,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int                                                              $productPriceMin = 0,
        #[MapQueryParameter(filter: \FILTER_VALIDATE_INT)] int                                                              $productPriceMax = 9999999,
    ): JsonResponse
    {
        $qb = $this->productRepository->createQueryBuilder('product');
        $qb->select('product')
            ->where($qb->expr()->between('product.price', $productPriceMin, $productPriceMax))
            ->leftJoin('product.productVariants', 'product_variant')
            ->leftJoin('product.category', 'category');
        if (!is_null($q)) {
            $qb->andWhere('product.name LIKE :q')
                ->setParameter('q', "%" . $q . "%");
        }
        if (!is_null($sort)) {
            $sortArr = explode(',', $sort);
            $qb->orderBy('product.' . strtolower($sortArr[0]), strtoupper($sortArr[1]));
        }
        if (!is_null($productCategory)) {
            $qb->andWhere('category.id IN(:categories)')
                ->setParameter('categories', new ArrayCollection(explode(",", $productCategory)));
        }
        if (!is_null($productSize)) {
            $qb->andWhere('product_variant.size IN(:sizes)')
                ->setParameter('sizes', new ArrayCollection(explode(",", $productSize)));
        }
        if (!is_null($productColor)) {
            $qb->andWhere('product_variant.color IN(:colors)')
                ->setParameter('colors', new ArrayCollection(explode(",", $productColor)));
        }
        $paginator = new Paginator($qb->getQuery());
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $size);
        $paginator
            ->getQuery()
            ->setFirstResult($size * ($page - 1)) // set the offset
            ->setMaxResults($size); // set the limit
        /** @var Product $product */
        $results = [];
        foreach ($paginator as $product) {
            $results[] = json_decode($this->serializer->serialize(
                $product,
                'json',
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'photos' => ["url"],
                    'category' => ["id"]
                ]
                ]));
        }
        $categoriesArr = $this->categoryRepository->findAll();
        $categories = [];

        foreach ($categoriesArr as $category) {
            $parent =$category->getParent();
            $parentId=null;
            if (!is_null($parent)) $parentId=$parent->getId();
            $categories[] = [
                "id"=>$category->getId(),
                "name"=> $category->getName(),
                "parentId"=> $parentId
            ];
        }
        $result = [
            "count" => $totalItems,
            "pages" => $pagesCount,
            "currentPage" => $page,
            "items" => $results,
            "categories"=> $categories,
        ];
        return new JsonResponse($result, Response::HTTP_OK);
    }
}