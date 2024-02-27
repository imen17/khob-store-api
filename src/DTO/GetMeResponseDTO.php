<?php

namespace App\DTO;

class GetMeResponseDTO
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly array $roles,
    )
    {

    }
}