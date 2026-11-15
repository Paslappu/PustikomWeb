<?php

namespace Tests\Feature;

use App\Models\Gedung;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GedungValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_building_name_is_allowed_on_different_buildings(): void
    {
        Gedung::create([
            'name' => 'Kuliah 1',
            'lokasi' => 'Lantai 1',
        ]);

        $response = $this->post(route('admin.gedung.store'), [
            'name' => 'Kuliah 1',
            'lokasi' => 'Lantai 2',
            'parent_id' => null,
        ]);

        $response->assertRedirect(route('admin.gedung'));
        $this->assertDatabaseCount('gedungs', 2);
    }
}
