<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AuthenticationRequestDTO
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email,

        #[Assert\Type('string')]
        #[Assert\NotBlank]
        public readonly string $password,
    )
    {

    }
}