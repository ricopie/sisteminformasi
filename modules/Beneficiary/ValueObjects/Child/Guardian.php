<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConversToArray;
use App\ValueObjects\Contact;
use Illuminate\Contracts\Support\Arrayable;

final class Guardian implements Arrayable
{
    use RecursivelyConversToArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private string $name,
        private string $relationship,
        private ?Contact $contact = null,
    ) {}
}
