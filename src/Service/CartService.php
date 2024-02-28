<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;

class CartService
{

    public function __construct(
        private readonly CartRepository         $cartRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security               $security,
    )
    {
    }

    public function getLastCart(User $user): Cart {
        try {
            return $this->cartRepository->createQueryBuilder("cart")
                ->where("cart.owner = :owner")
                ->setParameter("owner", $user)
                ->orderBy('cart.dateCreated', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $cart = new Cart();
            $cart->setOwner($user);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
            return $cart;
        }

    }
}