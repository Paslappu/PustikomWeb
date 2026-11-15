@extends('admin.layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? 'Device Report' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.' . $active) }}" class="text-muted">{{ $title ?? 'Device Report' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? 'Detail' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @php
        $hasCoords = $report->latitude !== null && $report->longitude !== null;
    @endphp

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Report #{{ $report->id }}</h5>
                <div>
                    @can('update', $report)
                        <a href="{{ route('admin.device-report.edit', $report->id) }}" class="btn btn-warning btn-sm">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                    @endcan
                    @can('delete', $report)
                        <form action="{{ route('admin.device-report.delete', $report->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">
                                <i class="ti ti-trash"></i> Delete
                            </button>
                        </form>
                    @endcan
                    <a href="{{ route('admin.device-report') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 35%">Device</th>
                            <td>
                                @if ($report->device)
                                    <a href="{{ route('admin.device.show', $report->device->id) }}">{{ $report->device->device_id }}</a>
                                    <small class="d-block text-muted">{{ $report->device->tipe?->name }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Captured At</th>
                            <td>{{ $report->captured_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Building (observed)</th>
                            <td>{{ $report->gedung?->display_label ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Coordinates</th>
                            <td>
                                @if ($hasCoords)
                                    {{ $report->latitude }}, {{ $report->longitude }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Reporter</th>
                            <td>{{ $report->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td style="white-space: pre-line;">{{ $report->notes ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $report->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @if ($hasCoords)
                        <div id="show-map" style="height: 280px; border: 1px solid #dee2e6; border-radius: 6px;"></div>
                    @else
                        <div class="text-muted">No coordinates recorded.</div>
                    @endif
                </div>
            </div>

            <h6 class="mt-4">Photos</h6>
            <div class="row g-2">
                @forelse ($report->images as $image)
                    <div class="col-6 col-md-3">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                            data-image-url="{{ $image->url }}">
                            <img src="{{ $image->url }}" alt="Photo" class="img-fluid border rounded"
                                style="width: 100%; height: 160px; object-fit: cover;" />
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-muted">No photos.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Gallery modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="Photo" id="imageModalImg" class="img-fluid" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.getElementById('imageModal').addEventListener('show.bs.modal', function(event) {
            document.getElementById('imageModalImg').src = event.relatedTarget.getAttribute('data-image-url');
        });

        @if ($hasCoords)
            if (typeof L !== 'undefined') {
                const pos = [{{ (float) $report->latitude }}, {{ (float) $report->longitude }}];
                const map = L.map('show-map', {
                    scrollWheelZoom: false
                }).setView(pos, 17);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                L.marker(pos).addTo(map);
            } else {
                document.getElementById('show-map').innerHTML =
                    '<div class="p-3 text-muted">Map could not be loaded.</div>';
            }
        @endif
    </script>
@endpush
