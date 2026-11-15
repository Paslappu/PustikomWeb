{{--
    Partial form Device Report (dipakai create & edit).
    Variabel: $report (null saat create), $devices, $gedungs, $mapCenter, $maxImages
--}}
@php
    $isEdit = isset($report) && $report;
    $capturedValue = old('captured_at', $isEdit ? $report->captured_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
    $latValue = old('latitude', $isEdit ? $report->latitude : null);
    $lngValue = old('longitude', $isEdit ? $report->longitude : null);
    $existingImages = $isEdit ? $report->images : collect();
@endphp

<div class="row">
    <div class="col-md-6">
        <!-- Device -->
        <div class="mb-3">
            <label class="control-label mb-1">Device <span class="text-danger">*</span></label>
            <select name="device_id" class="form-control form-select @error('device_id') is-invalid @enderror">
                <option value="" selected hidden>-- Select Device --</option>
                @foreach ($devices as $device)
                    <option value="{{ $device->id }}" @selected(old('device_id', $isEdit ? $report->device_id : null) == $device->id)>
                        {{ $device->device_id }}{{ $device->tipe ? ' - ' . $device->tipe->name : '' }}
                    </option>
                @endforeach
            </select>
            @error('device_id')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        <!-- Captured at -->
        <div class="mb-3">
            <label class="control-label mb-1">Captured At <span class="text-danger">*</span></label>
            <input type="datetime-local" name="captured_at" max="{{ now()->format('Y-m-d\TH:i') }}"
                class="form-control @error('captured_at') is-invalid @enderror" value="{{ $capturedValue }}" />
            @error('captured_at')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        <!-- Building (observed) -->
        <div class="mb-3">
            <label class="control-label mb-1">Building (observed)</label>
            <select name="gedung_id" class="form-control form-select @error('gedung_id') is-invalid @enderror">
                <option value="">-- Not specified --</option>
                @foreach ($gedungs as $gedung)
                    <option value="{{ $gedung->id }}" @selected(old('gedung_id', $isEdit ? $report->gedung_id : null) == $gedung->id)>
                        {{ $gedung->display_label }}
                    </option>
                @endforeach
            </select>
            @error('gedung_id')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        <!-- Coordinates -->
        <div class="row">
            <div class="col-6 mb-3">
                <label class="control-label mb-1">Latitude</label>
                <input type="number" step="any" min="-90" max="90" name="latitude" id="report-lat"
                    class="form-control @error('latitude') is-invalid @enderror" placeholder="-90 .. 90"
                    value="{{ $latValue }}" />
                @error('latitude')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-6 mb-3">
                <label class="control-label mb-1">Longitude</label>
                <input type="number" step="any" min="-180" max="180" name="longitude" id="report-lng"
                    class="form-control @error('longitude') is-invalid @enderror" placeholder="-180 .. 180"
                    value="{{ $lngValue }}" />
                @error('longitude')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-use-location">
                <i class="ti ti-current-location"></i> Use my location
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-clear-location">
                Clear coordinates
            </button>
            <small class="d-block text-muted mt-1" id="geo-status"></small>
        </div>

        <!-- Notes -->
        <div class="mb-3">
            <label class="control-label mb-1">Notes</label>
            <textarea name="notes" rows="4" maxlength="2000" class="form-control @error('notes') is-invalid @enderror"
                placeholder="Notes (max 2000 characters)">{{ old('notes', $isEdit ? $report->notes : '') }}</textarea>
            @error('notes')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <!-- Map -->
        <div class="mb-3">
            <label class="control-label mb-1">Map (click to set coordinates)</label>
            <div id="report-map" style="height: 280px; border: 1px solid #dee2e6; border-radius: 6px;"></div>
            <small class="text-muted">Map is optional, coordinates can be typed manually.</small>
        </div>

        <!-- Existing images (edit) -->
        @if ($existingImages->count() > 0)
            <div class="mb-3">
                <label class="control-label mb-1">Current Photos</label>
                <div class="row g-2">
                    @foreach ($existingImages as $image)
                        <div class="col-4">
                            <div class="border rounded p-1 text-center">
                                <img src="{{ $image->url }}" alt="Photo" class="img-fluid"
                                    style="max-height: 100px; object-fit: cover;" />
                                <div class="form-check mt-1">
                                    <input class="form-check-input existing-image-check" type="checkbox"
                                        name="delete_image_ids[]" value="{{ $image->id }}"
                                        id="del-img-{{ $image->id }}" @checked(collect(old('delete_image_ids', []))->contains($image->id)) />
                                    <label class="form-check-label small" for="del-img-{{ $image->id }}">Delete</label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('delete_image_ids.*')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        @endif

        <!-- New images -->
        <div class="mb-3">
            <label class="control-label mb-1">
                {{ $isEdit ? 'Add Photos' : 'Photos' }}
                @if (!$isEdit)
                    <span class="text-danger">*</span>
                @endif
                <small class="text-muted">(jpg, jpeg, png, webp - max 5 MB each, 1 to {{ $maxImages }} photos total)</small>
            </label>
            <input type="file" name="images[]" id="report-images" multiple
                accept="image/jpeg,image/png,image/webp"
                class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" />
            @error('images')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
            @error('images.*')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
            <small class="text-danger d-block" id="images-error"></small>
            <div class="row g-2 mt-1" id="images-preview"></div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function() {
            const latEl = document.getElementById('report-lat');
            const lngEl = document.getElementById('report-lng');
            const statusEl = document.getElementById('geo-status');
            const center = [{{ $mapCenter['lat'] }}, {{ $mapCenter['lng'] }}];
            let map = null;
            let marker = null;

            function hasCoords() {
                return latEl.value !== '' && lngEl.value !== '' && !isNaN(parseFloat(latEl.value)) && !isNaN(parseFloat(lngEl.value));
            }

            function syncMarker(pan) {
                if (!map) return;
                if (!hasCoords()) {
                    if (marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }
                    return;
                }
                const pos = [parseFloat(latEl.value), parseFloat(lngEl.value)];
                if (marker) {
                    marker.setLatLng(pos);
                } else {
                    marker = L.marker(pos).addTo(map);
                }
                if (pan) map.setView(pos, Math.max(map.getZoom(), 16));
            }

            function setCoords(lat, lng, pan) {
                latEl.value = Number(lat).toFixed(7);
                lngEl.value = Number(lng).toFixed(7);
                syncMarker(pan);
            }

            // Peta (opsional: form tetap bisa dipakai bila Leaflet gagal dimuat)
            if (typeof L !== 'undefined') {
                map = L.map('report-map').setView(hasCoords() ? [parseFloat(latEl.value), parseFloat(lngEl.value)] : center, hasCoords() ? 16 : 12);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                map.on('click', function(e) {
                    setCoords(e.latlng.lat, e.latlng.lng, false);
                });
                syncMarker(false);
            } else {
                document.getElementById('report-map').innerHTML =
                    '<div class="p-3 text-muted">Map could not be loaded. Please type the coordinates manually.</div>';
            }

            latEl.addEventListener('change', function() { syncMarker(true); });
            lngEl.addEventListener('change', function() { syncMarker(true); });

            document.getElementById('btn-clear-location').addEventListener('click', function() {
                latEl.value = '';
                lngEl.value = '';
                statusEl.textContent = '';
                syncMarker(false);
            });

            // Geolocation hanya tersedia pada secure context (HTTPS / localhost)
            document.getElementById('btn-use-location').addEventListener('click', function() {
                if (!navigator.geolocation || !window.isSecureContext) {
                    statusEl.textContent = 'Geolocation is not available (requires HTTPS). Please type the coordinates.';
                    return;
                }
                statusEl.textContent = 'Getting location...';
                navigator.geolocation.getCurrentPosition(function(pos) {
                    setCoords(pos.coords.latitude, pos.coords.longitude, true);
                    statusEl.textContent = 'Location set (accuracy ~' + Math.round(pos.coords.accuracy) + ' m).';
                }, function(err) {
                    statusEl.textContent = 'Could not get location: ' + err.message;
                }, {
                    enableHighAccuracy: true,
                    timeout: 15000
                });
            });

            // Validasi & preview foto di sisi klien (server tetap memvalidasi ulang)
            const maxImages = {{ $maxImages }};
            const maxBytes = 5 * 1024 * 1024;
            const allowed = ['image/jpeg', 'image/png', 'image/webp'];
            const input = document.getElementById('report-images');
            const errorEl = document.getElementById('images-error');
            const previewEl = document.getElementById('images-preview');

            function existingKept() {
                const all = document.querySelectorAll('.existing-image-check');
                let kept = all.length;
                all.forEach(function(c) {
                    if (c.checked) kept--;
                });
                return kept;
            }

            function checkImages() {
                errorEl.textContent = '';
                previewEl.innerHTML = '';
                const files = Array.from(input.files);
                let problem = '';

                files.forEach(function(f) {
                    if (!allowed.includes(f.type)) problem = 'Only jpg, jpeg, png, or webp images are allowed.';
                    else if (f.size > maxBytes) problem = 'Each photo must be at most 5 MB.';
                });
                if (!problem && existingKept() + files.length > maxImages) {
                    problem = 'A report can have at most ' + maxImages + ' photos.';
                }
                if (problem) {
                    errorEl.textContent = problem;
                    input.value = '';
                    return;
                }
                files.forEach(function(f) {
                    const col = document.createElement('div');
                    col.className = 'col-4';
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(f);
                    img.className = 'img-fluid border rounded';
                    img.style.maxHeight = '100px';
                    img.style.objectFit = 'cover';
                    col.appendChild(img);
                    previewEl.appendChild(col);
                });
            }

            input.addEventListener('change', checkImages);
            document.querySelectorAll('.existing-image-check').forEach(function(c) {
                c.addEventListener('change', checkImages);
            });
        })();
    </script>
@endpush
