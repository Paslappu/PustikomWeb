<?php

// app/Exports/DeviceExport.php
namespace App\Exports;

use App\Models\Device;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeviceExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        // Fetch devices with their latest location
        $devices = Device::with(['location' => function ($query) {
            $query->latest()->first();  // Get the latest location for each device
        }])->get();

        $data = $devices->map(function ($device) {
            // Get the latest location if exists
            $latestLocation = $device->location->first();
            $location = $latestLocation ? $latestLocation->location : null;
            $building = $latestLocation && $latestLocation->gedung ? $latestLocation->gedung->name : null;

            return [
                'device_id' => $device->device_id,
                'tipe' => $device->tipe->name ?? 'N/A',
                'is_active' => $device->isActive ? 'Active' : 'Inactive',
                'building' => $building,
            ];
        });

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Device ID',
            'Tipe Device',
            'Status',
            'Gedung',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            ],
            // Set the column width for better presentation
            'A' => ['width' => 15],
            'B' => ['width' => 20],
            'C' => ['width' => 15],
            'D' => ['width' => 25],
            'E' => ['width' => 20],
            // Style the data rows
            'A2:E1000' => [
                'font' => ['size' => 12],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Devices Data';  // Sheet title
    }
}
