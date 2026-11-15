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

        .my-content-img:before {
            content:"";
            position:absolute;
            width:100%;
            height:100%;
            top:0;left:0;right:0;
            background-color:rgba(0,0,0,0);
            transition: .3s ease-in-out;
        }

        .my-content-img:hover::before {
            background-color:rgba(0,0,0,0.5);
        }

        .my-content-img .my-btn-img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity:0;
            transition: .3s ease-in-out;
        }

        .my-content-img:hover .my-btn-img {   
            opacity: 1;
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

    {{-- Success Message --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert" id="success-alert">
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

    <div class="card">
        <form action="{{ route('admin.gedung.update', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card-body">
                <h5 class="mb-3">{{ $subtitle }} Form</h5>
                <div class="row">
                    <div class="col-12">
                        <!-- Building Name -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Building Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Building Name" value="{{ old('name', $data->name) }}" />
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
                                value="{{ old('lokasi', $data->lokasi) }}" />
                            @error('lokasi')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Parent Building -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Parent Building</label>
                            <select name="parent_id" class="form-control form-select @error('parent_id') is-invalid @enderror">
                                <option value="" selected hidden>-- Select Parent Building (if any) --</option>
                                @foreach ($gedungs as $gedung)
                                    <option value="{{ $gedung->id }}" 
                                        @if ($data->parent_id == $gedung->id) selected @endif>
                                        {{ $gedung->name }}{{ !empty($gedung->lokasi) ? ' - ' . $gedung->lokasi : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Current Image -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Building Image</label>
                            <div class="input-group mb-3">
                                <input type="file" name="photo"
                                    class="form-control @error('photo') is-invalid @enderror" />
                                <input type="hidden" name="current_image" value="{{ $data->photo ?? '' }}">
                                <button type="button" class="btn btn-info font-medium text-light" data-bs-toggle="modal"
                                    data-bs-target="#modal-image">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-photo me-1 fs-4"></i>
                                        Current Image
                                    </div>
                                </button>
                                <div class="modal fade" id="modal-image" tabindex="1"
                                    aria-labelledby="vertical-center-modal" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-body p-0">
                                                <div class="position-absolute top-0 end-0 me-2 mt-2">
                                                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </div>
                                                <img src="{{ asset('uploads/gedung/photo/' . $data->photo) }}"
                                                    alt="image" width="100%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
