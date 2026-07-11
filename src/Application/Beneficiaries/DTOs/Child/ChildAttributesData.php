<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs\Child;

use Spatie\LaravelData\Data;

class ChildAttributesData extends Data
{
    public function __construct(
        public EducationData $education,
        /** @var EducationData[] */
        public array $educationHistory = [],
        /** @var string[] */
        public array $hobbies = [],
    ) {}
}
