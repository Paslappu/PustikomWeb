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
                        <a href="{{ route('admin.device') }}" class="text-muted">{{ $title ?? '' }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subtitle ?? '' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('admin.device.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <h5 class="mb-3">{{ $subtitle }} Form</h5>
                <div class="row">
                    <div class="col-12">
                        <!-- Device ID -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Device ID <span class="text-danger">*</span></label>
                            <input type="text" name="device_id"
                                class="form-control @error('device_id') is-invalid @enderror" placeholder="Device ID"
                                value="{{ old('device_id') }}" />
                            @error('device_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="control-label mb-1">Category Dana<span class="text-danger">*</span></label>
                            <select name="category_dana_id"
                                class="form-control form-select @error('category_dana_id') is-invalid @enderror">
                                <option value="" selected hidden>-- Select Category Dana --</option>
                                @foreach ($category as $dana)
                                    <option value="{{ $dana->id }}"
                                        {{ old('category_dana_id') == $dana->id ? 'selected' : '' }}>{{ $dana->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_dana_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <!-- Device Type -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Device Type <span class="text-danger">*</span></label>
                            <select name="tipe_id" class="form-control form-select @error('tipe_id') is-invalid @enderror">
                                <option value="" selected hidden>-- Select Device Type --</option>
                                @foreach ($tipers as $tipe)
                                    <option value="{{ $tipe->id }}"
                                        {{ old('tipe_id') == $tipe->id ? 'selected' : '' }}>{{ $tipe->name }}</option>
                                @endforeach
                            </select>
                            @error('tipe_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Building -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Building <span class="text-danger">*</span></label>
                            <select name="gedung_id"
                                class="form-control form-select @error('gedung_id') is-invalid @enderror"
                                id="building-select">
                                <option value="" selected hidden>-- Select Building --</option>
                                @foreach ($gedungs as $gedung)
                                    <option value="{{ $gedung->id }}"
                                        data-image="{{ asset('uploads/gedung/photo/' . $gedung->photo) }}"
                                        {{ old('gedung_id') == $gedung->id ? 'selected' : '' }}>
                                        {{ $gedung->display_label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gedung_id')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="control-label mb-1">BMN Sticker <span class="text-danger">*</span></label>
                            <input type="file" name="sticker" accept="image/*"
                                class="form-control @error('sticker') is-invalid @enderror">
                            @error('sticker')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                            <small class="text-muted">Format: PNG/JPG (max: 2MB)</small>
                        </div>


                        <!-- Building Image -->
                        <div class="mb-3" id="building-image-container" style="display:none; position:relative;">
                            <label class="control-label mb-1">Building Image</label>
                            <div id="building-image"></div>
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Location (Click on the image to set location) <span
                                    class="text-danger">*</span></label>
                            <input type="hidden" name="location" id="location"
                                class="form-control @error('location') is-invalid @enderror"
                                value="{{ old('location') }}" />
                            @error('location')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3">
                            <label class="control-label mb-1">Status <span class="text-danger">*</span></label>
                            <select name="isActive"
                                class="form-control form-select @error('isActive') is-invalid @enderror">
                                <option value="" selected hidden>-- Select Status --</option>
                                <option value="1" {{ old('isActive') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('isActive') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('isActive')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <div class="card-body border-top">
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-device-floppy me-1 fs-4"></i> Save
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

@push('scripts')
    <script>
        // Variabel global untuk marker
        let marker = null;

        document.getElementById('building-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const buildingImageUrl = selectedOption.getAttribute('data-image');
            const buildingImageContainer = document.getElementById('building-image-container');
            const buildingImageDiv = document.getElementById('building-image');

            if (buildingImageUrl) {
                buildingImageContainer.style.display = 'block';
                buildingImageDiv.innerHTML =
                    `<img src="${buildingImageUrl}" id="building-map" alt="Building Image" style="max-width: 100%; height: auto; cursor: crosshair;" />`;

                // Reset lokasi tersembunyi
                document.getElementById('location').value = '';

                // Dapatkan element gambar dan tambahkan event listener
                const buildingMap = document.getElementById('building-map');

                buildingMap.addEventListener('click', function(e) {
                    const rect = buildingMap.getBoundingClientRect();
                    // Hitung koordinat klik relatif terhadap gambar yang ditampilkan
                    const clickX = e.clientX - rect.left;
                    const clickY = e.clientY - rect.top;
                    // Hitung persentase dari koordinat tersebut
                    const percentX = (clickX / buildingMap.clientWidth) * 100;
                    const percentY = (clickY / buildingMap.clientHeight) * 100;

                    // Simpan koordinat sebagai persentase (misalnya: "50.00,25.00")
                    document.getElementById('location').value =
                        `${percentX.toFixed(2)},${percentY.toFixed(2)}`;

                    // Hapus marker sebelumnya jika ada
                    if (marker) {
                        marker.remove();
                    }

                    // Buat marker baru dengan posisi relatif menggunakan persentase
                    marker = document.createElement('div');
                    marker.style.position = 'absolute';
                    marker.style.left =
                        `calc(${percentX.toFixed(2)}% - 10px)`; // 10px untuk menyesuaikan agar marker terpusat
                    marker.style.top = `calc(${percentY.toFixed(2)}% - 10px)`;
                    marker.style.width = '20px';
                    marker.style.height = '20px';
                    marker.style.backgroundColor = 'red';
                    marker.style.borderRadius = '50%';
                    marker.style.border = '2px solid white';
                    marker.style.pointerEvents = 'none'; // Agar marker tidak mengganggu klik selanjutnya

                    // Tambahkan marker ke dalam container gambar
                    buildingMap.parentElement.appendChild(marker);
                });
            }
        });
    </script>
@endpush
