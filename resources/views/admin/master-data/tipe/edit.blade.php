@extends('admin.layouts.app')

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
                        <a href="{{ route('admin.tipe') }}" class="text-muted">{{ $title ?? '' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? '' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.tipe.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card-body">
                <h5 class="mb-3">{{ $subtitle }} Form</h5>
                <div class="row">
                    <div class="col-12">
                        <div>
                            <label class="control-label mb-1">Tipe</label>
                            <input type="text" name="tipe_name"
                                class="form-control @error('tipe_name') is-invalid @enderror" placeholder="..."
                                value="{{ old('tipe_name', $data->name) }}" />
                            @error('tipe_name')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div>
                            <label class="control-label mb-1">Icon (Optional)</label>
                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror"
                                placeholder="fa-solid fa-file" value="{{ old('icon', $data->icon) }}" />
                            @error('icon')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                            <small class="text-muted">Enter the icon class (e.g., fa-solid fa-file). Refer to FontAwesome
                                icons.</small>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        <div>
                            <label class="control-label mb-1">3D Model (Optional)</label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                                accept=".stl,.obj,.fbx,.dae,.glb,.ply" />
                            @error('file')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="control-label mb-1">Ruckus? <span class="text-danger">*</span></label>
                        <select name="isRuckus" class="form-control @error('isRuckus') is-invalid @enderror">
                            <option value="1" {{ old('isRuckus', $data->isRuckus) == 1 ? 'selected' : '' }}>Yes
                            </option>
                            <option value="0" {{ old('isRuckus', $data->isRuckus) == 0 ? 'selected' : '' }}>No
                            </option>
                        </select>
                        @error('isRuckus')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>

                    @if ($data->file)
                        <div class="col-12 mt-3">
                            <div>
                                <label class="control-label mb-1">Current 3D Model</label>
                                <p class="text-muted">
                                    <a href="{{ asset('storage/' . $data->file) }}" target="_blank">View current model</a>
                                </p>
                                <small class="text-warning">Note: If you want to update the 3D model, select a new
                                    file.</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-actions">
                <div class="card-body border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-device-floppy me-1 fs-4"></i>
                            Update
                        </div>
                    </button>
                    <button type="reset" class="btn btn-danger rounded-pill px-4 ms-2 text-white">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
