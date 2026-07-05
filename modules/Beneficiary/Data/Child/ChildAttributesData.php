<?php

namespace Modules\Beneficiary\Data\Child;

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
