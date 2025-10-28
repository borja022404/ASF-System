@extends('layouts.Vet.app')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark text-center py-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}"
                    class="btn btn-outline-dark shadow btn-sm me-3 px-4 py-2 rounded d-flex text-white">
                    <i class="bi bi-arrow-left me-2"></i> <span>Back</span>
                </a>
                <div class="flex-grow-1 text-center">
                    <h3 class="mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Unassessed Reports
                    </h3>
                    <p class="mb-0 fs-6 opacity-75">All new reports that are waiting for your inspection.</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($reports->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    <h4 class="alert-heading">No Unassessed Reports</h4>
                    <p>There are no new reports waiting for your inspection.</p>
                    <hr>
                    <p class="mb-0">Keep up the great work!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-warning">
                            <tr>
                                <th scope="col">Report ID</th>
                                <th scope="col">Reporter</th>
                                <th scope="col">Date</th>
                                <th scope="col">Symptoms</th>
                                <th scope="col">Health</th>
                                <th scope="col">Risk Level</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $report)
                                <tr class="{{ $report->is_read_by_staff ? '' : 'table-warning' }}">
                                    <td class="fw-bold text-warning">{{ $report->report_id }}</td>
                                    <td>
                                        <i class="bi bi-person-fill me-1"></i>{{ $report->user->name }}
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-check me-1"></i>{{ $report->created_at->format('M d, Y') }}
                                    </td>

                                    <td>
                                        @if ($report->symptoms && $report->symptoms->count())
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($report->symptoms->take(3) as $symptom)
                                                    <span class="badge bg-primary text-truncate" style="max-width: 100px;"
                                                        data-bs-toggle="tooltip" title="{{ $symptom->description }}">
                                                        {{ $symptom->name }}
                                                    </span>
                                                @endforeach
                                                @if ($report->symptoms->count() > 3)
                                                    <span class="badge bg-secondary text-truncate" style="max-width: 100px;"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ $report->symptoms->slice(3)->pluck('name')->join(', ') }}">
                                                        +{{ $report->symptoms->count() - 3 }} more
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">No specific symptoms</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            <i
                                                class="bi bi-piggy-bank-fill me-1"></i>{{ ucwords($report->pig_health_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $riskColor = 'success';
                                            if ($report->risk_level == 'medium') {
                                                $riskColor = 'warning';
                                            } elseif ($report->risk_level == 'high') {
                                                $riskColor = 'danger';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $riskColor }}">
                                            <i
                                                class="bi bi-exclamation-octagon-fill me-1"></i>{{ ucfirst($report->risk_level) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColor = 'info';
                                            if ($report->report_status == 'submitted') {
                                                $statusColor = 'warning';
                                            } elseif (in_array($report->report_status, ['resolved', 'closed'])) {
                                                $statusColor = 'success';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">
                                            <i
                                                class="bi bi-info-circle-fill me-1"></i>{{ ucfirst($report->report_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vet.reports.show', $report->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-eye-fill me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
@endsection
