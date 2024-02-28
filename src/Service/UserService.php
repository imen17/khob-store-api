<?php

namespace App\Service;

use App\DTO\GetMeResponseDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserService
{
    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security               $security,
    )
    {
    }

    public function findById(int $id): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) throw new UserNotFoundException("User not found");
        return $user;
    }

    public function getLoggedInUser(): User
    {
        $user = $this->security->getUser();
        if (!$user) throw new UnauthorizedHttpException("You must be logged in");
        return $this->getByUsername($user->getUserIdentifier());
    }

    public function getByUsername(string $username): User
    {
        $user = $this->userRepository->findOneBy(['email' => $username]);
        if (is_null($user)) throw new UserNotFoundException("No user found with the username: " . $username);
        return $user;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function exists(string $username): bool
    {
        $user = $this->userRepository->findOneBy(['email' => $username]);
        return !is_null($user);
    }

}