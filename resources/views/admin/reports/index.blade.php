@extends('layouts.Admin.app')

@section('content')

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold text-primary mb-0"><i class="bi bi-clipboard-data me-2"></i>Reports Overview</h1>



            {{-- Export Button --}}
            <div class="btn-group">
                <a class="btn btn-success" href="{{ route('admin.backup.list') }}">
                    <i class="bi bi-hdd"></i> Database Backups
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle shadow-sm" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('admin.reports.export', ['type' => 'pdf'] + request()->all()) }}">
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Export as PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('admin.reports.export', ['type' => 'excel'] + request()->all()) }}">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i> Export as Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center"
                            href="{{ route('admin.reports.export', ['type' => 'word'] + request()->all()) }}">
                            <i class="bi bi-file-earmark-word text-primary me-2"></i> Export as Word
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Pig Health Status</label>
                        <select name="pig_health_status" class="form-select shadow-sm">
                            <option value="">All Health Status</option>
                            <option value="unassessed" {{ request('pig_health_status') == 'unassessed' ? 'selected' : '' }}>
                                Unassessed</option>
                            <option value="infected" {{ request('pig_health_status') == 'infected' ? 'selected' : '' }}>
                                Infected</option>
                            <option value="isolate" {{ request('pig_health_status') == 'isolate' ? 'selected' : '' }}>
                                Isolate</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-secondary">Risk Level</label>
                        <select name="risk_level" class="form-select shadow-sm">
                            <option value="">All Risk Levels</option>
                            <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Risk Summary --}}
        <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="badge rounded-pill bg-success p-2 px-3 shadow-sm">Low Risk: {{ $riskCounts['low'] }}</span>
            <span class="badge rounded-pill bg-warning text-dark p-2 px-3 shadow-sm">Medium Risk:
                {{ $riskCounts['medium'] }}</span>
            <span class="badge rounded-pill bg-danger p-2 px-3 shadow-sm">High Risk: {{ $riskCounts['high'] }}</span>
        </div>

        {{-- Reports Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if ($reports->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="text-muted mt-3">No reports found for the selected filters.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Report ID</th>
                                    <th class="fw-semibold">User</th>
                                    <th class="fw-semibold">Health</th>
                                    <th class="fw-semibold text-center">Risk</th>
                                    <th class="fw-semibold">Status</th>
                                    <th class="fw-semibold">Location</th>
                                    <th class="fw-semibold text-center">Date</th>
                                    <th class="fw-semibold text-center">Specialist</th>
                                    <th class="fw-semibold text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $report)
                                    <tr
                                        class="@if ($report->risk_level == 'low') table-success 
                                           @elseif($report->risk_level == 'medium') table-warning 
                                           @elseif($report->risk_level == 'high') table-danger @endif">
                                        <td><span class="badge bg-dark text-white">{{ $report->report_id }}</span></td>
                                        <td>{{ $report->user->name ?? 'N/A' }}</td>
                                        <td class="text-capitalize">{{ $report->pig_health_status }}</td>
                                        <td class="text-center">
                                            @if ($report->risk_level == 'low')
                                                <span class="badge bg-success">Low</span>
                                            @elseif ($report->risk_level == 'medium')
                                                <span class="badge bg-warning text-dark">Medium</span>
                                            @else
                                                <span class="badge bg-danger">High</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($report->report_status == 'resolved')
                                                <span class="badge bg-success">Resolved</span>
                                            @elseif($report->report_status == 'under_inspection')
                                                <span class="badge bg-warning text-dark">For Inspection</span>
                                            @else
                                                <span class="badge bg-secondary">Unassessed</span>
                                            @endif
                                        </td>
                                        <td>{{ $report->barangay }}, {{ $report->city }}</td>
                                        <td class="text-center text-muted">
                                            {{ $report->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td>
                                            @if ($report->vetAssessments->isNotEmpty())
                                                @foreach ($report->vetAssessments as $assessment)
                                                    <span class="badge bg-success">{{ $assessment->assessor->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.reports.show', $report) }}"
                                                class="btn btn-sm btn-outline-primary mb-1">
                                                <i class="bi bi-eye me-1"></i> Full View
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary mb-1" data-bs-toggle="modal"
                                                data-bs-target="#notesModal-{{ $report->id }}">
                                                <i class="bi bi-journal-text me-1"></i> Notes
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- Notes Modal --}}
                                    <div class="modal fade" id="notesModal-{{ $report->id }}" tabindex="-1"
                                        aria-labelledby="notesLabel-{{ $report->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title fw-semibold"
                                                        id="notesLabel-{{ $report->id }}">
                                                        Case Notes — Report #{{ $report->report_id }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-light">
                                                    @forelse ($report->notes as $note)
                                                        <div class="p-3 bg-white border rounded mb-3 shadow-sm">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span
                                                                    class="badge bg-{{ $note->note_type == 'vet_diagnosis' ? 'success' : ($note->note_type == 'admin_review' ? 'warning text-dark' : 'info') }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $note->note_type)) }}
                                                                </span>
                                                                <small
                                                                    class="text-muted">{{ $note->created_at->format('M d, Y h:i A') }}</small>
                                                            </div>
                                                            <p class="mt-2 mb-1">{{ $note->content }}</p>
                                                            <small class="text-muted"><em>by
                                                                    {{ $note->user->name ?? 'Unknown' }}</em></small>
                                                        </div>
                                                    @empty
                                                        <p class="text-muted text-center">No notes added yet.</p>
                                                    @endforelse
                                                </div>
                                                <div class="modal-footer bg-white">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $reports->links() }}
        </div>

    </div>

@endsection
