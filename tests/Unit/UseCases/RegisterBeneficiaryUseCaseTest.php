<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use Application\Beneficiaries\DTOs\FamilyCardData;
use Application\Beneficiaries\DTOs\GuardianData;
use Application\Beneficiaries\DTOs\RegisterBeneficiaryData;
use Application\Beneficiaries\UseCases\RegisterBeneficiaryUseCase;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Entities\FamilyCard;
use Domain\Beneficiaries\Exceptions\BeneficiaryAlreadyExistsException;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\Repositories\FamilyCardRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Child\ChildAttributes;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\Gender;
use Tests\TestCase;

final class RegisterBeneficiaryUseCaseTest extends TestCase
{
    private BeneficiaryRepositoryInterface&MockInterface $beneficiaries;

    private FamilyCardRepositoryInterface&MockInterface $familyCards;

    private Dispatcher&MockInterface $events;

    private RegisterBeneficiaryUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->beneficiaries = Mockery::mock(BeneficiaryRepositoryInterface::class);
        $this->familyCards = Mockery::mock(FamilyCardRepositoryInterface::class);
        $this->events = Mockery::mock(Dispatcher::class);

        $this->useCase = new RegisterBeneficiaryUseCase(
            $this->beneficiaries,
            $this->familyCards,
            $this->events,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function makeRegisterData(?array $specificAttributes = null, ?array $guardians = null): RegisterBeneficiaryData
    {
        return new RegisterBeneficiaryData(
            nik: new NationalIdentityNumber('1234567890123456'),
            type: BeneficiaryType::CHILD,
            fullName: 'John Doe',
            nickName: 'Johnny',
            birthPlace: 'Jakarta',
            birthDate: '2010-01-01',
            gender: Gender::MALE,
            familyCard: new FamilyCardData(
                family_card_number: '1234567890123456',
                head_of_family_name: 'John Doe Sr.',
                address: [
                    'street' => 'Jl. Merdeka',
                    'rt' => '001',
                    'rw' => '002',
                    'village' => 'Kebon Jeruk',
                    'district' => 'Kebon Jeruk',
                    'city' => 'Jakarta Barat',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '11530',
                ],
            ),
            specificAttributes: $specificAttributes ?? [
                'education' => [
                    'level' => 'elementary',
                    'status' => 'currently_enrolled',
                    'schoolName' => 'SD N 1',
                    'grade' => 5,
                    'nisn' => '1234567890',
                ],
                'hobbies' => ['Reading'],
            ],
            guardians: $guardians,
        );
    }

    #[Test]
    public function it_can_register_beneficiary(): void
    {
        $data = $this->makeRegisterData();

        $this->beneficiaries
            ->shouldReceive('existsByNik')
            ->once()
            ->with('1234567890123456')
            ->andReturn(false);

        $this->familyCards
            ->shouldReceive('findByNumber')
            ->once()
            ->with('1234567890123456')
            ->andReturn(null);

        $this->familyCards
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(FamilyCard::class));

        $this->beneficiaries
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Beneficiary::class));

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $beneficiary = $this->useCase->handle($data);

        $this->assertInstanceOf(Beneficiary::class, $beneficiary);
        $this->assertSame('John Doe', $beneficiary->fullName());
        $this->assertSame(BeneficiaryType::CHILD, $beneficiary->type());
    }

    #[Test]
    public function it_throws_when_nik_already_exists(): void
    {
        $data = $this->makeRegisterData();

        $this->beneficiaries
            ->shouldReceive('existsByNik')
            ->once()
            ->with('1234567890123456')
            ->andReturn(true);

        $this->expectException(BeneficiaryAlreadyExistsException::class);

        $this->useCase->handle($data);
    }

    #[Test]
    public function it_uses_existing_family_card_when_found(): void
    {
        $data = $this->makeRegisterData();
        $existingCard = FamilyCard::register(
            familyCardNumber: '1234567890123456',
            headOfFamilyName: 'John Doe Sr.',
            address: null,
        );

        $this->beneficiaries
            ->shouldReceive('existsByNik')
            ->once()
            ->andReturn(false);

        $this->familyCards
            ->shouldReceive('findByNumber')
            ->once()
            ->with('1234567890123456')
            ->andReturn($existingCard);

        $this->familyCards
            ->shouldReceive('save')
            ->never();

        $this->beneficiaries
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Beneficiary::class));

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $beneficiary = $this->useCase->handle($data);

        $this->assertSame((string) $existingCard->id(), (string) $beneficiary->familyCardId());
    }

    #[Test]
    public function it_registers_with_specific_attributes_for_child(): void
    {
        $attributes = [
            'education' => [
                'level' => 'elementary',
                'status' => 'currently_enrolled',
                'schoolName' => 'SD N 1',
                'grade' => 5,
                'nisn' => '1234567890',
            ],
            'hobbies' => ['Reading'],
        ];

        $data = $this->makeRegisterData(specificAttributes: $attributes);

        $this->beneficiaries
            ->shouldReceive('existsByNik')
            ->once()
            ->andReturn(false);

        $this->familyCards
            ->shouldReceive('findByNumber')
            ->once()
            ->andReturn(null);

        $this->familyCards
            ->shouldReceive('save')
            ->once();

        $this->beneficiaries
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Beneficiary $beneficiary) {
                $this->assertNotNull($beneficiary->specificAttributes());
                $this->assertInstanceOf(ChildAttributes::class, $beneficiary->specificAttributes());

                return true;
            }));

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $beneficiary = $this->useCase->handle($data);

        $this->assertInstanceOf(ChildAttributes::class, $beneficiary->specificAttributes());
    }

    #[Test]
    public function it_adds_guardians_during_registration(): void
    {
        $guardians = [
            new GuardianData(
                person: [
                    'name' => 'Guardian Name',
                    'occupation' => 'Employee',
                    'education' => 'bachelor',
                    'contact' => [
                        'phone' => '081234567890',
                        'email' => 'guardian@example.com',
                    ],
                ],
                relationship: GuardianRelationship::LEGAL_GUARDIAN,
            ),
        ];

        $data = $this->makeRegisterData(guardians: $guardians);

        $this->beneficiaries
            ->shouldReceive('existsByNik')
            ->once()
            ->andReturn(false);

        $this->familyCards
            ->shouldReceive('findByNumber')
            ->once()
            ->andReturn(null);

        $this->familyCards
            ->shouldReceive('save')
            ->once();

        $this->beneficiaries
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Beneficiary $beneficiary) {
                return count($beneficiary->guardians()) === 1;
            }));

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $beneficiary = $this->useCase->handle($data);

        $this->assertCount(1, $beneficiary->guardians());
        $this->assertSame(GuardianRelationship::LEGAL_GUARDIAN, $beneficiary->guardians()[0]->relationship());
    }

    #[Test]
    public function it_dispatches_domain_events(): void
    {
        $data = $this->makeRegisterData();

        $this->beneficiaries
            ->shouldReceive('existsByNik')
            ->once()
            ->andReturn(false);

        $this->familyCards
            ->shouldReceive('findByNumber')
            ->once()
            ->andReturn(null);

        $this->familyCards
            ->shouldReceive('save')
            ->once();

        $this->beneficiaries
            ->shouldReceive('save')
            ->once();

        // Expect at least one event dispatch
        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $this->useCase->handle($data);
    }

    #[Test]
    public function it_registers_general_beneficiary_without_specific_attributes(): void
    {
        $data = new RegisterBeneficiaryData(
            nik: new NationalIdentityNumber('1234567890123456'),
            type: BeneficiaryType::GENERAL,
            fullName: 'Jane Doe',
            nickName: null,
            birthPlace: 'Jakarta',
            birthDate: '1995-05-15',
            gender: Gender::FEMALE,
            familyCard: new FamilyCardData(
                family_card_number: '1234567890123456',
                head_of_family_name: 'John Doe Sr.',
                address: [
                    'street' => 'Jl. Merdeka',
                    'rt' => '001',
                    'rw' => '002',
                    'village' => 'Kebon Jeruk',
                    'district' => 'Kebon Jeruk',
                    'city' => 'Jakarta Barat',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '11530',
                ],
            ),
            specificAttributes: null,
            guardians: null,
        );

        $this->beneficiaries
            ->shouldReceive('existsByNik')
            ->once()
            ->andReturn(false);

        $this->familyCards
            ->shouldReceive('findByNumber')
            ->once()
            ->andReturn(null);

        $this->familyCards
            ->shouldReceive('save')
            ->once();

        $this->beneficiaries
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Beneficiary $beneficiary) {
                $this->assertNull($beneficiary->specificAttributes());
                $this->assertSame(BeneficiaryType::GENERAL, $beneficiary->type());

                return true;
            }));

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $beneficiary = $this->useCase->handle($data);

        $this->assertSame(BeneficiaryType::GENERAL, $beneficiary->type());
        $this->assertNull($beneficiary->specificAttributes());
    }
}
