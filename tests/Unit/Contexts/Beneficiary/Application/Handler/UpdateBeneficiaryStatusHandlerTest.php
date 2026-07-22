<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Handler;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Application\Command\UpdateBeneficiaryStatusCommand;
use Copie\Contexts\Beneficiary\Application\Handler\UpdateBeneficiaryStatusHandler;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Enums\EducationStatus;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Education;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Shared\Domain\Enums\EducationLevel;
use Copie\Shared\Domain\Enums\Gender;
use Copie\Shared\Domain\EventDispatcherInterface;
use Copie\Shared\Domain\Exceptions\EntityNotFoundException;
use Copie\Shared\Domain\ValueObjects\DomainId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpdateBeneficiaryStatusHandlerTest extends TestCase
{
    /**
     * @var MockObject&BeneficiaryRepositoryInterface
     */
    private MockObject $beneficiaryRepository;

    /**
     * @var MockObject&EventDispatcherInterface
     */
    private MockObject $eventDispatcher;

    private UpdateBeneficiaryStatusHandler $updateBeneficiaryStatusHandler;

    private Beneficiary $existingBeneficiary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beneficiaryRepository = $this->createMock(BeneficiaryRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->updateBeneficiaryStatusHandler = new UpdateBeneficiaryStatusHandler($this->beneficiaryRepository, $this->eventDispatcher);
        $this->existingBeneficiary = $this->createExistingBeneficiary();
    }

    #[Test]
    public function test_handle_deactivates_beneficiary(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $updateBeneficiaryStatusCommand = new UpdateBeneficiaryStatusCommand(
            id: $this->existingBeneficiary->id()->value,
            isActive: false,
        );

        $this->updateBeneficiaryStatusHandler->handle($updateBeneficiaryStatusCommand);

        $this->assertFalse($this->existingBeneficiary->isActive());
    }

    #[Test]
    public function test_handle_activates_beneficiary(): void
    {
        $this->existingBeneficiary->markAsDeleted();
        $this->existingBeneficiary->pullDomainEvents();

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $updateBeneficiaryStatusCommand = new UpdateBeneficiaryStatusCommand(
            id: $this->existingBeneficiary->id()->value,
            isActive: true,
        );

        $this->updateBeneficiaryStatusHandler->handle($updateBeneficiaryStatusCommand);

        $this->assertTrue($this->existingBeneficiary->isActive());
    }

    #[Test]
    public function test_handle_throws_exception_when_beneficiary_not_found(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $updateBeneficiaryStatusCommand = new UpdateBeneficiaryStatusCommand(
            id: DomainId::generate()->value,
            isActive: false,
        );

        $this->expectException(EntityNotFoundException::class);
        $this->updateBeneficiaryStatusHandler->handle($updateBeneficiaryStatusCommand);
    }

    #[Test]
    public function test_handle_saves_beneficiary_after_status_change(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $updateBeneficiaryStatusCommand = new UpdateBeneficiaryStatusCommand(
            id: $this->existingBeneficiary->id()->value,
            isActive: false,
        );

        $this->updateBeneficiaryStatusHandler->handle($updateBeneficiaryStatusCommand);
    }

    private function createExistingBeneficiary(): Beneficiary
    {
        return Beneficiary::create(
            nationalIdentityNumber: new NationalIdentityNumber('9999999999999999'),
            beneficiaryType: BeneficiaryType::CHILD,
            name: new Name('Existing', 'User'),
            nickName: null,
            birthPlace: 'Jakarta',
            birthDate: '2020-01-01',
            gender: Gender::MALE,
            familyCard: FamilyCard::create('9999999999', 'Existing Head'),
            specificAttributes: new ChildAttributes(
                education: new Education(
                    level: EducationLevel::ELEMENTARY,
                    status: EducationStatus::ENROLLED,
                    schoolName: 'SD Test',
                    grade: 1,
                    nisn: '1111111111'
                ),
                educationHistory: [],
                hobbies: []
            ),
        );
    }
}
