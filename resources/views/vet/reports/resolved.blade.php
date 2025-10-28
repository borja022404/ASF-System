@extends('layouts.Vet.app')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white text-center py-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}"
                    class="btn btn-outline-dark shadow btn-sm me-3 px-4 py-2 rounded d-flex text-white">
                    <i class="bi bi-arrow-left me-2"></i> <span>Back</span>
                </a>
                <div class="flex-grow-1 text-center">
                    <h3 class="mb-1">
                        <i class="bi bi-check-circle-fill me-2"></i> Resolved Reports
                    </h3>
                    <p class="mb-0 fs-6 opacity-75">All reports that have been fully resolved or closed.</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($resolved->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    <h4 class="alert-heading">No Resolved Reports</h4>
                    <p>There are no reports currently marked as "resolved."</p>
                    <hr>
                    <p class="mb-0">All pending reports are still waiting for your attention.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">Report ID</th>
                                <th scope="col">Reporter</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Health</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resolved as $report)
                                <tr>
                                    <td class="fw-bold text-success">{{ $report->report_id }}</td>
                                    <td>
                                        <i class="bi bi-person-fill me-1"></i>{{ $report->user->name }}
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-check me-1"></i>{{ $report->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i
                                                class="bi bi-check-circle-fill me-1"></i>{{ ucfirst($report->report_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i
                                                class="bi bi-piggy-bank-fill me-1"></i>{{ ucfirst($report->pig_health_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vet.reports.show', $report->id) }}"
                                            class="btn btn-success btn-sm">
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
        }

        .rounded-top-4 {
            border-top-left-radius: 1.5rem !important;
            border-top-right-radius: 1.5rem !important;
        }
    </style>
@endpush
