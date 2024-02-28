<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;
class UpdateUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?string $firstName,

        #[Assert\NotBlank]
        public readonly string $lastName,

        #[Assert\PositiveOrZero]
        public readonly int $phone,
    ) {
    }
}