<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class AuthenticationController extends AbstractController
{
    #[Route('/auth/login', methods: ["POST"])]
    public function login(Request $request, EntityManagerInterface $entityManager, PasswordHasherInterface $passwordHasher, JWTEncoderInterface $JWTEncoder): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $request->getUser()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $isValid = $passwordHasher
            ->verify($user->getPassword(), $request->getPassword());

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $JWTEncoder->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 3600 // 1 hour expiration
        ]);

        return new JsonResponse(['token' => $token]);
    }
}
