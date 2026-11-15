@extends('admin.layouts.app')

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? 'Gedung' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.' . $active) }}" class="text-muted">{{ $title ?? 'Gedung' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? 'Add Building' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.gedung.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h5 class="mb-3">{{ $subtitle }} Form</h5>
                <div class="row">
                    <div class="col-12">
                        <!-- Building Name -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Building Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Building Name"
                                value="{{ old('name') }}" />
                            @error('name')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Location <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" 
                                class="form-control @error('lokasi') is-invalid @enderror" placeholder="Location"
                                value="{{ old('lokasi') }}" />
                            @error('lokasi')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Parent Building (optional) -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Parent Building</label>
                            <select name="parent_id" class="form-control form-select @error('parent_id') is-invalid @enderror">
                                <option value="" selected hidden>-- Select Parent Building (if any) --</option>
                                @foreach ($gedungs as $gedung)
                                    <option value="{{ $gedung->id }}">{{ $gedung->name }}{{ !empty($gedung->lokasi) ? ' - ' . $gedung->lokasi : '' }}</option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Photo Upload -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Building Photo</label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" />
                            @error('photo')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="card-body border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-device-floppy me-1 fs-4"></i>
                            Save
                        </div>
                    </button>
                    <a href="{{ route('admin.gedung') }}" class="btn btn-danger rounded-pill px-4 ms-2 text-white">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Optionally, handle file input, price formatting, or other JS features here
    </script>
@endpush
