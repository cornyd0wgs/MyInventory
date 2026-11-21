<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class BarcodeLookup
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string $barcode = null,
    ) {
    }
}
