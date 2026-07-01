<?php

namespace Tests\Faker;

use Faker\Provider\id_ID\Address;

class IndonesiaAddressHelper
{
    protected static array $cities = [];

    protected static array $provinces = [];

    public static function init(): void
    {
        // Hanya muat data jika array masih kosong (biar hemat memori)
        if (empty(self::$cities)) {
            $reflection = new \ReflectionClass(Address::class);
            self::$cities = $reflection->getProperty('cityNames')->getValue();
            self::$provinces = $reflection->getProperty('state')->getValue();
        }
    }

    public static function village(): string
    {
        self::init();

        return 'Kelurahan '.fake()->randomElement(self::$cities);
    }

    public static function district(): string
    {
        self::init();

        return 'Kecamatan '.fake()->randomElement(self::$cities);
    }

    public static function province(): string
    {
        self::init();

        return fake()->randomElement(self::$provinces);
    }
}
