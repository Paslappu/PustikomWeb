<?php

namespace Database\Seeders;

use App\Models\CategoryDana;
use App\Models\Device;
use App\Models\Tipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all device types and category dana
        $tipes = Tipe::all();
        $categoryDanas = CategoryDana::all();
        
        // If no types or categories exist, create at least one of each
        if ($tipes->isEmpty()) {
            $tipes = collect([
                Tipe::create([
                    'name' => 'Access Point',
                    'slug' => 'access-point',
                    'isRuckus' => true
                ]),
                Tipe::create([
                    'name' => 'Router',
                    'slug' => 'router',
                    'isRuckus' => false
                ]),
                Tipe::create([
                    'name' => 'Switch',
                    'slug' => 'switch',
                    'isRuckus' => false
                ])
            ]);
        }
        
        if ($categoryDanas->isEmpty()) {
            $categoryDanas = collect([
                CategoryDana::create([
                    'name' => 'PNBP',
                ]),
                CategoryDana::create([
                    'name' => 'BLU',
                ]),
                CategoryDana::create([
                    'name' => 'BOPTN',
                ])
            ]);
        }
        
        // AP data from the provided JSON
        $apData = [
            [
                "apMac" => "00:E6:3A:1C:85:A0",
                "apSerial" => "222322022353"
            ],
            [
                "apMac" => "00:E6:3A:1E:08:F0",
                "apSerial" => "232322000210"
            ],
            [
                "apMac" => "00:E6:3A:1E:89:50",
                "apSerial" => "232322002264"
            ],
            [
                "apMac" => "00:E6:3A:1E:DA:A0",
                "apSerial" => "232322003565"
            ],
            [
                "apMac" => "00:E6:3A:1E:DB:A0",
                "apSerial" => "232322003581"
            ],
            [
                "apMac" => "00:E6:3A:1E:F8:A0",
                "apSerial" => "232322004045"
            ],
            [
                "apMac" => "00:E6:3A:1E:F9:A0",
                "apSerial" => "232322004061"
            ],
            [
                "apMac" => "00:E6:3A:1F:0B:20",
                "apSerial" => "232322004341"
            ],
            [
                "apMac" => "00:E6:3A:1F:0F:C0",
                "apSerial" => "232322004415"
            ],
            [
                "apMac" => "00:E6:3A:1F:2F:B0",
                "apSerial" => "232322004926"
            ],
            [
                "apMac" => "00:E6:3A:22:D8:50",
                "apSerial" => "232322019912"
            ],
            [
                "apMac" => "00:E6:3A:23:46:F0",
                "apSerial" => "232322021682"
            ],
            [
                "apMac" => "00:E6:3A:23:63:80",
                "apSerial" => "232322022139"
            ],
            [
                "apMac" => "00:E6:3A:23:6D:90",
                "apSerial" => "232322022300"
            ],
            [
                "apMac" => "00:E6:3A:23:96:D0",
                "apSerial" => "232322022960"
            ],
            [
                "apMac" => "C8:A6:08:04:E6:20",
                "apSerial" => "152322004992"
            ],
            [
                "apMac" => "D0:4F:58:09:21:A0",
                "apSerial" => "992322025530"
            ]
        ];
        
        // Create a device for each AP
        foreach ($apData as $ap) {
            // Get random type and category
            $randomTipe = $tipes->random();
            $randomCategory = $categoryDanas->random();
            
            // Create the device with the AP serial as device_id
            Device::create(attributes: [
                'device_id' => $ap['apMac'],
                'tipe_id' => $randomTipe->id,
                'category_dana_id' => $randomCategory->id,
                'isActive' => true,
                'sticker' => 'default-sticker.jpg' // Default sticker file
            ]);
            
            // Output created device info
            $this->command->info("Created device with ID: {$ap['apSerial']}");
        }
        
        $this->command->info('All devices created successfully!');
    }
}
