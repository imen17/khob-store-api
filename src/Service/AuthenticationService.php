<?php

namespace App\Service;

use App\Entity\User;
use App\DTO\AuthenticationRequestDTO;
use App\DTO\ChangePasswordRequestDTO;
use DateTimeInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\AccessToken\Oidc\Exception\MissingClaimException;

class AuthenticationService
{

    private string $JWT_SECRET_KEY;
    private string $JWT_PUBLIC_KEY;
    private int $JWT_EXPIRY;
    private int $JWT_REFRESH_EXPIRY;
    private string $JWT_TOKEN_NAME;
    private string $JWT_REFRESH_NAME;
    private string $JWT_ALG;
    public function __construct(
        private readonly UserService                 $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ParameterBagInterface $params,
        private readonly KernelInterface $appKernel
    )
    {
        $projectRoot = $appKernel->getProjectDir();
        $this->JWT_SECRET_KEY=file_get_contents($projectRoot. $params->get('jwt_secret_path'));
        $this->JWT_PUBLIC_KEY=file_get_contents($projectRoot. $params->get('jwt_public_path'));
        $this->JWT_ALG=$params->get('jwt_alg');
        $this->JWT_EXPIRY=$params->get('jwt_secret_expiry');
        $this->JWT_REFRESH_EXPIRY=$params->get('jwt_refresh_cookie_expiry');
        $this->JWT_TOKEN_NAME=$params->get('jwt_access_cookie_name');
        $this->JWT_REFRESH_NAME=$params->get('jwt_refresh_cookie_name');
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


    private function createToken(string $username, int $expiry, array $claims = []): string
    {
        $claims['username'] = $username;
        $claims['exp'] = $expiry;
        return JWT::encode($claims, $this->JWT_SECRET_KEY, $this->JWT_ALG);
    }

    private function createAccessTokenCookie(User $user): Cookie
    {
        $expiry = time() + intval($this->JWT_EXPIRY);
        $token = $this->createToken($user->getUserIdentifier(), $expiry, ["ROLES" => $user->getRoles()]);
        return $this->createCookie($this->JWT_TOKEN_NAME, $token, $expiry);

    }

    private function createRefreshTokenCookie(User $user): Cookie
    {
        $expiry = time() + intval($this->JWT_REFRESH_EXPIRY);
        $token = $this->createToken($user->getUserIdentifier(), $expiry);
        return $this->createCookie($this->JWT_REFRESH_NAME, $token, $expiry);
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

    public function authenticate(Request $request): User {
        $jwtToken = $request->cookies->get($this->JWT_TOKEN_NAME);
        if (is_null($jwtToken)) throw new CustomUserMessageAuthenticationException('No JWT token provided');
        $decoded = (array) JWT::decode($jwtToken, new Key($this->JWT_PUBLIC_KEY, $this->JWT_ALG));
        $username = $decoded["username"];
        if (is_null($username)) throw new MissingClaimException("username missing from token.");
        return $this->userService->getByUsername($username);
    }
    public function login(AuthenticationRequestDTO $request): Response
    {
        $user = $this->userService->getByUsername($request->email);
        $isValid = $this->passwordHasher->isPasswordValid($user, $request->password);
        if (!$isValid) throw new BadCredentialsException("Wrong password.");
        return $this->createSuccessResponse($user);
    }

    public function refresh(Request $request): Response
    {
        $refreshToken = $request->cookies->get($this->JWT_REFRESH_NAME);
        try {
            $decoded =  (array)  JWT::decode($refreshToken, new Key($this->JWT_PUBLIC_KEY, $this->JWT_ALG));
            $user = $this->userService->getByUsername($decoded["username"]);
        } catch (Exception $e) {
            return new Response($e->getMessage(),Response::HTTP_FORBIDDEN);
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
        $accessToken = $this->createCookie($this->JWT_TOKEN_NAME, "", 0);
        $refreshToken = $this->createCookie($this->JWT_REFRESH_NAME, "", 0);
        $response = new Response(
            null,
            Response::HTTP_OK,
        );
        $response->headers->set("Set-Cookie", $accessToken);
        $response->headers->set("Set-Cookie", $refreshToken, false);
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