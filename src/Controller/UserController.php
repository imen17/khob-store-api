<?php

namespace App\Controller;

use App\DTO\UpdateUserDTO;
use App\Entity\CartItem;
use App\Entity\Favourite;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use App\Service\CartService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService         $userService,
        private readonly SerializerInterface $serializer,
        private readonly CartService         $cartService
    )
    {
    }

    #[Route('/users/me', methods: ["GET"])]
    public function getMe(): JsonResponse
    {
        $user = $this->userService->getLoggedInUser();
        $favourites = $user->getFavourites()->map(function (Favourite $favourite) {
            return $favourite->getProduct()->getId();

        });
        $userDTO = json_decode($this->serializer
            ->serialize(
                $user,
                "json",
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'firstName',
                    'lastName',
                    'roles'
                ]
                ]
            ), true);
        $cart = $this->cartService->getLastCart($user);
        $cartItems = $cart->getCartItems()->filter(function (CartItem $cartItem) {
            return is_null($cartItem->getDateRemoved());
        })->map(function (CartItem $cartItem) {
            return [
                "productId" => $cartItem->getProductVariant()->getProduct()->getId(),
                "variantId" => $cartItem->getProductVariant()->getId(),
            ];

        });
        $userDTO["cart"] = $cartItems->getValues();
        $userDTO["favourites"] = $favourites->toArray();
        return new JsonResponse(
            $userDTO,
            Response::HTTP_OK,
            []
        );
    }

    # https://symfony.com/doc/current/doctrine.html#automatically-fetching-objects-entityvalueresolver
    #[Route('/users/{id}', methods: ["GET"])]
    public function getUserById(User $user): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);
        return new JsonResponse(
            $this->serializer
                ->serialize(
                    $user,
                    "json",
                    [AbstractNormalizer::ATTRIBUTES => [
                        'firstName',
                        'lastName',
                        'phone',
                        'addresses' => [
                            "id",
                            "addressLine",
                            "city",
                            "governorate"
                        ]]
                    ]
                ),
            Response::HTTP_OK, [],
            true
        );
    }

    #[Route('/users/{id}', methods: ["PATCH"])]
    public function updateUser(#[MapRequestPayload] UpdateUserDTO $userObject, int $id): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $this->getUser());
        $userToBeEdited = $this->userService->findById($id);
        $userToBeEdited->setFirstName($userObject->firstName)
            ->setLastName($userObject->lastName)
            ->setPhone($userObject->phone);
        $this->userService->save($userToBeEdited);
        return new Response(
            null,
            Response::HTTP_ACCEPTED
        );
    }
}