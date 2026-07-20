<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Application\Command\CreateBeneficiaryCommand;
use Copie\Contexts\Beneficiary\Application\Command\CreateBeneficiaryHandler;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Enums\EducationStatus;
use Copie\Contexts\Beneficiary\Domain\Exceptions\BeneficiaryAlreadyExistsException;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Education;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Shared\Domain\Enums\EducationLevel;
use Copie\Shared\Domain\Enums\Gender;
use Copie\Shared\Domain\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateBeneficiaryHandlerTest extends TestCase
{
    /**
     * @var MockObject&BeneficiaryRepositoryInterface
     */
    private MockObject $beneficiaryRepository;

    private MockObject $eventDispatcher;

    private CreateBeneficiaryHandler $createBeneficiaryHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beneficiaryRepository = $this->createMock(BeneficiaryRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->createBeneficiaryHandler = new CreateBeneficiaryHandler($this->beneficiaryRepository, $this->eventDispatcher);
    }

    #[Test]
    public function test_handle_creates_beneficiary_and_saves(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findByNik')
            ->willReturn(null);

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function ($beneficiary): void {
                $this->assertInstanceOf(Beneficiary::class, $beneficiary);
            });

        $createBeneficiaryCommand = CreateBeneficiaryCommand::fromArray($this->validCommandData());
        $beneficiary = $this->createBeneficiaryHandler->handle($createBeneficiaryCommand);

        $this->assertInstanceOf(Beneficiary::class, $beneficiary);
        $this->assertSame('3201234567890001', $beneficiary->nik()->value);
        $this->assertSame(BeneficiaryType::CHILD, $beneficiary->type());
        $this->assertSame('Budi', $beneficiary->name()->firstName);
        $this->assertSame('Santoso', $beneficiary->name()->lastName);
        $this->assertSame('Bandung', $beneficiary->birthPlace());
        $this->assertSame('2015-06-15', $beneficiary->birthDate());
        $this->assertSame(Gender::MALE, $beneficiary->gender());
        $this->assertSame('320123456789001', $beneficiary->familyCard()->number());
        $this->assertSame('Santoso', $beneficiary->familyCard()->headOfFamilyName());

    }

    #[Test]
    public function test_handle_throws_exception_when_nik_already_exists(): void
    {
        $existingBeneficiary = $this->createExistingBeneficiary();

        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findByNik')
            ->willReturn($existingBeneficiary);

        $createBeneficiaryCommand = CreateBeneficiaryCommand::fromArray($this->validCommandData());

        $this->expectException(BeneficiaryAlreadyExistsException::class);
        $this->createBeneficiaryHandler->handle($createBeneficiaryCommand);
    }

    #[Test]
    public function test_handle_creates_family_card_from_command(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findByNik')
            ->willReturn(null);

        $createBeneficiaryCommand = CreateBeneficiaryCommand::fromArray($this->validCommandData());
        $beneficiary = $this->createBeneficiaryHandler->handle($createBeneficiaryCommand);

        $this->assertSame('320123456789001', $beneficiary->familyCard()->number());
        $this->assertSame('Santoso', $beneficiary->familyCard()->headOfFamilyName());
    }

    #[Test]
    public function test_handle_resolves_child_attributes_when_type_is_child(): void
    {
        $this->beneficiaryRepository
            ->expects($this->once())
            ->method('findByNik')
            ->willReturn(null);

        $createBeneficiaryCommand = CreateBeneficiaryCommand::fromArray($this->validCommandData());
        $beneficiary = $this->createBeneficiaryHandler->handle($createBeneficiaryCommand);

        $this->assertInstanceOf(ChildAttributes::class, $beneficiary->specificAttributes());
        $this->assertInstanceOf(Education::class, $beneficiary->specificAttributes()->education);
        $this->assertSame(EducationLevel::ELEMENTARY, $beneficiary->specificAttributes()->education->level);
        $this->assertSame(EducationStatus::ENROLLED, $beneficiary->specificAttributes()->education->status);
        $this->assertSame('SD Negeri 1', $beneficiary->specificAttributes()->education->schoolName);
        $this->assertSame(3, $beneficiary->specificAttributes()->education->grade);
        $this->assertSame('0012345678', $beneficiary->specificAttributes()->education->nisn);
        $this->assertSame(['Drawing'], $beneficiary->specificAttributes()->hobbies);
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
                    level: EducationLevel::JUNIOR_HIGH,
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

    private function validCommandData(): array
    {
        return [
            'nik' => '3201234567890001',
            'type' => 'child',
            'firstName' => 'Budi',
            'lastName' => 'Santoso',
            'nickName' => null,
            'birthPlace' => 'Bandung',
            'birthDate' => '2015-06-15',
            'gender' => 'male',
            'familyCardNumber' => '320123456789001',
            'headOfFamilyName' => 'Santoso',
            'specificAttributes' => [
                'education' => [
                    'level' => 'elementary',
                    'status' => 'enrolled',
                    'schoolName' => 'SD Negeri 1',
                    'grade' => 3,
                    'major' => null,
                    'nisn' => '0012345678',
                ],
                'educationHistory' => [],
                'hobbies' => ['Drawing'],
            ],
        ];
    }
}
