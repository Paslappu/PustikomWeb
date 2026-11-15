@extends('admin.layouts.app')

@section('content')
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? 'Users' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.' . $active) }}" class="text-muted">{{ $title ?? 'Users' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? 'Add User' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.user.store') }}" method="post">
            @csrf
            <div class="card-body">
                <h5 class="mb-3">{{ $subtitle }} Form</h5>
                <div class="row">
                    <div class="col-12">
                        <!-- User Name -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror" placeholder="User Name"
                                value="{{ old('name') }}" />
                            @error('name')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- User Email -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" 
                                class="form-control @error('email') is-invalid @enderror" placeholder="User Email"
                                value="{{ old('email') }}" />
                            @error('email')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" 
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password" />
                            @error('password')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" 
                                class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password" />
                            @error('password_confirmation')
                                <small class="invalid-feedback">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <!-- Role Selection -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Role <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-control form-select @error('role_id') is-invalid @enderror">
                                <option value="" selected hidden>-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
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
                    <a href="{{ route('admin.user') }}" class="btn btn-danger rounded-pill px-4 ms-2 text-white">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Optionally, handle any JS features here if needed
    </script>
@endpush
