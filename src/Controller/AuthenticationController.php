<?php

namespace App\Controller;

use App\Requests\AuthenticationRequestDTO;
use App\Requests\ChangePasswordRequestDTO;
use App\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AuthenticationController extends AbstractController
{

    public function __construct(
        private readonly AuthenticationService $authenticationService,
    ) {
    }

    #[Route('/auth/login', methods: ["POST"])]
    public function login(#[MapRequestPayload] AuthenticationRequestDTO $authenticationRequestDTO): Response
    {
        return $this->authenticationService->authenticate($authenticationRequestDTO);
    }

    #[Route('/auth/refresh', methods: ["GET"])]
    public function refreshToken(Request $request): Response
    {
        return $this->authenticationService->refresh($request);
    }

    #[Route('/auth/changePassword', methods: ["PATCH"])]
    public function updatePassword(#[MapRequestPayload] ChangePasswordRequestDTO $changePasswordRequestDTO): Response
    {
        return $this->authenticationService->updatePassword($changePasswordRequestDTO);
    }

    #[Route('/auth/logout', methods: ["GET"])]
    public function logout(): Response
    {
        return $this->authenticationService->logout();
    }

    #[Route('/auth/register', methods: ["POST"])]
    public function register(#[MapRequestPayload] AuthenticationRequestDTO $authenticationRequestDTO): Response
    {
        return $this->authenticationService->createUser($authenticationRequestDTO);
    }
}
