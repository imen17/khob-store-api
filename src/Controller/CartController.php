<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartRepository;
use App\Repository\ProductVariantRepository;
use App\Service\CartService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User;

class CartController extends AbstractController
{
    public function __construct(
        private readonly CartRepository         $cartRepository,
        private readonly CartService         $cartService,
        private readonly Security               $security,
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }


    #[Route('/cart',methods: ["GET"])]
    public function getCart(): JsonResponse{
        /** @var User $user */
         $user =  $this->security->getUser();
        if (!$user) throw new UnauthorizedHttpException("You must be logged in");
        $cart = $this->cartService->getLastCart($user);
        $cartItems = $cart->getCartItems()->filter(function (CartItem $cartItem) {
            return is_null($cartItem->getDateRemoved());
        })->map(function (CartItem $cartItem) {
            $variant = $cartItem->getProductVariant();
            $product =  $variant->getProduct();
            $photo = $product->getPhotos()->first();
            if (!$photo) $photo = null;
            else $photo = $photo->getUrl();
            return [
                "name"=> $product->getName(),
                "productId"  =>$product->getId(),
                "variantId" => $variant->getId(),
                "photo" => $photo,
                "price" => $product->getPrice(),
                "size" => $variant->getSize(),
                "color" => $variant->getColor()
            ];
        });
        return new JsonResponse($cartItems->getValues(), Response::HTTP_OK);
    }
}