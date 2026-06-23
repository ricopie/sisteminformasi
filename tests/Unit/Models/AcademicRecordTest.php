<?php

namespace Tests\Unit\Models;

use App\Models\AcademicRecord;
use App\Models\FosterChild;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_foster_child(): void
    {
        $academicRecord = AcademicRecord::factory()->create();

        $this->assertInstanceOf(FosterChild::class, $academicRecord->fosterChild);
    }
}
