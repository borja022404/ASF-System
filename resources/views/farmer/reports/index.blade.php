@extends('layouts.Farmer.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold">
                    <i class="bi bi-clipboard-data me-2"></i> My Reports
                </h4>

                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('farmer.reports.create') }}" class="btn btn-light btn-sm shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> New Report
                    </a>
                </div>

            </div>

            <div class="card-body p-4">
                @if ($reports->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-muted fs-5">You haven’t submitted any reports yet.</p>
                        <a href="{{ route('farmer.reports.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Submit Your First Report
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Date Submitted</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $report)
                                    <tr>
                                        <td class="fw-semibold">{{ $report->report_id }}</td>
                                        <td>
                                            <span
                                                class="badge rounded-pill 
                                            bg-{{ $report->report_status == 'submitted'
                                                ? 'warning text-dark'
                                                : ($report->report_status == 'under_inspection'
                                                    ? 'info'
                                                    : ($report->report_status == 'resolved'
                                                        ? 'success'
                                                        : 'secondary')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $report->report_status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('farmer.reports.show', $report->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
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
    </div>
@endsection
