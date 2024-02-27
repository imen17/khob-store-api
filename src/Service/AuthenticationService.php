<?php

namespace App\Service;

use App\Entity\User;
use App\DTO\AuthenticationRequestDTO;
use App\DTO\ChangePasswordRequestDTO;
use DateTimeInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AuthenticationService
{
    private const ACCESS_TOKEN_NAME = "accessToken";
    private const REFRESH_TOKEN_NAME = "refreshToken";

    public function __construct(
        private readonly UserService                 $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTEncoderInterface         $JWTEncoder,
    )
    {

    }

    private function createCookie(string $name, string $value, DateTimeInterface|int|string $expiry)
    {
        return Cookie::create($name)
            ->withValue($value)
            ->withExpires($expiry)
            ->withPath("/")
            ->withHttpOnly(true)
            ->withSameSite("strict")
            ->withSecure(true);
    }

    /**
     * @throws JWTEncodeFailureException
     */
    private function createToken(string $username, int $expiry, array $claims = []): string
    {
        $claims['username'] = $username;
        $claims['exp'] = $expiry;
        return $this->JWTEncoder->encode($claims);
    }

    private function createAccessTokenCookie(User $user): Cookie
    {
        $expiry = time() + intval($_ENV['JWT_EXPIRY']);
        $token = $this->createToken($user->getUserIdentifier(), $expiry, ["ROLES" => $user->getRoles()]);
        return $this->createCookie(self::ACCESS_TOKEN_NAME, $token, $expiry);

    }

    private function createRefreshTokenCookie(User $user): Cookie
    {
        $expiry = time() + intval($_ENV['JWT_REFRESH_EXPIRY']);
        $token = $this->createToken($user->getUserIdentifier(), $expiry, ["ROLES" => $user->getRoles()]);
        return $this->createCookie(self::REFRESH_TOKEN_NAME, $token, $expiry);
    }

    private function createSuccessResponse(User $user): Response {
        $accessToken = $this->createAccessTokenCookie($user);
        $refreshToken = $this->createRefreshTokenCookie($user);

        $response = new Response(
            null,
            Response::HTTP_OK,
        );
        $response->headers->set("Set-Cookie", $accessToken, false);
        $response->headers->set("Set-Cookie", $refreshToken, false);

        return $response;
    }
    public function authenticate(AuthenticationRequestDTO $request): Response
    {
        $user = $this->userService->getByUsername($request->email);
        $isValid = $this->passwordHasher->isPasswordValid($user, $request->password);
        if (!$isValid) throw new BadCredentialsException("Wrong password.");
        return $this->createSuccessResponse($user);
    }

    public function refresh(Request $request): Response
    {
        $refreshToken = $request->cookies->get("refreshToken");
        try {
            $decoded = $this->JWTEncoder->decode($refreshToken);
            $user = $this->userService->getByUsername($decoded["username"]);
        } catch (JWTDecodeFailureException $e) {
            return new Response($e->getReason(),Response::HTTP_FORBIDDEN);
        }
        return $this->createSuccessResponse($user);
    }

    public function updatePassword(ChangePasswordRequestDTO $changePasswordRequestDTO): Response
    {
        $user = $this->userService->getByUsername($changePasswordRequestDTO->email);
        $isValid = $this->passwordHasher->isPasswordValid($user, $changePasswordRequestDTO->oldPassword);
        if (!$isValid) throw new BadCredentialsException("Wrong password.");
        $newPassword = $this->passwordHasher->hashPassword($user, $changePasswordRequestDTO->newPassword);
        if ($newPassword == $user->getPassword()) throw new BadCredentialsException("The new password cannot be the same as the old password.");

        $user->setPassword($newPassword);
        $this->userService->save($user);

        return new Response(
            null,
            Response::HTTP_OK,
        );
    }


    public function logout(): Response
    {
        $accessToken = $this->createCookie(self::ACCESS_TOKEN_NAME, "", 0);
        $response = new Response(
            null,
            Response::HTTP_OK,
        );
        $response->headers->set("Set-Cookie", $accessToken);
        return $response;
    }

    public function createUser(AuthenticationRequestDTO $request): Response
    {
        $exits = $this->userService->exists($request->email);
        if ($exits) throw new ConflictHttpException("User already exists");

        $user = new User();
        $user->setEmail($request->email)
            ->setPassword($this->passwordHasher->hashPassword($user, $request->password));

        $this->userService->save($user);

        return new Response(
            null,
            Response::HTTP_CREATED,
        );
    }
}