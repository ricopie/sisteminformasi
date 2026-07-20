<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Application\Command\UpdateBeneficiaryCommand;
use Copie\Contexts\Beneficiary\Application\Command\UpdateBeneficiaryHandler;
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
use Copie\Shared\Domain\ValueObjects\DomainId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UpdateBeneficiaryHandlerTest extends TestCase
{
    /**
     * @var MockObject&BeneficiaryRepositoryInterface
     */
    private MockObject $beneficiaryRepository;

    private MockObject $eventDispatcher;

    private UpdateBeneficiaryHandler $updateBeneficiaryHandler;

    private Beneficiary $existingBeneficiary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beneficiaryRepository = $this->createMock(BeneficiaryRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->updateBeneficiaryHandler = new UpdateBeneficiaryHandler($this->beneficiaryRepository, $this->eventDispatcher);
        $this->existingBeneficiary = $this->createExistingBeneficiary();
    }

    #[Test]
    public function test_handle_throws_exception_when_beneficiary_not_found(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $validId = DomainId::generate()->value;
        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray(['id' => $validId]);

        $this->expectException(RuntimeException::class);
        $this->updateBeneficiaryHandler->handle($updateBeneficiaryCommand);
    }

    #[Test]
    public function test_handle_updates_name_when_provided(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray([
            'id' => $this->existingBeneficiary->id()->value,
            'firstName' => 'NewName',
        ]);

        $this->updateBeneficiaryHandler->handle($updateBeneficiaryCommand);

        $this->assertSame('NewName', $this->existingBeneficiary->name()->firstName);
        $this->assertSame('User', $this->existingBeneficiary->name()->lastName);
    }

    #[Test]
    public function test_handle_updates_gender_when_provided(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray([
            'id' => $this->existingBeneficiary->id()->value,
            'gender' => 'female',
        ]);

        $this->updateBeneficiaryHandler->handle($updateBeneficiaryCommand);

        $this->assertSame(Gender::FEMALE, $this->existingBeneficiary->gender());
    }

    #[Test]
    public function test_handle_updates_family_card_when_provided(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray([
            'id' => $this->existingBeneficiary->id()->value,
            'familyCardNumber' => 'NEW_CARD',
        ]);

        $this->updateBeneficiaryHandler->handle($updateBeneficiaryCommand);

        $this->assertSame('NEW_CARD', $this->existingBeneficiary->familyCard()->number());
    }

    #[Test]
    public function test_handle_replaces_guardians_when_provided(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($this->existingBeneficiary);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->existingBeneficiary);

        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray([
            'id' => $this->existingBeneficiary->id()->value,
            'guardians' => [
                [
                    'person' => ['name' => 'New Guardian'],
                    'relationship' => 'father',
                ],
            ],
        ]);

        $this->updateBeneficiaryHandler->handle($updateBeneficiaryCommand);

        $this->assertCount(1, $this->existingBeneficiary->guardians());
        $this->assertSame('New Guardian', $this->existingBeneficiary->guardians()[0]->person()->name);
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
