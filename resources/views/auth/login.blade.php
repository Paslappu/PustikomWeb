@extends('auth.layouts.app')

@section('content')
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed"
        data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="{{ url('/') }}" class="text-center d-block mb-3 w-100">
                                    <img src="{{ asset('ung.png') }}" alt="Logo" class="img-fluid" style="max-height: 100px;">
                                    <h4 class="app-title">SISTEM INFORMASI PENDATAAN PERANGKAT (SIPP)</h4>
                                </a>
                                <div class="position-relative text-center my-4">
                                    <p class="mb-0 fs-4 px-3 d-inline-block bg-white text-dark z-index-5 position-relative">
                                        Sign In</p>
                                    <span
                                        class="border-top w-100 position-absolute top-50 start-50 translate-middle"></span>
                                </div>
                                <form action="{{ route('login') }}" method="post">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1" class="form-label">Email</label>
                                        <input type="email" name="email" 
                                            class="form-control 
                                            @error('email')
                                                is-invalid
                                            @enderror" 
                                            id="exampleInputEmail1"
                                            aria-describedby="emailHelp" placeholder="example@email.com">
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <!-- Show Hide Password -->
                                        <div class="mb-3" id="show_hide_password">
                                            <label for="exampleInputPassword1" class="form-label">Password</label>
                                            <div class="shbtn-group">
                                                <input type="password" name="password" 
                                                class="form-control 
                                                @error('password')
                                                    is-invalid
                                                @enderror"
                                                    id="exampleInputPassword1" 
                                                    placeholder="Enter password">
                                                <span class="shbtn">
                                                    <i class="ti ti-eye-off"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-dark w-100 py-8 rounded-2">Sign In</button>
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
    <script>
        // Ensure jQuery is loaded, then initialize password toggle
        function initPasswordToggle() {
            const passwordInput = document.getElementById('exampleInputPassword1');
            const toggleBtn = document.querySelector('.shbtn');
            
            if (!passwordInput || !toggleBtn) return;
            
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const icon = toggleBtn.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('ti-eye-off');
                    icon.classList.add('ti-eye');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('ti-eye');
                    icon.classList.add('ti-eye-off');
                }
            });
        }
        
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPasswordToggle);
        } else {
            initPasswordToggle();
        }
    </script>
@endpush