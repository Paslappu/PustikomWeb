<?php

namespace Database\Factories;

use App\Models\CategoryDana;
use App\Models\Tipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipe_id' => Tipe::factory(),
            'category_dana_id' => CategoryDana::factory(),
            'device_id' => strtoupper(fake()->unique()->bothify('DEV-####??')),
            'isActive' => true,
            'sticker' => 'sticker.png', // kolom NOT NULL, file tidak perlu ada
        ];
    }
}
