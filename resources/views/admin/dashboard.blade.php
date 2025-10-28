@extends('layouts.Admin.app')

@section('content')
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-dark mb-1">Dashboard</h2>
                <small class="text-muted">Welcome back, manage your ASF reports efficiently</small>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3">Welcome, <strong>AdminUser</strong></span>
                <img src="https://ui-avatars.com/api/?name=Admin+User&background=2c3e50&color=fff&size=40"
                    class="rounded-circle" alt="Profile">
            </div>
        </div>

        <!-- Overview Section -->
        <div id="overview-section" class="dashboard-section active">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('admin.reports.index') }}" class="text-decoration-none">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-primary" id="total-reports">{{ $totalReports }}</div>
                                    <div class="stat-label">Total Reports</div>
                                    <small class="text-muted">All time submissions</small>
                                </div>
                                <i class="bi bi-file-medical-fill text-primary" style="font-size: 2rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </a>

                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('admin.reports.index') }}" class="text-decoration-none">
                        <div class="stat-card risk-high">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-danger" id="pending-reports">{{ $underInspectionReports }}
                                    </div>
                                    <div class="stat-label">For Inspection</div>
                                    <small class="text-muted">Pending assessment</small>
                                </div>
                                <i class="bi bi-exclamation-triangle-fill text-danger"
                                    style="font-size: 2rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('admin.reports.index') }}" class="text-decoration-none">
                        <div class="stat-card risk-medium">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-success" id="resolved-reports">{{ $resolvedReports }}</div>
                                    <div class="stat-label">Resolved Cases</div>
                                    <small class="text-muted">Successfully handled</small>
                                </div>
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                        <div class="stat-card risk-low">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-warning" id="total-users">{{ $totalUsers }}</div>
                                    <div class="stat-label">Active Users</div>
                                    <small class="text-muted">Registered farmers</small>
                                </div>
                                <i class="bi bi-people-fill text-warning" style="font-size: 2rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>



            <!-- Recent Reports -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Reports</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Report ID</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Risk Level</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-reports-tbody">
                                        @forelse ($recentReports as $report)
                                            <tr>
                                                <td>{{ $report->report_id }}</td>
                                                <td>{{ $report->barangay }}, {{ $report->city }}</td>
                                                <td>
                                                    @if ($report->report_status == 'resolved')
                                                        <span class="badge bg-success">Resolved</span>
                                                    @elseif($report->report_status == 'under_inspection')
                                                        <span class="badge bg-warning text-dark">For Inspection</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unassessed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge 
                    @if ($report->risk_level == 'high') bg-danger
                    @elseif($report->risk_level == 'medium') bg-warning
                    @else bg-success @endif">
                                                        {{ ucfirst($report->risk_level) }}
                                                    </span>
                                                </td>
                                                <td>{{ $report->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.reports.show', $report->id) }}"
                                                        class="btn btn-sm btn-dark">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No recent reports found.
                                                </td>
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


    <!-- Case Note Modal -->
    <div class="modal fade" id="caseNoteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-chat-left-text me-2"></i>Add Case Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="caseNoteForm">
                        <div class="mb-3">
                            <label class="form-label">Note Type</label>
                            <select class="form-select" name="note_type" required>
                                <option value="general_comment">General Comment</option>
                                <option value="vet_diagnosis">Veterinary Diagnosis</option>
                                <option value="admin_review">Administrative Review</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note Content</label>
                            <textarea class="form-control" name="content" rows="4" required placeholder="Enter your note here..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveCaseNote()">
                        <i class="bi bi-save me-1"></i>Save Note
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="text-center text-white">
                <div class="spinner-border mb-3" role="status"></div>
                <p>Processing...</p>
            </div>
        </div>
    </div>
@endsection
