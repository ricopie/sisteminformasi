<?php

namespace App\ValueObjects;

class Address
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $address,
        public string $rt,
        public string $rw,
        public string $village,
        public string $sub_district,
        public string $city,
        public string $province,
        public string $postal_code
    ) {
        //
    }
}
