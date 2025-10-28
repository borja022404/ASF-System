@extends('layouts.Vet.app')

@section('content')
    <div class="card">
        <div class="card-header bg-danger text-white text-center py-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-dark shadow btn-sm me-3 px-4 py-2 rounded d-flex text-white">
                    <i class="bi bi-arrow-left me-2"></i> <span>Back</span>
                </a>
                <div class="flex-grow-1 text-center">
                    <h3 class="mb-1">
                        <i class="bi bi-heart-fill me-2"></i> High-Risk Reports
                    </h3>
                    <p class="mb-0 fs-6 opacity-75">Reports with a high risk level that require your immediate attention.</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($highRiskReports->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    <h4 class="alert-heading">No High-Risk Reports</h4>
                    <p>There are currently no reports categorized as "High-Risk."</p>
                    <hr>
                    <p class="mb-0">Keep up the great work!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th scope="col">Report ID</th>
                                <th scope="col">Reporter</th>
                                <th scope="col">Date</th>
                                <th scope="col">Symptoms</th>
                                <th scope="col">Risk Level</th>
                                <th scope="col">Pig Health</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($highRiskReports as $report)
                                <tr>
                                    <td class="fw-bold text-danger">{{ $report->report_id }}</td>
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
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                            {{ ucfirst($report->risk_level) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-piggy-bank-fill me-1"></i>
                                            {{ ucfirst($report->pig_health_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vet.reports.show', $report->id) }}"
                                            class="btn btn-primary btn-sm">
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
@endsection


@push('styles')
    <style>
        .rounded-4 {
            border-radius: 1.5rem !important;
            min-height: 100vh;
        }

        .rounded-top-4 {
            border-top-left-radius: 1.5rem !important;
            border-top-right-radius: 1.5rem !important;
        }
    </style>
@endpush


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
