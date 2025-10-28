@extends('layouts.Vet.app')

@section('content')
    <div class="card">
        <div class="card shadow-sm border-0">
            {{-- Card Header --}}
            <div class="card-header bg-success text-white py-3">
                <div class="d-flex justify-content-between align-items-center">

                    <a href="{{ route('dashboard') }}"
                        class="btn btn-outline-dark shadow btn-sm me-3 px-4 py-2 rounded d-flex text-white">
                        <i class="bi bi-arrow-left me-2"></i> <span>Back</span>
                    </a>

                    <h4 class="mt-4 fw-semibold">
                        <i class="bi bi-journal-medical me-2"></i>Disease Reports
                    </h4>
                </div>

                {{-- Filters --}}
                <div class=" mt-3 bg-white bg-opacity-10 rounded p-3">
                    <form action="{{ route('vet.reports.index') }}" method="GET">
                        <div class="row g-2">
                            {{-- Search --}}
                            <div class="col-12 col-md-4 col-lg-3">
                                <input type="text" name="report_id" class="form-control form-control-sm"
                                    placeholder="Search Report ID..." value="{{ request('report_id') }}">
                            </div>

                            {{-- Health Status Filter --}}
                            <div class="col-6 col-md-4 col-lg-3">
                                <select name="pig_health_status" class="form-select form-select-sm">
                                    <option value="">All Health Status</option>
                                    <option value="unassessed"
                                        {{ request('pig_health_status') == 'unassessed' ? 'selected' : '' }}>
                                        Unassessed
                                    </option>
                                    <option value="infected"
                                        {{ request('pig_health_status') == 'infected' ? 'selected' : '' }}>Infected
                                    </option>
                                    <option value="isolate"
                                        {{ request('pig_health_status') == 'isolate' ? 'selected' : '' }}>Isolated
                                    </option>
                                    <option value="dead" {{ request('pig_health_status') == 'dead' ? 'selected' : '' }}>
                                        Dead</option>
                                </select>
                            </div>

                            {{-- Risk Level Filter --}}
                            <div class="col-6 col-md-4 col-lg-3">
                                <select name="risk_level" class="form-select form-select-sm">
                                    <option value="">All Risk Levels</option>
                                    <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>
                                        Low
                                    </option>
                                    <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium
                                    </option>
                                    <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>
                                        High
                                    </option>
                                </select>
                            </div>

                            {{-- Apply Button --}}
                            <div class="col-12 col-lg-3">
                                <button type="submit" class="btn btn-light btn-sm w-100">
                                    <i class="bi bi-funnel me-1"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="card-body p-4">
                @if ($reports->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="text-muted mt-3">No reports found matching your criteria.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="fw-semibold" style="width: 120px;">Report ID</th>
                                    <th class="fw-semibold">Symptoms</th>
                                    <th class="fw-semibold" style="width: 140px;">Status</th>
                                    <th class="fw-semibold text-center" style="width: 80px;">Risk</th>
                                    <th class="fw-semibold" style="width: 120px;">Submitted By</th>
                                    <th class="fw-semibold" style="width: 110px;">Date</th>
                                    <th class="fw-semibold text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $report)
                                    <tr
                                        class="
    {{ !$report->is_read_by_staff ? 'table-warning' : '' }}
    @if ($report->risk_level == 'low') table-success
    @elseif($report->risk_level == 'medium') table-warning
    @elseif($report->risk_level == 'high') table-danger @endif
">
                                        {{-- Report ID --}}
                                        <td>
                                            <span
                                                class="badge bg-dark text-white font-monospace">{{ $report->report_id }}</span>
                                        </td>

                                        {{-- Symptoms --}}
                                        <td>
                                            @if ($report->symptoms && $report->symptoms->count())
                                                <div class="d-flex flex-wrap gap-1">
                                                    @php
                                                        $maxDisplay = 3;
                                                        $totalSymptoms = $report->symptoms->count();
                                                    @endphp
                                                    @foreach ($report->symptoms->take($maxDisplay) as $symptom)
                                                        <span class="badge bg-light text-dark border"
                                                            data-bs-toggle="tooltip" title="{{ $symptom->description }}">
                                                            {{ $symptom->name }}
                                                        </span>
                                                    @endforeach
                                                    @if ($totalSymptoms > $maxDisplay)
                                                        <span class="badge bg-secondary">
                                                            +{{ $totalSymptoms - $maxDisplay }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted small">No symptoms</span>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @php
                                                    $reportStatusConfig = match ($report->report_status) {
                                                        'submitted' => [
                                                            'color' => 'warning',
                                                            'icon' => 'clock-history',
                                                        ],
                                                        'under_inspection' => [
                                                            'color' => 'info',
                                                            'icon' => 'search',
                                                        ],
                                                        default => [
                                                            'color' => 'success',
                                                            'icon' => 'check-circle',
                                                        ],
                                                    };
                                                @endphp
                                                <small class="text-muted">Report:</small>
                                                <span class="badge bg-{{ $reportStatusConfig['color'] }} bg-opacity-75">
                                                    <i class="bi bi-{{ $reportStatusConfig['icon'] }} me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $report->report_status)) }}
                                                </span>

                                                @php
                                                    $healthStatusConfig = match ($report->pig_health_status) {
                                                        'infected' => ['color' => 'danger', 'icon' => 'virus'],
                                                        'isolate' => [
                                                            'color' => 'info',
                                                            'icon' => 'shield-fill-check',
                                                        ],
                                                        'dead' => [
                                                            'color' => 'dark',
                                                            'icon' => 'x-circle-fill',
                                                        ],
                                                        default => [
                                                            'color' => 'secondary',
                                                            'icon' => 'question-circle',
                                                        ],
                                                    };
                                                @endphp
                                                <small class="text-muted mt-1">Health:</small>
                                                <span class="badge bg-{{ $healthStatusConfig['color'] }} bg-opacity-75">
                                                    <i class="bi bi-{{ $healthStatusConfig['icon'] }} me-1"></i>
                                                    {{ ucfirst($report->pig_health_status) }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Risk Level --}}
                                        <td class="text-center">
                                            @php
                                                $riskConfig = match ($report->risk_level) {
                                                    'low' => ['color' => 'success', 'icon' => '●'],
                                                    'medium' => ['color' => 'warning', 'icon' => '●●'],
                                                    'high' => ['color' => 'danger', 'icon' => '●●●'],
                                                    default => ['color' => 'secondary', 'icon' => '—'],
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $riskConfig['color'] }} px-3 py-2">
                                                <span class="d-block small">{{ $riskConfig['icon'] }}</span>
                                                {{ ucfirst($report->risk_level ?? 'N/A') }}
                                            </span>
                                        </td>

                                        {{-- Submitted By --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle text-muted me-2"></i>
                                                <span>{{ $report->user->name }}</span>
                                            </div>
                                        </td>

                                        {{-- Date --}}
                                        <td class="text-muted small">
                                            {{ $report->created_at->format('M d, Y') }}
                                        </td>

                                        {{-- Actions --}}
                                        <td class="text-center">
                                            <a href="{{ route('vet.reports.show', $report->id) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if (!$reports->isEmpty())
                <div class="card-footer bg-light border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of
                            {{ $reports->total() }}
                            reports
                        </small>
                        {{ $reports->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Initialize Bootstrap Tooltips --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
@endsection
