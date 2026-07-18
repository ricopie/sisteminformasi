<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Attributes\Digits;
use Copie\Shared\Domain\Attributes\NotBlank;
use Copie\Shared\Domain\ValueObject;
use Stringable;

final class Address extends ValueObject implements Stringable
{
    final public function __construct(
        #[NotBlank(message: 'Street is required')]
        public readonly string $street,

        #[NotBlank(message: 'RT is required')]
        #[Digits(min: 2, max: 3, message: 'RT must be 2-3 digits')]
        public readonly string $rt,

        #[NotBlank(message: 'RW is required')]
        #[Digits(min: 2, max: 3, message: 'RW must be 2-3 digits')]
        public readonly string $rw,

        #[NotBlank(message: 'Village is required')]
        public readonly string $village,

        #[NotBlank(message: 'District is required')]
        public readonly string $district,

        #[NotBlank(message: 'City is required')]
        public readonly string $city,

        #[NotBlank(message: 'Province is required')]
        public readonly string $province,

        #[NotBlank(message: 'Postal code is required')]
        #[Digits(min: 5, max: 5, message: 'Postal code must be 5 digits')]
        public readonly string $postalCode,
    ) {
        $this->validate();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            street: $data['street'],
            rt: $data['rt'],
            rw: $data['rw'],
            village: $data['village'],
            district: $data['district'],
            city: $data['city'],
            province: $data['province'],
            postalCode: $data['postalCode'],
        );
    }

    public function toArray(): array
    {
        return [
            'street' => $this->street,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'village' => $this->village,
            'district' => $this->district,
            'city' => $this->city,
            'province' => $this->province,
            'postalCode' => $this->postalCode,
        ];
    }

    protected function equalize(): array
    {
        return [
            $this->street,
            $this->rt,
            $this->rw,
            $this->village,
            $this->district,
            $this->city,
            $this->province,
            $this->postalCode,
        ];
    }

    public function fullAddress(): string
    {
        return sprintf(
            '%s, RT %s/RW %s, Kel. %s, Kec. %s, %s, %s %s',
            $this->street,
            $this->rt,
            $this->rw,
            $this->village,
            $this->district,
            $this->city,
            $this->province,
            $this->postalCode
        );
    }

    public function __toString(): string
    {
        return $this->fullAddress();
    }
}
