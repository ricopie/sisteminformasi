<?php

namespace Tests\Unit\Models;

use App\Models\ChildEducation;
use App\Models\FosterChild;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildEducationTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_foster_child(): void
    {
        $childEducation = ChildEducation::factory()->create();

        $this->assertInstanceOf(FosterChild::class, $childEducation->fosterChild);
    }
}
