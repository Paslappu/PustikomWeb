@extends('admin.layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Style for the map image */
        .building-image-wrapper {
            position: relative;
            width: 100%;
            height: 500px;
            /* Ensure this is flexible based on content */
            background: url('{{ asset('uploads/gedung/photo/' . $data->photo) }}') no-repeat center center;
            background-size: contain;
            /* Ensures image fits entirely within the container */
            background-position: center center;
            background-repeat: no-repeat;
            /* Adding more flexibility if needed, make sure to adjust height as required */
        }

        /* Style for each pin (active/inactive) */
        .device-pin {
            position: absolute;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        /* Green for active devices, Red for inactive */
        .device-pin.active {
            background-color: green;
        }

        .device-pin.inactive {
            background-color: red;
        }

        /* Updated style for the QR code tooltip */
        .qr-code-tooltip {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            padding: 15px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            top: -150px;
            left: -70px;
            z-index: 1000;
            width: 150px;
            height: 150px;
        }

        .device-pin:hover .qr-code-tooltip {
            display: block;
        }

        /* Optional: Style for device details */
        table tr td {
            padding: 5px;
        }
    </style>
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
                <a href="{{ route('admin.' . $active) }}" class="btn btn-sm btn-dark mb-3"><i class="ti ti-arrow-left"></i>
                    Back to {{ $title ?? '' }}</a>

                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="shop-content">
                            <h4 class="fw-semibold">{{ $data->name ?? '' }}</h4>
                            <p class="mb-3">{{ $data->lokasi ?? '' }}</p>

                            <!-- Display Parent Building if exists -->
                            @if ($data->parent)
                                <div class="mb-3">
                                    <strong>Parent Building:</strong>
                                    <a
                                        href="{{ route('admin.gedung.show', $data->parent->id) }}">{{ $data->parent->name }}</a>
                                </div>
                            @endif

                            <!-- Display Child Buildings if exists -->
                            @if ($data->children->count() > 0)
                                <div class="mb-3">
                                    <strong>Child Buildings:</strong>
                                    <ul>
                                        @foreach ($data->children as $child)
                                            <li><a
                                                    href="{{ route('admin.gedung.show', $child->id) }}">{{ $child->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Additional building information (you can add more if necessary) -->
                            <div class="border-top border-bottom py-3">
                                <table>
                                    <tr>
                                        <td><strong>Location</strong></td>
                                        <td>:</td>
                                        <td>{{ $data->lokasi ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>

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

                    <div class="col-lg-7">
                        <div class="building-image-wrapper">
                            @foreach ($data->devices as $device)
                                @if ($device->location && strpos($device->location, ',') !== false)
                                    @php
                                        [$x, $y] = explode(',', $device->location);
                                    @endphp
                                    <div class="device-pin {{ $device->isActive ? 'active' : 'inactive' }}"
                                        data-x="{{ $x }}" data-y="{{ $y }}"
                                        style="top: {{ $x }}%; left: {{ $y }}%;"
                                        title="Device: {{ $device->device_id }}">
                                        <div class="qr-code-tooltip">
                                            <!-- Generate a larger QR code for better clarity -->
                                            {!! $device->qr !!}
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
        integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(function() {
            const updateMarkerPosition = () => {
                // Get the building image dimensions
                const buildingImage = $('.building-image-wrapper')[0];
                const imageRect = buildingImage.getBoundingClientRect();

                // Loop over each device pin
                $('.device-pin').each(function() {
                    // Get the coordinates from data attributes
                    const x = $(this).data('x');
                    const y = $(this).data('y');

                    // Only adjust if x and y are valid
                    if (x !== undefined && y !== undefined) {
                        // Calculate position as a percentage of the image size
                        const xPercent = (x / imageRect.width) * 100;
                        const yPercent = (y / imageRect.height) * 100;

                        // Set the position of the pin relative to the image size
                        $(this).css({
                            top: `${yPercent}%`,
                            left: `${xPercent}%`
                        });
                    }
                });
            };

            // Run once when the page loads
            updateMarkerPosition();

            // Recalculate positions when window is resized
            $(window).on('resize', function() {
                updateMarkerPosition();
            });
        });
    </script>
@endpush
