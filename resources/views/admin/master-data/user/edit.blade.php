@extends('admin.layouts.app')

@section('content')
    {{-- Bagian Header / Breadcrumb --}}
    <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-8">{{ $title ?? 'Users' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.user') }}" class="text-muted">{{ $title ?? 'Users' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? 'Edit User' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Pesan Sukses (Jika ada) --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Utama --}}
    <div class="card">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">{{ $subtitle ?? 'Edit Form' }}</h5>
        </div>
        
        <form action="{{ route('admin.user.update', $user->id) }}" method="post">
            @csrf
            @method('put')
            
            <div class="card-body">
                <div class="row">
                    {{-- Kolom Kiri --}}
                    <div class="col-md-6">
                        {{-- Input Name --}}
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                placeholder="Full Name" value="{{ old('name', $user->name) }}" />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Input Email --}}
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="name@example.com" value="{{ old('email', $user->email) }}" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Kolom Kanan (Password) --}}
                    <div class="col-md-6">
                        {{-- Input Password --}}
                        <div class="mb-3">
                            <label class="form-label">Password <small class="text-muted">(Opsional)</small></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Biarkan kosong jika tidak ingin mengganti"/>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                placeholder="Ketik ulang password"/>
                        </div>
                    </div>

                    {{-- Hidden Role ID --}}
                    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                </div>
            </div>
            
            <div class="card-footer bg-white border-top text-end">
                <a href="{{ route('admin.user') }}" class="btn btn-danger rounded-pill px-4 me-2">
                    Cancel
                </a>
                <button type="submit" class="btn btn-success rounded-pill px-4">
                    <i class="ti ti-device-floppy me-1"></i> Update
                </button>
            </div>
        </form>
    </div>
@endsection