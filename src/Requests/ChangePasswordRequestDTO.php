<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordRequestDTO
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email,

        #[Assert\NotBlank]
        public readonly string $oldPassword,

        #[Assert\NotBlank]
        public readonly string $newPassword)
    {

    }
}