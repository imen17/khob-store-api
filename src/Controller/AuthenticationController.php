<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class AuthenticationController extends AbstractController
{
    #[Route('/auth/login', methods: ["POST"])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, JWTEncoderInterface $JWTEncoder): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);
        $email=$parameters["email"];
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $isValid = $passwordHasher->isPasswordValid($user, $parameters["password"]);

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $JWTEncoder->encode([
            'username' => $user->getEmail(),
            'exp' => time() + 3600 // 1 hour expiration
        ]);

        return new JsonResponse(['token' => $token]);
    }
}
