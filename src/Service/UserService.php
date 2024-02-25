<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getByUsername(string $username): User {
        $user = $this->userRepository->findOneBy(['email' => $username]);
        if (is_null($user)) throw new UserNotFoundException("No user found with the username: " . $username);
        return $user;
    }

    public function save(User $user): void {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function exists(string $username): bool {
        $user = $this->userRepository->findOneBy(['email' => $username]);
       return !is_null($user);
    }

}