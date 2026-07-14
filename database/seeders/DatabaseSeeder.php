<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Infrastructure\Beneficiaries\Models\BeneficiaryModel;
use Infrastructure\Beneficiaries\Models\FamilyCardModel;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user if not exists
        User::firstOrCreate(User::factory()->definition());

        // die;

        // Create family cards
        $familyCards = FamilyCardModel::factory(25)->create();
        $this->command->info('Created '.$familyCards->count().' family cards.');

        // Create beneficiaries with various types
        $beneficiaries = BeneficiaryModel::factory(100)->create();
        $this->command->info('Created '.$beneficiaries->count().' beneficiaries.');

        // Show type distribution
        $types = $beneficiaries->groupBy('type')->map->count();
        foreach ($types as $type => $count) {
            $this->command->info(sprintf('  - %s: %s', $type, $count));
        }
    }
}
