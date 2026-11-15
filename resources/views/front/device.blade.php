<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Device Details - {{ $device->device_id ?? 'Device' }}</title>
  <!-- Include Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Include Font Awesome for better icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Styles -->
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --success-color: #4CAF50;
      --info-color: #2196F3;
      --warning-color: #ff9800;
      --danger-color: #f44336;
      --light-color: #f8f9fa;
      --dark-color: #212529;
      --text-primary: #333;
      --text-secondary: #6c757d;
      --border-color: #dee2e6;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 0;
      color: var(--text-primary);
      line-height: 1.6;
    }
    
    .page-header {
      background-color: var(--primary-color);
      color: white;
      padding: 15px 0;
      margin-bottom: 30px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .main-container {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
      padding: 30px;
      margin-bottom: 30px;
    }
    
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      margin-bottom: 25px;
      border: 1px solid var(--border-color);
      overflow: hidden;
    }
    
    .card-header {
      background-color: rgba(67, 97, 238, 0.05);
      border-bottom: 1px solid var(--border-color);
      padding: 15px 20px;
      font-weight: 600;
    }
    
    .card-body {
      padding: 20px;
    }
    
    h1, h2, h3, h4, h5, h6 {
      color: var(--primary-color);
      font-weight: 600;
    }
    
    .device-title {
      font-size: 26px;
      margin-bottom: 20px;
      border-bottom: 2px solid var(--primary-color);
      padding-bottom: 10px;
      display: inline-block;
    }
    
    .breadcrumb {
      padding: 10px 0;
      margin-bottom: 20px;
      background: none;
    }
    
    .breadcrumb-item a {
      text-decoration: none;
      color: var(--text-secondary);
      transition: color 0.3s;
    }
    
    .breadcrumb-item a:hover {
      color: var(--primary-color);
    }
    
    .breadcrumb-item.active {
      color: var(--primary-color);
      font-weight: 500;
    }
    
    .status-badge {
      padding: 5px 12px;
      border-radius: 50px;
      color: white;
      font-weight: 600;
      font-size: 14px;
      display: inline-block;
    }
    
    .status-active {
      background-color: var(--success-color);
    }
    
    .status-inactive {
      background-color: var(--text-secondary);
    }
    
    .info-card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 25px;
    }
    
    .info-card-header {
      background-color: var(--light-color);
      padding: 15px;
      border-radius: 10px 10px 0 0;
      border-bottom: 1px solid var(--border-color);
    }
    
    .info-card-body {
      padding: 15px;
    }
    
    .info-item {
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      border-bottom: 1px dashed var(--border-color);
      padding-bottom: 10px;
    }
    
    .info-item:last-child {
      border-bottom: none;
      margin-bottom: 0;
    }
    
    .info-label {
      font-weight: 500;
      color: var(--text-secondary);
      display: flex;
      align-items: center;
    }
    
    .info-label i {
      margin-right: 8px;
      color: var(--primary-color);
      width: 20px;
      text-align: center;
    }
    
    .info-value {
      font-weight: 600;
      color: var(--text-primary);
    }
    
    .qr-container {
      margin: 25px 0;
      text-align: center;
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    
    .qr-container h5 {
      margin-bottom: 15px;
      color: var(--primary-color);
    }
    
    .location-container {
      position: relative;
      width: 100%;
      height: auto;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin: 25px 0;
    }
    
    .location-container img {
      width: 100%;
      height: auto;
      object-fit: contain;
      border-radius: 10px;
    }
    
    .location-container h5 {
      position: absolute;
      top: 15px;
      left: 15px;
      background-color: rgba(255, 255, 255, 0.9);
      padding: 8px 15px;
      border-radius: 5px;
      font-size: 16px;
      font-weight: 600;
      margin: 0;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .marker {
      position: absolute;
      width: 20px;
      height: 20px;
      background-color: var(--danger-color);
      border-radius: 50%;
      border: 3px solid white;
      transform: translate(-50%, -50%);
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
      transition: all 0.3s ease;
    }
    
    .marker:hover {
      transform: translate(-50%, -50%) scale(1.2);
      cursor: pointer;
    }
    
    .table {
      width: 100%;
      margin-bottom: 20px;
      border-collapse: separate;
      border-spacing: 0;
      border-radius: 10px;
      overflow: hidden;
    }
    
    .table-bordered {
      border: 1px solid var(--border-color);
    }
    
    .table-bordered th,
    .table-bordered td {
      border: 1px solid var(--border-color);
      padding: 12px 15px;
    }
    
    .table thead th {
      background-color: rgba(67, 97, 238, 0.05);
      color: var(--primary-color);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 14px;
      letter-spacing: 0.5px;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.02);
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }
    
    .ap-info-card {
      margin-bottom: 25px;
      border: none;
    }
    
    .ap-info-card .card-header {
      background-color: rgba(67, 97, 238, 0.1);
      border-bottom: none;
      color: var(--primary-color);
    }
    
    .ap-info-card .section-title {
      font-size: 18px;
      font-weight: 600;
    }
    
    .ap-info-card .info-item {
      transition: all 0.3s ease;
      padding: 10px 5px;
    }
    
    .ap-info-card .info-item:hover {
      background-color: rgba(67, 97, 238, 0.05);
      border-radius: 5px;
    }
    
    @media (max-width: 768px) {
      .card-body {
        padding: 15px;
      }
      
      .info-item {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .info-value {
        margin-top: 5px;
        margin-left: 28px;
      }
    }
    
    .model-viewer-container {
      width: 100%;
      background-color: #f8f9fa;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 25px;
    }
    
    model-viewer {
      width: 100%;
      height: 400px;
      background-color: #f8f9fa;
      --poster-color: transparent;
    }
    
    /* Animation for loading elements */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
      animation: fadeIn 0.5s ease forwards;
    }
  </style>
</head>
<body>
  <!-- Page Header -->
  <header class="page-header">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Device Details</h1>
        {{-- <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm">
          <i class="fas fa-arrow-left me-1"></i> Back to Home
        </a> --}}
      </div>
    </div>
  </header>

  <div class="container main-container">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i> Home</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ $device->device_id }}</li>
      </ol>
    </nav>

    <h2 class="device-title">{{ $device->device_id ?? 'Device' }}</h2>

    <div class="row">
      <!-- Left Section: Device Info -->
      <div class="col-lg-8 fade-in" style="animation-delay: 0.1s;">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-info-circle me-2"></i> Device Information
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Device Type:</strong></p>
                <p class="badge bg-info text-white py-2 px-3 rounded-pill">
                  <i class="fas fa-microchip me-1"></i> {{ $device->tipe->name ?? 'N/A' }}
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Status:</strong></p>
                <span class="status-badge {{ $device->isActive ? 'status-active' : 'status-inactive' }}">
                  <i class="fas {{ $device->isActive ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                  {{ $device->isActive ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
            
            @if ($device->tipe->file)
              <div class="model-viewer-container">
                <model-viewer class="product-3d-viewer" alt="model" 
                  src="{{ $device->tipe->getFileUrlAttribute() }}"
                  shadow-intensity="1" camera-controls touch-action="pan-y" 
                  auto-rotate ar ar-modes="webxr scene-viewer quick-look">
                </model-viewer>
              </div>
            @else
              <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i> No 3D model available for this device type.
              </div>
            @endif

            <!-- Ruckus AP Info Section -->
            @if(isset($apSummary) && is_array($apSummary))
            <div class="card ap-info-card">
              <div class="card-header">
                <span class="section-title"><i class="fas fa-wifi me-2"></i> AP Ruckus Information</span>
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="info-item"><span class="info-label"><i class="fas fa-tag"></i> Name</span><span class="info-value">{{ $apSummary['name'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-barcode"></i> Serial</span><span class="info-value">{{ $apSummary['serial'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-microchip"></i> Model</span><span class="info-value">{{ $apSummary['model'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-network-wired"></i> IP</span><span class="info-value">{{ $apSummary['ip'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-code-branch"></i> Version</span><span class="info-value">{{ $apSummary['version'] ?? '-' }}</span></div>
                  </div>
                  <div class="col-md-6">
                    <div class="info-item"><span class="info-label"><i class="fas fa-wifi"></i> 2.4GHz</span><span class="info-value">{{ $apSummary['wifi24Channel'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-wifi"></i> 5GHz</span><span class="info-value">{{ $apSummary['wifi50Channel'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-link"></i> Connection</span><span class="info-value">{{ $apSummary['connectionState'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-user-check"></i> Registration</span><span class="info-value">{{ $apSummary['registrationState'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-clock"></i> Uptime</span><span class="info-value">{{ isset($apSummary['uptime']) ? gmdate('H:i:s', $apSummary['uptime']) : '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-users"></i> Client Count</span><span class="info-value">{{ $apSummary['clientCount'] ?? '-' }}</span></div>
                    <div class="info-item"><span class="info-label"><i class="fas fa-history"></i> Last Seen</span><span class="info-value">{{ isset($apSummary['lastSeenTime']) ? \Carbon\Carbon::createFromTimestampMs($apSummary['lastSeenTime'])->format('d M Y H:i') : '-' }}</span></div>
                  </div>
                </div>
              </div>
            </div>
            @endif

            <!-- QR Code Section -->
            <div class="qr-container">
              <h5><i class="fas fa-qrcode me-2"></i>QR Code</h5>
              {!! $device->qr !!}
            </div>

            <!-- Device Location -->
            @if ($gedung && $gedung->photo)
            <div class="card mb-4">
              <div class="card-header">
                <i class="fas fa-map-marker-alt me-2"></i> Location
              </div>
              <div class="card-body p-0">
                <div class="location-container" id="location-container">
                  <h5>Device Location</h5>
                  <img src="{{ asset('uploads/gedung/photo/' . $gedung->photo) }}" alt="Building Map" id="building-map">
                  @if ($x && $y)
                    <div class="marker" style="left: {{ $x }}%; top: {{ $y }}%;" title="Device Location"></div>
                  @endif
                </div>
                <p class="mt-3 mb-0 px-3 pb-3"><strong><i class="fas fa-map-pin me-2"></i>Coordinates:</strong> {{ $latestLocation ? $latestLocation->location : 'N/A' }}</p>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Right Section: Additional Info -->
      <div class="col-lg-4 fade-in" style="animation-delay: 0.2s;">
        <div class="card">
          <div class="card-header">
            <i class="fas fa-clipboard-list me-2"></i> Additional Information
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <tr>
                <td class="table-active"><strong>Device ID</strong></td>
                <td>{{ $device->device_id }}</td>
              </tr>
              <tr>
                <td class="table-active"><strong>Device Type</strong></td>
                <td>{{ $device->tipe->name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td class="table-active"><strong>Building</strong></td>
                <td>{{ $gedung->name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td class="table-active"><strong>Status</strong></td>
                <td>
                  <span class="badge {{ $device->isActive ? 'bg-success' : 'bg-secondary' }}">
                    {{ $device->isActive ? 'Active' : 'Inactive' }}
                  </span>
                </td>
              </tr>
              <tr>
                <td class="table-active"><strong>Location</strong></td>
                <td>{{ $latestLocation ? $latestLocation->location : 'N/A' }}</td>
              </tr>
            </table>

            <!-- Location History Section -->
            <div class="card mt-4">
              <div class="card-header">
                <i class="fas fa-history me-2"></i> Location History
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-striped table-hover mb-0">
                    <thead>
                      <tr>
                        <th>Building</th>
                        <th>Location</th>
                        <th>Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($locationHistory as $history)
                        <tr>
                          <td>{{ $history->gedung->name ?? 'N/A' }}</td>
                          <td>{{ $history->location ?? 'N/A' }}</td>
                          <td>{{ $history->created_at->format('d M Y H:i') }}</td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="3" class="text-center">No location history available.</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h5>SISTEM INFORMASI PENDATAAN PERANGKAT (SIPP)</h5>
          <p class="small">© {{ date('Y') }} All Rights Reserved</p>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="mb-0">Universitas Negeri Gorontalo</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
  <script>
    // Add fade-in animation to elements as they appear in viewport
    document.addEventListener('DOMContentLoaded', function() {
      // Add tooltip functionality for markers
      const marker = document.querySelector('.marker');
      if (marker) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.style.position = 'absolute';
        tooltip.style.display = 'none';
        tooltip.style.background = 'rgba(0,0,0,0.7)';
        tooltip.style.color = 'white';
        tooltip.style.padding = '5px 10px';
        tooltip.style.borderRadius = '3px';
        tooltip.style.zIndex = '1000';
        tooltip.textContent = 'Device Location';
        
        document.body.appendChild(tooltip);
        
        marker.addEventListener('mouseover', function(e) {
          tooltip.style.left = e.pageX + 'px';
          tooltip.style.top = (e.pageY - 30) + 'px';
          tooltip.style.display = 'block';
        });
        
        marker.addEventListener('mouseout', function() {
          tooltip.style.display = 'none';
        });
      }
    });
  </script>
</body>
</html>
