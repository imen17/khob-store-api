<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;
class AddToCartDTO
{
    public function __construct(
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public readonly ?int $variantId,
    ) {
    }
}