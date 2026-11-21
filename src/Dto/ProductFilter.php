<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductFilter
{
    public function __construct(
        public ?string $q = null,
        #[Assert\Positive]
        public ?int $category = null,
    ) {
    }
}
