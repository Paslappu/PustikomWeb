@extends('admin.layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .building-image-wrapper {
            position: relative;
            width: 100%;
            height: auto;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            overflow: visible;
            background-color: #f8fafc;
        }

        .building-image-wrapper img {
            display: block;
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* Style untuk setiap pin device */
        .device-pin {
            position: absolute;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            transform: translate(-50%, -50%);
        }

        .device-pin.active {
            background-color: green;
        }

        .device-pin.inactive {
            background-color: red;
        }

        /* Tooltip untuk QR code */
        .qr-code-tooltip {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            padding: 15px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            top: -170px;
            left: -70px;
            z-index: 99999;
            width: 150px;
            min-height: 150px;
            overflow: visible;
        }

        .device-pin {
            z-index: 20;
        }

        .device-pin:hover .qr-code-tooltip {
            display: block;
        }

        table tr td {
            padding: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? '' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.' . $active) }}" class="text-muted">{{ $title ?? '' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? '' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="shop-detail">
        <div class="card shadow-none border">
            <div class="card-body p-4">
                <a href="{{ route('admin.' . $active) }}" class="btn btn-sm btn-dark mb-3">
                    <i class="ti ti-arrow-left"></i> Back to {{ $title ?? '' }}
                </a>

                <div class="shop-content">
                    <h4 class="fw-semibold">{{ $data->name ?? '' }}</h4>
                    <p class="mb-3">{{ $data->lokasi ?? '' }}</p>

                    @if ($data->parent)
                        <div class="mb-3">
                            <strong>Parent Building:</strong>
                            <a href="{{ route('admin.gedung.show', $data->parent->id) }}">{{ $data->parent->name }}</a>
                        </div>
                    @endif

                    @if ($data->children->count() > 0)
                        <div class="mb-3">
                            <strong>Child Buildings:</strong>
                            <ul>
                                @foreach ($data->children as $child)
                                    <li>
                                        <a href="{{ route('admin.gedung.show', $child->id) }}">{{ $child->display_label }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Gambar Gedung dengan Pin Device -->
                    <div class="building-image-wrapper">
                        @if ($data->photo)
                            <img src="{{ asset('uploads/gedung/photo/' . $data->photo) }}"
                                 alt="{{ $data->name ?? 'Building Map' }}"
                                 class="building-map">
                        @endif

                        @foreach ($devicesWithLastLocation as $location)
                            @if ($location->location && strpos($location->location, ',') !== false)
                                @php
                                    // Asumsikan lokasi disimpan sebagai "x,y" dalam persen (misalnya "50,30")
                                    [$x, $y] = explode(',', $location->location);
                                @endphp
                                <div class="device-pin {{ $location->device->isActive ? 'active' : 'inactive' }}"
                                    data-x="{{ $x }}" data-y="{{ $y }}"
                                    style="left: {{ $x }}%; top: {{ $y }}%;"
                                    title="Device: {{ $location->device->device_id }}">
                                    <i class="fa-solid {{ $location->device->tipe->icon }}"></i>
                                    <!-- Tooltip dengan QR code dan device code -->
                                    <div class="qr-code-tooltip">
                                        {!! $location->device->qr !!}
                                        <div class="device-code-tooltip">
                                            <small>{{ $location->device->device_id }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Tabel Informasi Lokasi Device -->
                    <div class="border-top border-bottom py-3">
                        <table>
                            <tr>
                                <td><strong>Location</strong></td>
                                <td>:</td>
                                <td>{{ $data->lokasi ?? 'N/A' }}</td>
                            </tr>
                        </table>
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Device</th>
                                        <th>Code Device</th>
                                        <th>Category Dana</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($data->location) && is_iterable($data->location))
                                        @foreach ($data->location as $item)
                                            <tr>
                                                <td>{{ $item->device->tipe->name ?? 'N/A' }}</td>
                                                <td>{{ $item->device->device_id ?? 'N/A' }}</td>
                                                <td>{{ $item->device->categoryDana->name ?? 'N/A' }}</td>
                                                <td>{{ $item->created_at ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">Data tidak tersedia</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="mt-3">
                        <a href="{{ route('admin.' . $active . '.edit', $data->id) }}" class="btn btn-warning">
                            <i class="ti ti-pencil"></i> Edit Building
                        </a>
                        <form action="{{ route('admin.' . $active . '.delete', $data->id) }}" method="post"
                            class="d-inline">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="ti ti-trash"></i> Delete Building
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Jika lokasi device disimpan sebagai persentase, inline style sudah menentukan posisinya.
        // Namun, contoh berikut memastikan posisi pin tetap benar saat window di-resize.
        $(function() {
            const updateMarkerPosition = () => {
                $('.device-pin').each(function() {
                    const x = $(this).data('x');
                    const y = $(this).data('y');

                    $(this).css({
                        left: `${x}%`,
                        top: `${y}%`
                    });
                });
            };

            updateMarkerPosition();
            $(window).on('resize', updateMarkerPosition);
        });
    </script>
@endpush
