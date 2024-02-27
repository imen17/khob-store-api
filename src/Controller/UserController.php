<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiProperty;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('/users/me', methods: ["GET"])]
    public function getMe(): Response
    {
        return new Response(
            $this->serializer->serialize($this->userService->getLoggedInUser(), "json"),
            Response::HTTP_OK
        );
    }
}