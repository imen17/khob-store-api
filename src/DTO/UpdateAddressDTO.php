<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;


class UpdateAddressDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?string $addressLine,

        #[Assert\NotBlank]
        public readonly string $city,

        #[Assert\NotBlank]
        public readonly string $governorate,
    ) {
    }
}