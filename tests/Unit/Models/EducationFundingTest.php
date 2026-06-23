<?php

namespace Tests\Unit\Models;

use App\Models\EducationFunding;
use App\Models\FosterChild;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationFundingTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_foster_child(): void
    {
        $educationFunding = EducationFunding::factory()->create();

        $this->assertInstanceOf(FosterChild::class, $educationFunding->fosterChild);
    }
}
