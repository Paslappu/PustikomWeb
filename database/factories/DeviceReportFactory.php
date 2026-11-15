<?php

namespace Database\Factories;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceReport>
 */
class DeviceReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'device_id' => Device::factory(),
            'user_id' => User::factory(),
            'gedung_id' => null,
            'captured_at' => now()->subHour(),
            'latitude' => 0.5412000,
            'longitude' => 123.0595000,
            'notes' => fake()->sentence(),
        ];
    }
}
