<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConversToArray;
use Illuminate\Contracts\Support\Arrayable;

final class ChildAttributes implements Arrayable
{
    use RecursivelyConversToArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private Guardian $guardian,
        private Education $education,
        private array $hobbies = [],
        private ?string $entry_date = null,
    ) {}
}
