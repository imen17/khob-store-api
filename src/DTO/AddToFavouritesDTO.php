<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;
class AddToFavouritesDTO
{
    public function __construct(
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public readonly ?int $productId,
    ) {
    }
}