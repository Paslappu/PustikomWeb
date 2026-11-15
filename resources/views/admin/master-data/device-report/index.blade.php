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
                    <li class="breadcrumb-item active" aria-current="page">{{ $title ?? '' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.device-report') }}">
                <div class="row g-2">
                    <div class="col-md-4 col-xl-3">
                        <input type="search" name="q" class="form-control" placeholder="Search notes / device ID..."
                            value="{{ $filters['q'] ?? '' }}" />
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <select name="device_id" class="form-control form-select">
                            <option value="">-- Device --</option>
                            @foreach ($devices as $device)
                                <option value="{{ $device->id }}" @selected(($filters['device_id'] ?? '') == $device->id)>
                                    {{ $device->device_id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <select name="gedung_id" class="form-control form-select">
                            <option value="">-- Building --</option>
                            @foreach ($gedungs as $gedung)
                                <option value="{{ $gedung->id }}" @selected(($filters['gedung_id'] ?? '') == $gedung->id)>
                                    {{ $gedung->display_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($isAdmin)
                        <div class="col-md-4 col-xl-2">
                            <select name="user_id" class="form-control form-select">
                                <option value="">-- Reporter --</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" @selected(($filters['user_id'] ?? '') == $u->id)>
                                        {{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-4 col-xl-2">
                        <input type="date" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}"
                            title="From date" />
                    </div>
                    <div class="col-md-4 col-xl-2">
                        <input type="date" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}"
                            title="To date" />
                    </div>
                    <div class="col-12 d-flex justify-content-between mt-2">
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                            <a href="{{ route('admin.device-report') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                        <a href="{{ route('admin.device-report.create') }}" class="btn btn-info d-flex align-items-center">
                            <i class="ti ti-plus text-white me-1 fs-5"></i> Add Device Report
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Notification --}}
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show" role="alert"
            id="success-alert">
            <div class="d-flex gap-2 align-items-center">
                <div>
                    <span class="d-inline-flex p-1 rounded-circle border border-2 border-white mb-0">
                        <i class="fs-5 ti ti-check"></i>
                    </span>
                </div>
                <div>{{ $message }}</div>
            </div>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @endif

    @if ($reports->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap">
                        <thead class="header-item">
                            <tr>
                                <th>No</th>
                                <th>Photo</th>
                                <th>Device</th>
                                <th>Captured At</th>
                                <th>Building</th>
                                <th>Coordinates</th>
                                <th>Reporter</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td>{{ $reports->firstItem() + $loop->index }}</td>
                                    <td>
                                        @if ($report->images->first())
                                            <img src="{{ $report->images->first()->url }}" alt="Photo"
                                                style="width: 56px; height: 56px; object-fit: cover; border-radius: 6px;" />
                                            @if ($report->images->count() > 1)
                                                <small class="text-muted">+{{ $report->images->count() - 1 }}</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $report->device?->device_id ?? '-' }}
                                        <small class="d-block text-muted">{{ $report->device?->tipe?->name }}</small>
                                    </td>
                                    <td>{{ $report->captured_at->format('d M Y H:i') }}</td>
                                    <td>{{ $report->gedung?->name ?? '-' }}</td>
                                    <td>
                                        @if ($report->latitude !== null && $report->longitude !== null)
                                            {{ $report->latitude }}, {{ $report->longitude }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $report->user?->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.device-report.show', $report->id) }}"
                                            class="btn btn-sm btn-secondary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @can('update', $report)
                                            <a href="{{ route('admin.device-report.edit', $report->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $report)
                                            <form action="{{ route('admin.device-report.delete', $report->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $reports->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="alert alert-warning mb-0" role="alert">
                    <div class="d-flex gap-2 align-items-center">
                        <span
                            class="rounded-circle px-1 py-0 border border-2 border-warning text-light bg-warning mb-0 d-block"
                            style="font-size: 16px;">
                            <i class="ti ti-alert-circle"></i>
                        </span>
                        <p class="mb-0">
                            No device reports found. <a href="{{ route('admin.device-report.create') }}">Add a report</a> now.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
