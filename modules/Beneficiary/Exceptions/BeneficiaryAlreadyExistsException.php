<?php

namespace Modules\Beneficiary\Exceptions;

use RuntimeException;

class BeneficiaryAlreadyExistsException extends RuntimeException
{
    public function __construct(string $nik)
    {
        parent::__construct("Beneficiary with NIK '{$nik}' already exists.");
    }
}
