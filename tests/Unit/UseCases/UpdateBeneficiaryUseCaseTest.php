<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use Application\Beneficiaries\DTOs\FamilyCardData;
use Application\Beneficiaries\DTOs\GuardianData;
use Application\Beneficiaries\DTOs\UpdateBeneficiaryData;
use Application\Beneficiaries\UseCases\UpdateBeneficiaryUseCase;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Entities\FamilyCard;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\Repositories\FamilyCardRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Child\ChildAttributes;
use Domain\Beneficiaries\ValueObjects\Child\Education;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Shared\Exceptions\EntityNotFoundException;
use Shared\ValueObjects\Contact;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Enum\EducationLevel;
use Shared\ValueObjects\Enum\Gender;
use Shared\ValueObjects\Person;
use Tests\TestCase;

final class UpdateBeneficiaryUseCaseTest extends TestCase
{
    private BeneficiaryRepositoryInterface&MockInterface $beneficiaries;

    private FamilyCardRepositoryInterface&MockInterface $familyCards;

    private Dispatcher&MockInterface $events;

    private UpdateBeneficiaryUseCase $useCase;

    private function makeId(): string
    {
        return '01ARZ3NDEKTSV4RRFFQ69G5FAV';
    }

    private function makeBeneficiary(?BeneficiaryType $type = BeneficiaryType::CHILD): Beneficiary
    {
        return Beneficiary::register(
            new NationalIdentityNumber('1234567890123456'),
            $type,
            'John Doe',
            'Johnny',
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            new DomainId('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
            $type === BeneficiaryType::CHILD
                ? new ChildAttributes(
                    education: new Education(
                        level: EducationLevel::ELEMENTARY,
                        status: EducationStatus::CURRENTLY_ENROLLED,
                        schoolName: 'SD N 1',
                        grade: 5,
                        nisn: '1234567890',
                    ),
                    hobbies: ['Reading'],
                )
                : null,
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->beneficiaries = Mockery::mock(BeneficiaryRepositoryInterface::class);
        $this->familyCards = Mockery::mock(FamilyCardRepositoryInterface::class);
        $this->events = Mockery::mock(Dispatcher::class);

        $this->useCase = new UpdateBeneficiaryUseCase(
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

    #[Test]
    public function it_can_update_full_name(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = $this->makeId();

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::on(fn (Beneficiary $b) => $b->fullName() === 'Jane Doe' && $b->nickName() === 'Jane'));

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $data = new UpdateBeneficiaryData(
            fullName: 'Jane Doe',
            nickName: 'Jane',
        );

        $result = $this->useCase->handle($id, $data);

        $this->assertSame('Jane Doe', $result->fullName());
        $this->assertSame('Jane', $result->nickName());
    }

    #[Test]
    public function it_can_update_birth_info(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = $this->makeId();

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('save')
            ->once();

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $data = new UpdateBeneficiaryData(
            birthPlace: 'Bandung',
            birthDate: '2011-02-02',
        );

        $result = $this->useCase->handle($id, $data);

        $this->assertSame('Bandung', $result->birthPlace());
        $this->assertSame('2011-02-02', $result->birthDate());
    }

    #[Test]
    public function it_can_change_gender(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = $this->makeId();

        $this->assertSame(Gender::MALE, $beneficiary->gender());

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('save')
            ->once();

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $data = new UpdateBeneficiaryData(
            gender: Gender::FEMALE,
        );

        $result = $this->useCase->handle($id, $data);

        $this->assertSame(Gender::FEMALE, $result->gender());
    }

    #[Test]
    public function it_can_assign_to_new_family_card(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = $this->makeId();
        $originalCardId = $beneficiary->familyCardId();

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        // Family card not found, will create new one
        $this->familyCards
            ->shouldReceive('findByNumber')
            ->once()
            ->with('9876543210987654')
            ->andReturn(null);

        $this->familyCards
            ->shouldReceive('save')
            ->once()
            ->with(Mockery::type(FamilyCard::class));

        $this->beneficiaries
            ->shouldReceive('save')
            ->once();

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $data = new UpdateBeneficiaryData(
            familyCard: new FamilyCardData(
                family_card_number: '9876543210987654',
                head_of_family_name: 'Jane Doe Sr.',
                address: [
                    'street' => 'Jl. Sudirman',
                    'rt' => '003',
                    'rw' => '004',
                    'village' => 'Menteng',
                    'district' => 'Menteng',
                    'city' => 'Jakarta Pusat',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '10310',
                ],
            ),
        );

        $result = $this->useCase->handle($id, $data);

        $this->assertNotEquals((string) $originalCardId, (string) $result->familyCardId());
    }

    #[Test]
    public function it_can_update_specific_attributes(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = $this->makeId();

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('save')
            ->once();

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $data = new UpdateBeneficiaryData(
            specificAttributes: [
                'education' => [
                    'level' => 'senior_high',
                    'status' => 'currently_enrolled',
                    'schoolName' => 'SMA N 1',
                    'grade' => 10,
                    'nisn' => '0987654321',
                ],
                'hobbies' => ['Swimming', 'Coding'],
            ],
        );

        $result = $this->useCase->handle($id, $data);

        $attributes = $result->specificAttributes();
        $this->assertInstanceOf(ChildAttributes::class, $attributes);

        $array = $attributes->toArray();
        $this->assertSame('SMA N 1', $array['education']['schoolName']);
        $this->assertSame(['Swimming', 'Coding'], $array['hobbies']);
    }

    #[Test]
    public function it_can_replace_guardians(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = $this->makeId();

        // Add an existing guardian first
        $beneficiary->addGuardian(
            person: new Person('Old Guardian', 'Job', null, null, new Contact('081111111111')),
            relationship: GuardianRelationship::LEGAL_GUARDIAN,
        );

        $this->assertCount(1, $beneficiary->guardians());

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('save')
            ->once();

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $data = new UpdateBeneficiaryData(
            guardians: [
                new GuardianData(
                    person: [
                        'name' => 'New Guardian',
                        'occupation' => 'Doctor',
                        'education' => 'master',
                        'contact' => [
                            'phone' => '082222222222',
                            'email' => 'new@example.com',
                        ],
                    ],
                    relationship: GuardianRelationship::LEGAL_GUARDIAN,
                ),
            ],
        );

        $result = $this->useCase->handle($id, $data);

        $this->assertCount(1, $result->guardians());
        $this->assertSame('New Guardian', $result->guardians()[0]->person()->name);
    }

    #[Test]
    public function it_throws_when_beneficiary_not_found(): void
    {
        $id = $this->makeId();

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn(null);

        $this->expectException(EntityNotFoundException::class);

        $data = new UpdateBeneficiaryData;
        $this->useCase->handle($id, $data);
    }

    #[Test]
    public function it_dispatches_domain_events(): void
    {
        $beneficiary = $this->makeBeneficiary();
        $id = $this->makeId();

        $this->beneficiaries
            ->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($beneficiary);

        $this->beneficiaries
            ->shouldReceive('save')
            ->once();

        $this->events
            ->shouldReceive('dispatch')
            ->zeroOrMoreTimes();

        $data = new UpdateBeneficiaryData(
            fullName: 'Jane Doe',
        );

        $this->useCase->handle($id, $data);
    }
}
