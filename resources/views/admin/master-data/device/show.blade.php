@extends('admin.layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .device-card {
            margin-bottom: 30px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 50px;
            color: white;
            font-weight: bold;
        }

        .status-active {
            background-color: #28a745;
        }

        .status-inactive {
            background-color: #6c757d;
        }

        .marker {
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: red;
            border-radius: 50%;
            border: 2px solid white;
            transform: translate(-50%, -50%);
        }

        .qr-container {
            margin-top: 20px;
            text-align: center;
        }

        .location-container {
            margin-top: 20px;
            position: relative;
            width: 100%;
            /* Tetapkan tinggi tetap atau biarkan auto sesuai kebutuhan */
            height: auto;
        }

        .location-container img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .location-container h5 {
            position: absolute;
            top: 10px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        table tr td {
            padding: 8px 15px;
        }

        .action-buttons a, .action-buttons button {
            margin-right: 10px;
        }

        .breadcrumb-item a {
            color: #5e5e5e;
        }

        /* Styles for Ruckus AP Info */
        .section-title {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .info-item {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #eee;
            padding-bottom: 5px;
        }
        .info-label {
            font-weight: 500;
            color: #555;
        }
        .info-value {
            font-weight: 600;
            color: #333;
        }
        .card-header {
            padding: 12px 15px;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? 'Device Details' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.' . $active) }}" class="text-muted">{{ $title ?? 'Device Details' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? 'Overview' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="shop-detail">
        <div class="row">
            <!-- Left Section: Device Info -->
            <div class="col-lg-8 device-card">
                <div class="card shadow-none border">
                    <div class="card-body p-4">
                        <a href="{{ route('admin.' . $active) }}" class="btn btn-sm btn-dark mb-3">
                            <i class="ti ti-arrow-left"></i> Back to {{ $title ?? '' }}
                        </a>

                        <div class="device-content">
                            <h5 class="fw-semibold mb-3">{{ $device->device_id ?? 'N/A' }}</h5>

                            <p><strong>Device Type:</strong> {{ $device->tipe->name ?? 'N/A' }}</p>

                            @if ($device->tipe->file)
                                <model-viewer class="product-3d-viewer mb-3" alt="model" src="{{ $device->tipe->getFileUrlAttribute() }}" shadow-intensity="1" camera-controls touch-action="pan-y"></model-viewer>
                            @else
                                <p>No 3D model available for this device type.</p>
                            @endif

                            <!-- Status Section -->
                            <p><strong>Status:</strong>
                                <span class="status-badge {{ $device->isActive ? 'status-active' : 'status-inactive' }}">
                                    {{ $device->isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </p>

                            <!-- Ruckus AP Info Section -->
                            @if(isset($apSummary) && is_array($apSummary))
                            <div class="card mb-4">
                                <div class="card-header bg-white border-0">
                                    <span class="section-title"><i class="ti ti-wifi"></i> Info AP Ruckus</span>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="info-item"><span class="info-label"><i class="ti ti-tag"></i> Nama AP</span><span class="info-value">{{ $apSummary['name'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-barcode"></i> Serial</span><span class="info-value">{{ $apSummary['serial'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-device-desktop"></i> Model</span><span class="info-value">{{ $apSummary['model'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-network"></i> IP</span><span class="info-value">{{ $apSummary['ip'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-versions"></i> Version</span><span class="info-value">{{ $apSummary['version'] ?? '-' }}</span></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item"><span class="info-label"><i class="ti ti-wifi"></i> Channel 2.4Ghz</span><span class="info-value">{{ $apSummary['wifi24Channel'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-wifi"></i> Channel 5Ghz</span><span class="info-value">{{ $apSummary['wifi50Channel'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-link"></i> Connection</span><span class="info-value">{{ $apSummary['connectionState'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-user-check"></i> Registration</span><span class="info-value">{{ $apSummary['registrationState'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-clock"></i> Uptime</span><span class="info-value">{{ isset($apSummary['uptime']) ? gmdate('H:i:s', $apSummary['uptime']) : '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-users"></i> Client Count</span><span class="info-value">{{ $apSummary['clientCount'] ?? '-' }}</span></div>
                                            <div class="info-item"><span class="info-label"><i class="ti ti-history"></i> Last Seen</span><span class="info-value">{{ isset($apSummary['lastSeenTime']) ? \Carbon\Carbon::createFromTimestampMs($apSummary['lastSeenTime'])->format('d M Y H:i') : '-' }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- QR Code Section -->
                            <div class="qr-container">
                                <h5>QR Code:</h5>
                                {!! $device->qr !!}
                            </div>

                            <!-- Device Location -->
                            @if ($gedung && $gedung->photo)
                                <div class="location-container" id="location-container">
                                    <h5>Device Location</h5>
                                    <img src="{{ asset('uploads/gedung/photo/' . $gedung->photo) }}" alt="Building Map" id="building-map">
                                    @if ($x && $y)
                                        {{-- Asumsikan $x dan $y sudah disimpan dalam format persen (misalnya: 50 untuk 50%) --}}
                                        <div class="marker" style="left: {{ $x }}%; top: {{ $y }}%;"></div>
                                    @endif
                                </div>
                                <p><strong>Coordinates:</strong> {{ $latestLocation ? $latestLocation->location : 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Section: Additional Info -->
            <div class="col-lg-4 device-card">
                <div class="card shadow-none border">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold">Additional Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Device ID</strong></td>
                                <td>{{ $device->device_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Device Type</strong></td>
                                <td>{{ $device->tipe->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Building</strong></td>
                                <td>{{ $gedung->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>{{ $device->isActive ? 'Active' : 'Inactive' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Location</strong></td>
                                <td>{{ $latestLocation ? $latestLocation->location : 'N/A' }}</td>
                            </tr>
                        </table>

                        <!-- History Location Section -->
                        <h5 class="fw-semibold mt-4">Location History</h5>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Building</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($locationHistory as $history)
                                    <tr>
                                        <td>{{ $history->gedung->name ?? 'N/A' }}</td>
                                        <td>{{ $history->location ?? 'N/A' }}</td>
                                        <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No location history available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="action-buttons mt-3">
                            <a href="{{ route('admin.' . $active . '.edit', $device->id) }}" class="btn btn-warning">
                                <i class="ti ti-pencil"></i> Edit Device
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#moveDeviceLocationModal"
                                data-device-id="{{ $device->id }}">
                                <i class="ti ti-map-pin"></i> Move Device Building
                            </button>
                            <form action="{{ route('admin.' . $active . '.delete', $device->id) }}" method="post" class="d-inline">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="ti ti-trash"></i> Delete Device
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Modal for Moving Device Location on Device Detail -->
<div class="modal fade" id="moveDeviceLocationModal" tabindex="-1" aria-labelledby="moveDeviceLocationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveDeviceLocationModalLabel">Move Device Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.device.addLocation', $device->id) }}" method="POST"
                id="moveDeviceLocationForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="device_id" id="device-id-input">
                    <div class="mb-3">
                        <label for="device_show_gedung_id" class="form-label">Building</label>
                        <select name="gedung_id" id="device_show_gedung_id" class="form-control" required>
                            <option value="" selected hidden>-- Select Building --</option>
                            @foreach ($gedungs as $gedung)
                                <option value="{{ $gedung->id }}"
                                    data-image="{{ asset('uploads/gedung/photo/' . $gedung->photo) }}">
                                    {{ $gedung->display_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="building-image-container-detail" style="display:none; position:relative;">
                        <label for="location" class="form-label">Building Image</label>
                        <div id="building-image-detail"></div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location (Click on the image to set location)</label>
                        <input type="hidden" name="location" id="device_show_location" class="form-control" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Location</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
    
    <script>
        // Jika nilai $x dan $y memang sudah berupa persentase, marker akan otomatis
        // berada di posisi yang tepat relatif terhadap container (dengan CSS absolute).
        // Karena gambar ditampilkan full-width dan container responsif, marker tidak perlu dihitung ulang.

        let detailMarker = null;

        document.getElementById('device_show_gedung_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const buildingImageUrl = selectedOption.getAttribute('data-image');
            const buildingImageContainer = document.getElementById('building-image-container-detail');
            const buildingImage = document.getElementById('building-image-detail');

            if (buildingImageUrl) {
                buildingImageContainer.style.display = 'block';
                buildingImage.innerHTML = `<img src="${buildingImageUrl}" id="building-map-detail" alt="Building Image" style="max-width: 100%; height: auto; cursor: crosshair;" />`;

                document.getElementById('device_show_location').value = '';

                const buildingMap = document.getElementById('building-map-detail');
                buildingMap.addEventListener('click', function(e) {
                    const rect = buildingMap.getBoundingClientRect();
                    const clickX = e.clientX - rect.left;
                    const clickY = e.clientY - rect.top;
                    const percentX = (clickX / buildingMap.clientWidth) * 100;
                    const percentY = (clickY / buildingMap.clientHeight) * 100;

                    document.getElementById('device_show_location').value = `${percentX.toFixed(2)},${percentY.toFixed(2)}`;

                    if (detailMarker) {
                        detailMarker.remove();
                    }

                    detailMarker = document.createElement('div');
                    detailMarker.style.position = 'absolute';
                    detailMarker.style.left = `calc(${percentX.toFixed(2)}% - 10px)`;
                    detailMarker.style.top = `calc(${percentY.toFixed(2)}% - 10px)`;
                    detailMarker.style.width = '20px';
                    detailMarker.style.height = '20px';
                    detailMarker.style.backgroundColor = 'red';
                    detailMarker.style.borderRadius = '50%';
                    detailMarker.style.border = '2px solid white';
                    detailMarker.style.pointerEvents = 'none';

                    buildingMap.parentElement.appendChild(detailMarker);
                });
            }
        });
    </script>
@endpush
