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
        private readonly Security               $security
    )
    {
    }

    public function getLoggedInUser(): GetMeResponseDTO
    {
        $userInterface = $this->security->getUser();
        if (!$userInterface) throw new UnauthorizedHttpException("You must be logged in");
        $user = $this->getByUsername($userInterface->getUserIdentifier());
        return new GetMeResponseDTO($user->getFirstName(),
            $user->getLastName(),
            $user->getRoles());
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