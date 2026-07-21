<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Query;

use Copie\Contexts\Beneficiary\Application\BeneficiaryQueryInterface;
use Copie\Contexts\Beneficiary\Application\Query\GetBeneficiaryHandler;
use Copie\Contexts\Beneficiary\Application\Query\GetBeneficiaryQuery;
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
use Copie\Shared\Domain\ValueObjects\DomainId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetBeneficiaryHandlerTest extends TestCase
{
    /**
     * @var MockObject&BeneficiaryQueryInterface
     */
    private MockObject $beneficiaryQuery;

    private GetBeneficiaryHandler $getBeneficiaryHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beneficiaryQuery = $this->createMock(BeneficiaryQueryInterface::class);
        $this->getBeneficiaryHandler = new GetBeneficiaryHandler($this->beneficiaryQuery);
    }

    #[Test]
    public function test_handle_returns_beneficiary_when_found(): void
    {
        $beneficiary = $this->createExistingBeneficiary();
        $domainId = DomainId::generate();

        $this->beneficiaryQuery
            ->expects($this->once())
            ->method('findById')
            ->with($domainId)
            ->willReturn($beneficiary);

        $getBeneficiaryQuery = new GetBeneficiaryQuery($domainId->value);
        $result = $this->getBeneficiaryHandler->handle($getBeneficiaryQuery);

        $this->assertSame($beneficiary, $result);
    }

    #[Test]
    public function test_handle_returns_null_when_not_found(): void
    {
        $domainId = DomainId::generate();

        $this->beneficiaryQuery
            ->expects($this->once())
            ->method('findById')
            ->with($domainId)
            ->willReturn(null);

        $getBeneficiaryQuery = new GetBeneficiaryQuery($domainId->value);
        $result = $this->getBeneficiaryHandler->handle($getBeneficiaryQuery);

        $this->assertNull($result);
    }

    #[Test]
    public function test_handle_delegates_to_query_with_correct_id(): void
    {
        $domainId = DomainId::generate();
        $getBeneficiaryQuery = new GetBeneficiaryQuery($domainId->value);

        $this->beneficiaryQuery
            ->expects($this->exactly(1))
            ->method('findById')
            ->with($domainId);

        $this->getBeneficiaryHandler->handle($getBeneficiaryQuery);
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
