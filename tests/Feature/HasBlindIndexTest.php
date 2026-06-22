<?php

namespace Tests\Feature;

use App\Http\Requests\StoreFamilyCardRequest;
use App\Models\FamilyCard;
use App\Models\FosterChild;
use App\Services\BlindIndexService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class HasBlindIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_family_card_encrypts_number(): void
    {
        $kk = FamilyCard::factory()->create([
            'family_card_number' => '1234567890123456',
        ]);

        $this->assertNotNull($kk->getAttributes()['family_card_number_encrypted']);
        $this->assertNotNull($kk->getAttributes()['family_card_number_hash']);
        $this->assertArrayNotHasKey('family_card_number', $kk->getAttributes());
    }

    public function test_read_family_card_number_decrypts(): void
    {
        $kk = FamilyCard::factory()->create([
            'family_card_number' => '1234567890123456',
        ]);

        $this->assertSame('1234567890123456', $kk->family_card_number);
    }

    public function test_create_foster_child_encrypts_nik(): void
    {
        $kk = FamilyCard::factory()->create();
        $anak = FosterChild::factory()->create([
            'nik' => '1234567890123456',
            'family_card_id' => $kk->id,
        ]);

        $this->assertNotNull($anak->getAttributes()['nik_encrypted']);
        $this->assertNotNull($anak->getAttributes()['nik_hash']);
        $this->assertArrayNotHasKey('nik', $anak->getAttributes());
    }

    public function test_read_nik_decrypts(): void
    {
        $kk = FamilyCard::factory()->create();
        $anak = FosterChild::factory()->create([
            'nik' => '1234567890123456',
            'family_card_id' => $kk->id,
        ]);

        $this->assertSame('1234567890123456', $anak->nik);
    }

    public function test_family_card_hash_is_deterministic(): void
    {
        $kk = FamilyCard::factory()->create(['family_card_number' => '1111111111111111']);
        $service = $this->app->make(BlindIndexService::class);

        $this->assertSame(
            $service->hash('1111111111111111'),
            $kk->getAttributes()['family_card_number_hash']
        );
    }

    public function test_nik_hash_is_deterministic(): void
    {
        $kk = FamilyCard::factory()->create();
        $anak = FosterChild::factory()->create(['nik' => '1111111111111111', 'family_card_id' => $kk->id]);
        $service = $this->app->make(BlindIndexService::class);

        $this->assertSame(
            $service->hash('1111111111111111'),
            $anak->getAttributes()['nik_hash']
        );
    }

    public function test_update_nik_re_encrypts(): void
    {
        $kk = FamilyCard::factory()->create();
        $anak = FosterChild::factory()->create([
            'nik' => '1234567890123456',
            'family_card_id' => $kk->id,
        ]);

        $anak->update(['nik' => '6543210987654321']);

        $this->assertSame('6543210987654321', $anak->fresh()->nik);
    }

    public function test_family_card_has_many_foster_children(): void
    {
        $kk = FamilyCard::factory()
            ->has(FosterChild::factory()->count(3), 'familyMember')
            ->create();

        $this->assertCount(3, $kk->fresh()->familyMember);
    }

    public function test_foster_child_belongs_to_family_card(): void
    {
        $kk = FamilyCard::factory()->create();
        $anak = FosterChild::factory()->create(['family_card_id' => $kk->id]);

        $this->assertTrue($anak->familyCard->is($kk));
    }

    public function test_search_foster_child_by_nik(): void
    {
        $kk = FamilyCard::factory()->create();
        $anak = FosterChild::factory()->create([
            'nik' => '1234567890123456',
            'family_card_id' => $kk->id,
        ]);
        $service = $this->app->make(BlindIndexService::class);

        $found = $service->findByHash(FosterChild::class, 'nik_hash', '1234567890123456');

        $this->assertNotNull($found);
        $this->assertTrue($found->is($anak));
    }

    public function test_search_foster_child_by_nik_returns_null_if_not_found(): void
    {
        $service = $this->app->make(BlindIndexService::class);

        $this->assertNull($service->findByHash(FosterChild::class, 'nik_hash', '1234567890123456'));
    }

    public function test_search_family_card_by_number(): void
    {
        $kk = FamilyCard::factory()->create([
            'family_card_number' => '1234567890123456',
        ]);
        $service = $this->app->make(BlindIndexService::class);

        $found = $service->findByHash(FamilyCard::class, 'family_card_number_hash', '1234567890123456');

        $this->assertNotNull($found);
        $this->assertTrue($found->is($kk));
    }

    public function test_search_works_with_normalized_formatted_input(): void
    {
        $kk = FamilyCard::factory()->create([
            'family_card_number' => '1234567890123456',
        ]);
        $service = $this->app->make(BlindIndexService::class);

        $found = $service->findByHash(FamilyCard::class, 'family_card_number_hash', '1234-5678-9012-3456');

        $this->assertNotNull($found);
        $this->assertTrue($found->is($kk));
    }

    public function test_store_family_card_validation_passes_with_valid_data(): void
    {
        $data = [
            'family_card_number' => '1234567890123456',
            'head_of_family_name' => 'Budi',
            'address' => 'Jl. Merdeka No.1',
            'rt' => '001',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cibeunying',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
        ];

        $request = new StoreFamilyCardRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_store_family_card_validation_fails_if_no_kk_already_exists(): void
    {
        FamilyCard::factory()->create(['family_card_number' => '1234567890123456']);

        $data = [
            'family_card_number' => '1234567890123456',
            'head_of_family_name' => 'Budi',
            'address' => 'Jl. Merdeka No.1',
            'rt' => '001',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cibeunying',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
        ];

        $request = new StoreFamilyCardRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('family_card_number', $validator->errors()->toArray());
    }

    public function test_store_family_card_validation_fails_if_no_kk_not_16_digits(): void
    {
        $data = [
            'family_card_number' => '123456789012345',
            'head_of_family_name' => 'Budi',
            'address' => 'Jl. Merdeka No.1',
            'rt' => '001',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cibeunying',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
        ];

        $request = new StoreFamilyCardRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('family_card_number', $validator->errors()->toArray());
    }

    public function test_store_family_card_validation_fails_if_no_kk_contains_letters(): void
    {
        $data = [
            'family_card_number' => '123456789012345a',
            'head_of_family_name' => 'Budi',
            'address' => 'Jl. Merdeka No.1',
            'rt' => '001',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cibeunying',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
        ];

        $request = new StoreFamilyCardRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('family_card_number', $validator->errors()->toArray());
    }

    public function test_store_family_card_validation_fails_if_rt_more_than_3_digits(): void
    {
        $data = [
            'family_card_number' => '1234567890123456',
            'head_of_family_name' => 'Budi',
            'address' => 'Jl. Merdeka No.1',
            'rt' => '1234',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cibeunying',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
        ];

        $request = new StoreFamilyCardRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('rt', $validator->errors()->toArray());
    }

    public function test_store_family_card_validation_fails_if_postal_code_not_5_digits(): void
    {
        $data = [
            'family_card_number' => '1234567890123456',
            'head_of_family_name' => 'Budi',
            'address' => 'Jl. Merdeka No.1',
            'rt' => '001',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cibeunying',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '4011',
        ];

        $request = new StoreFamilyCardRequest;
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('postal_code', $validator->errors()->toArray());
    }
}
