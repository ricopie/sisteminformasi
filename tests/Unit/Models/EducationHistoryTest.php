<?php

namespace Tests\Unit\Models;

use App\Models\EducationHistory;
use App\Models\FosterChild;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_foster_child(): void
    {
        $educationHistory = EducationHistory::factory()->create();

        $this->assertInstanceOf(FosterChild::class, $educationHistory->fosterChild);
    }
}
