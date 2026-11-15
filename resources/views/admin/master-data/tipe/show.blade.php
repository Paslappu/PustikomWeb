@extends('admin.layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
        integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Ensure FontAwesome is loaded -->

    <style>
        .product-3d-viewer {
            width: 100%;
            height: 500px;
            border: 1px solid #ddd;
            margin-top: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $tipe->name ?? '' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.tipe') }}" class="text-muted">Tipe</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $tipe->name ?? '' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="shop-detail">
        <div class="card shadow-none border">
            <div class="card-body p-4">
                <a href="{{ route('admin.tipe') }}" class="btn btn-sm btn-dark mb-3"><i class="ti ti-arrow-left"></i> Back
                    to Tipe</a>

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="shop-content">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="fs-2">Tipe</span>
                                <span
                                    class="badge text-bg-success fs-2 fw-semibold rounded-3">{{ $tipe->name ?? '' }}</span>
                            </div>

                            <h4 class="fw-semibold">{{ $tipe->name ?? '' }}</h4>
                            <p class="mb-3">Icon</p>
                            @if ($tipe->icon)
                            {!! $tipe->icon !!} 
                                
                                
                            @endif

                            <h4 class="fw-semibold mb-3">Model 3D</h4>
                            @if ($tipe->file)
                            <model-viewer class="product-3d-viewer" alt="model" src="{{ $tipe->getFileUrlAttribute() }}" shadow-intensity="1"
                                camera-controls touch-action="pan-y"></model-viewer>
                            @else
                                <p>No 3D model available for this tipe.</p>
                            @endif

                            <div class="mt-3">
                                <a href="{{ route('admin.tipe.edit', $tipe->id) }}" class="btn btn-warning">
                                    <i class="ti ti-pencil"></i> Edit Tipe
                                </a>
                                <form action="{{ route('admin.tipe.delete', $tipe->id) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="ti ti-trash"></i> Delete Tipe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@google/model-viewer@2.10.0/dist/model-viewer.min.js"></script>
@endpush
