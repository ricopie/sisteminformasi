<?php

namespace App\ValueObjects;

use App\Concern\RecursivelyConversToArray;
use Illuminate\Contracts\Support\Arrayable;
use Modules\Beneficiary\Enums\EducationLevel;

final class Person implements Arrayable
{
    use RecursivelyConversToArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private string $name,
        private ?string $occupation,
        private ?EducationLevel $education,
        private ?Address $address,
        private ?Contact $contact
    ) {}
}
