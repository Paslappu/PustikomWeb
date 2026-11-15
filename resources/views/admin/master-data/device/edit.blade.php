@extends('admin.layouts.app')

@push('styles')
    <style>
        .btn-modal-close {
            width: 30px;
            height: 30px;
            font-size: 12px;
            color: #fff;
            background-color: rgba(255, 255, 255, .3);
            border: none;
            border-radius: 50%;
            outline: none;
            cursor: pointer;
        }

        .forPraImg {
            overflow-x: auto !important; 
            overflow-y: hidden; 
            width: 100%;
        }

        .forPraImg::-webkit-scrollbar {
            height: 6px;
        }

        .forPraImg::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        
        .forPraImg::-webkit-scrollbar-thumb {
            background: #888; 
        }

        .forPraImg::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }

        .my-content-img {
            position: relative;
        }

        .my-content-img img {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? 'Update Device' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.device') }}" class="text-muted">Devices</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Device</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert">
            <div class="d-flex gap-2 align-items-center">
                <span class="d-inline-flex p-1 rounded-circle border border-2 border-white">
                    <i class="fs-5 ti ti-check"></i>
                </span>
                <div>{{ $message }}</div>
            </div>
        </div>
    @endif

    <div class="card">
        <form action="{{ route('admin.device.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <h5 class="mb-3">Device Information</h5>
                <div class="row">
                    <div class="col-12">
                        {{-- Device ID --}}
                        <div class="mb-3">
                            <label class="control-label mb-1">Device ID <span class="text-danger">*</span></label>
                            <input type="text" name="device_id" class="form-control @error('device_id') is-invalid @enderror"
                                value="{{ old('device_id', $data->device_id) }}" required />
                            @error('device_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Device Type --}}
                        <div class="mb-3">
                            <label class="control-label mb-1">Device Type <span class="text-danger">*</span></label>
                            <select name="tipe_id" class="form-control @error('tipe_id') is-invalid @enderror">
                                <option value="" hidden>-- Select Type --</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" {{ $data->tipe_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipe_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="control-label mb-1">Active Status <span class="text-danger">*</span></label>
                            <select name="isActive" class="form-control @error('isActive') is-invalid @enderror">
                                <option value="1" {{ $data->isActive ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$data->isActive ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('isActive')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>


                        {{-- Category Dana --}}
                        <div class="mb-3">
                            <label class="control-label mb-1">Category Dana <span class="text-danger">*</span></label>
                            <select name="category_dana_id" class="form-control @error('category_dana_id') is-invalid @enderror">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $data->category_dana_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_dana_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        BMN Sticker
                        <div class="mb-3">
                            <label class="control-label mb-1">BMN Sticker <span class="text-danger">*</span></label>
                            <input type="file" name="sticker" accept="image/*" class="form-control @error('sticker') is-invalid @enderror" />
                            @error('sticker')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">Format: PNG/JPG (max: 2MB)</small>

                            {{-- Preview Image --}}
                            @if ($data->sticker)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/uploads/sticker/' . $data->sticker) }}" alt="Sticker" class="img-thumbnail" width="150">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions border-top">
                <div class="card-body">
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('admin.device') }}" class="btn btn-danger">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection
