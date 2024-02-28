<?php

namespace App\Controller;

use App\DTO\AddToFavouritesDTO;
use App\Entity\Favourite;
use App\Repository\FavouriteRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class FavouriteController extends AddressController
{
    public function __construct(
        private readonly FavouriteRepository    $favouriteRepository,
        private readonly ProductRepository      $productRepository,
        private readonly SerializerInterface    $serializer,
        private readonly Security               $security,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/favourites/add', methods: ["POST"])]
    public function addToFavourites(#[MapRequestPayload] AddToFavouritesDTO $addToFavouritesDTO): Response
    {
        $product = $this->productRepository->find($addToFavouritesDTO->productId);
        if (!$product) throw new EntityNotFoundException();
        $user = $this->security->getUser();
        if (!$user) throw new UnauthorizedHttpException("You must be logged in");
        $favourite = $this->favouriteRepository->findOneBy(["owner" => $user, "product" => $product]);
        if ($favourite !== null) return new Response(null, Response::HTTP_OK);
        $favourite = new Favourite();
        $favourite->setProduct($product)->setOwner($user);
        $this->entityManager->persist($favourite);
        $this->entityManager->flush();
        return new Response(null, Response::HTTP_OK);
    }

    #[Route('/favourites/remove', methods: ["POST"])]
    public function removeFromFavourites(#[MapRequestPayload] AddToFavouritesDTO $addToFavouritesDTO): Response
    {
        $product = $this->productRepository->find($addToFavouritesDTO->productId);
        $favourite = $this->favouriteRepository->findOneBy(["product" => $product]);
        if ($favourite === null) return new Response(null, Response::HTTP_GONE);
        $this->entityManager->remove($favourite);
        $this->entityManager->flush();
        return new Response(null, Response::HTTP_OK);
    }

    #[Route('/favourites', methods: ["GET"])]
    public function listFavourites(): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) throw new UnauthorizedHttpException("You must be logged in");
        $favourites = $this->favouriteRepository->findBy(["owner" => $user]);
        return new JsonResponse(
            $this->serializer
                ->serialize(
                    $favourites,
                    "json",
                    [AbstractNormalizer::ATTRIBUTES =>
                        [
                            'product' => [
                                'id'
                            ]
                        ]
                    ]
                )
            , Response::HTTP_OK, [], true);
    }
}