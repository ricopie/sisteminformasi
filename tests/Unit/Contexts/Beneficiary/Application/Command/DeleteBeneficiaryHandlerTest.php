<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Application\Command\DeleteBeneficiaryCommand;
use Copie\Contexts\Beneficiary\Application\Command\DeleteBeneficiaryHandler;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Enums\EducationStatus;
use Copie\Contexts\Beneficiary\Domain\Events\BeneficiaryDeleted;
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

class DeleteBeneficiaryHandlerTest extends TestCase
{
    /**
     * @var MockObject&BeneficiaryRepositoryInterface
     */
    private MockObject $beneficiaryRepository;

    private MockObject $eventDispatcher;

    private DeleteBeneficiaryHandler $deleteBeneficiaryHandler;

    private Beneficiary $existingBeneficiary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beneficiaryRepository = $this->createMock(BeneficiaryRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->deleteBeneficiaryHandler = new DeleteBeneficiaryHandler($this->beneficiaryRepository, $this->eventDispatcher);
        $this->existingBeneficiary = $this->createExistingBeneficiary();
    }

    #[Test]
    public function test_handle_marks_beneficiary_as_inactive(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $deleteBeneficiaryCommand = new DeleteBeneficiaryCommand(id: $this->existingBeneficiary->id()->value);

        $this->deleteBeneficiaryHandler->handle($deleteBeneficiaryCommand);

        $this->assertFalse($this->existingBeneficiary->isActive());
    }

    #[Test]
    public function test_handle_throws_exception_when_beneficiary_not_found(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $deleteBeneficiaryCommand = new DeleteBeneficiaryCommand(id: (string) DomainId::generate());

        $this->expectException(EntityNotFoundException::class);
        $this->deleteBeneficiaryHandler->handle($deleteBeneficiaryCommand);
    }

    #[Test]
    public function test_handle_emits_beneficiary_deleted_event(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $this->existingBeneficiary->pullDomainEvents();

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(BeneficiaryDeleted::class));

        $deleteBeneficiaryCommand = new DeleteBeneficiaryCommand(id: $this->existingBeneficiary->id()->value);

        $this->deleteBeneficiaryHandler->handle($deleteBeneficiaryCommand);
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
