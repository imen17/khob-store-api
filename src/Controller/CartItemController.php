<?php

namespace App\Controller;

use App\DTO\AddToCartDTO;
use App\Entity\CartItem;
use App\Repository\ProductVariantRepository;
use App\Service\CartService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CartItemController extends AddressController
{
    public function __construct(
        private readonly UserService         $userService,
        private readonly CartService         $cartService,
        private readonly ProductVariantRepository         $productVariantRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/cartItems/add', methods: ["POST"])]
    public function addToCart(#[MapRequestPayload] AddToCartDTO $addToCartDTO): Response
    {
        $cart = $this->cartService->getLastCart($this->userService->getLoggedInUser());
        $cartItem = $cart->getCartItems()->findFirst(function(int $key, CartItem $item) use ($addToCartDTO)  {
            return $item->getProductVariant()->getId()  === $addToCartDTO->variantId And is_null($item->getDateRemoved());
        });
        if (is_null($cartItem)) {
            $productVariant = $this->productVariantRepository->find($addToCartDTO->variantId);
            if (!$productVariant) throw new EntityNotFoundException();
            $cartItem = new CartItem();
            $cartItem->setCart($cart)
                ->setProductVariant($productVariant);
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
        }
        return new Response(null, Response::HTTP_OK);
    }

    #[Route('/cartItems/remove', methods: ["POST"])]
    public function removeFromCart(#[MapRequestPayload] AddToCartDTO $removeFromCartDTO): Response
    {
        $cart = $this->cartService->getLastCart($this->userService->getLoggedInUser());
        $cartItem = $cart->getCartItems()->findFirst(function(int $key, CartItem $item) use ($removeFromCartDTO)  {
            return $item->getProductVariant()->getId() === $removeFromCartDTO->variantId And is_null($item->getDateRemoved());
        });
        if (!is_null($cartItem)) {
            $cartItem->setDateRemoved(new \DateTime());
            $this->entityManager->persist($cartItem);
            $this->entityManager->flush();
        }
        return new Response(null, Response::HTTP_OK);
    }
}