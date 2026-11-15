@extends('admin.layouts.app')

@push('styles')
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? '' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title ?? '' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-xl-3">
                    <div id="table_config_filter" class="position-relative">
                        <input type="search" id="search-box" class="form-control ps-5" aria-controls="table_config"
                            placeholder="Search Device..." />
                        <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                    </div>
                </div>
                <div class="col-md-4 col-xl-3">
                    <select id="filter_tipe" class="form-control">
                        <option value="">-- Select Device Type --</option>
                        @foreach ($tipe as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-xl-3">
                    <select id="filter_gedung" class="form-control">
                        <option value="">-- Select Building --</option>
                        @foreach ($gedungs as $gedung)
                            <option value="{{ $gedung->id }}">{{ $gedung->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 col-xl-3 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                    <a href="{{ route('admin.device.create') }}"
                        class="btn btn-info d-flex align-items-center mb-2 mb-md-0">
                        <i class="ti ti-plus text-white me-1 fs-5"></i> Add Device
                    </a>
                    <a href="{{ route('admin.device.export') }}"
                        class="btn btn-success mb-3 mb-md-0 ms-md-2 d-flex align-items-center">
                        <i class="fa fa-download text-white me-2 fs-5"></i> Export Devices to Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert"
            id="success-alert">
            <div class="d-flex gap-2 align-items-center">
                <div>
                    <span class="d-inline-flex p-1 rounded-circle border border-2 border-white mb-0">
                        <i class="fs-5 ti ti-check"></i>
                    </span>
                </div>
                <div>
                    {{ $message ?? '' }}
                </div>
            </div>
        </div>
    @endif

    @if (count($devices) > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table_config" class="table align-middle text-nowrap">
                        <thead class="header-item">
                            <tr>
                                <th>No</th>
                                <th>Device ID</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Gedung</th>
                                <th>Lokasi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($devices as $device)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $device->device_id }}</td>
                                    <td>{{ $device->tipe->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $device->isActive ? 'success' : 'danger' }}">
                                            {{ $device->isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $latestLocation = $device->location->first();
                                            $gedung = $latestLocation && $latestLocation->gedung ? $latestLocation->gedung : null;
                                            $buildingName = $gedung ? $gedung->name : 'N/A';
                                            $parentBuildingName = $gedung && $gedung->parent ? $gedung->parent->name : null;
                                        @endphp
                                        <div class="text-muted">
                                            <div>{{ $buildingName }}</div>
                                            @if($parentBuildingName)
                                                <small class="d-block text-muted">{{ $parentBuildingName }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $buildingLocation = $gedung && !empty($gedung->lokasi) ? $gedung->lokasi : 'N/A';
                                        @endphp
                                        <span class="text-muted">{{ $buildingLocation }}</span>
                                    </td>
                                    <td>
                                      
                                        <a href="{{ route('admin.device.show', $device->id) }}"
                                            class="btn btn-sm btn-secondary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.device.edit', $device->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                
                                        <form action="{{ route('admin.device.delete', $device->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('device', $device->device_id) }}"
                                            class="btn btn-sm btn-secondary">
                                            <i class="ti ti-eye"></i> Lihat Device
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#moveLocationModal" data-device-id="{{ $device->id }}">
                                        <i class="ti ti-move"></i> Move Location
                                    </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="alert alert-warning mb-0" role="alert">
                    <div class="d-flex gap-2 align-items-center">
                        <span
                            class="rounded-circle px-1 py-0 border border-2 border-warning text-light bg-warning mb-0 d-block"
                            style="font-size: 16px;">
                            <i class="ti ti-alert-circle"></i>
                        </span>
                        <p class="mb-0">
                            No devices found. <a href="{{ route('admin.device.create') }}">Add a device</a> now.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

<!-- Modal for Moving Device Location -->
<div class="modal fade" id="moveLocationModal" tabindex="-1" aria-labelledby="moveLocationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveLocationModalLabel">Move Device Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.device.addLocation', ':device_id') }}" method="POST"
                id="moveLocationForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="device_id" id="device-id-input">
                    <div class="mb-3">
                        <label for="gedung_id" class="form-label">Building</label>
                        <select name="gedung_id" id="gedung_id" class="form-control" required>
                            <option value="" selected hidden>-- Select Building --</option>
                            @foreach ($gedungs as $gedung)
                                <option value="{{ $gedung->id }}"
                                    data-image="{{ asset('uploads/gedung/photo/' . $gedung->photo) }}">
                                    {{ $gedung->display_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="building-image-container" style="display:none; position:relative;">
                        <label for="location" class="form-label">Building Image</label>
                        <div id="building-image"></div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location (Click on the image to set location)</label>
                        <input type="hidden" name="location" id="location" class="form-control" required />
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
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.js"></script>
    <script>
        let marker = null; // Untuk melacak marker yang telah dibuat

        // Initialize DataTable
        const dTable = $("#table_config").DataTable({
            "dom": "lrtip"
        });

        $("#search-box").keyup(function() {
            dTable.search($(this).val()).draw();
        });

        // Handle modal opening dan set device id pada form
        const moveLocationModal = document.getElementById('moveLocationModal');
        moveLocationModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Button yang memicu modal
            const deviceId = button.getAttribute('data-device-id'); // Ambil device id

            // Set device id di input tersembunyi dan update URL action
            const form = document.getElementById('moveLocationForm');
            const action = form.getAttribute('action').replace(':device_id', deviceId);
            form.setAttribute('action', action);
            document.getElementById('device-id-input').value = deviceId;

            // Pastikan marker dihapus saat modal dibuka kembali
            marker = null;
            document.getElementById('location').value = '';
            document.getElementById('building-image-container').style.display = 'none';
            document.getElementById('building-image').innerHTML = '';
        });

        // Handle perubahan pada select gedung dan tampilkan gambar gedung
        document.getElementById('gedung_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const buildingImageUrl = selectedOption.getAttribute('data-image');
            const buildingImageContainer = document.getElementById('building-image-container');
            const buildingImage = document.getElementById('building-image');

            if (buildingImageUrl) {
                buildingImageContainer.style.display = 'block';
                buildingImage.innerHTML =
                    `<img src="${buildingImageUrl}" id="building-map" alt="Building Image" style="max-width: 100%; height: auto; cursor: crosshair;" />`;

                // Reset input lokasi
                document.getElementById('location').value = '';

                const buildingMap = document.getElementById('building-map');

                // Tambahkan event listener klik pada gambar
                buildingMap.addEventListener('click', function(e) {
                    const rect = buildingMap.getBoundingClientRect();
                    // Hitung posisi klik relatif terhadap gambar
                    const clickX = e.clientX - rect.left;
                    const clickY = e.clientY - rect.top;

                    // Hitung persentase posisi klik
                    const percentX = (clickX / buildingMap.clientWidth) * 100;
                    const percentY = (clickY / buildingMap.clientHeight) * 100;

                    // Simpan lokasi dalam format persentase (misal: "50.00,25.00")
                    document.getElementById('location').value =
                        `${percentX.toFixed(2)},${percentY.toFixed(2)}`;

                    // Hapus marker sebelumnya jika ada
                    if (marker) {
                        marker.remove();
                    }

                    // Buat marker baru dengan posisi relative menggunakan persentase
                    marker = document.createElement('div');
                    marker.style.position = 'absolute';
                    marker.style.left =
                    `calc(${percentX.toFixed(2)}% - 10px)`; // offset agar marker terpusat
                    marker.style.top = `calc(${percentY.toFixed(2)}% - 10px)`;
                    marker.style.width = '20px';
                    marker.style.height = '20px';
                    marker.style.backgroundColor = 'red';
                    marker.style.borderRadius = '50%';
                    marker.style.border = '2px solid white';
                    marker.style.pointerEvents = 'none'; // biarkan klik diteruskan ke gambar

                    // Tempel marker ke dalam container gambar (pastikan container memiliki posisi relative)
                    buildingMap.parentElement.appendChild(marker);
                });
            }
        });
    </script>
@endpush
