@extends('layouts.Admin.app')

@section('content')
    {{-- Print Header with Logo (Hidden on screen, visible on print) --}}
    <div class="print-header">
        <div class="print-logo-container">
            <img src="{{ asset('/images/vitarich-logo.png') }}" alt="Vitarich Logo" class="print-logo">
            <div class="print-header-text">
                <h1>PIG HEALTH REPORT</h1>
                <p class="print-system-name">SMART ASF DISEASE DETECTION MONITORING SYSTEM</p>
            </div>
        </div>
        <div class="print-report-id">Report #{{ $report->report_id }}</div>
        <div class="print-date">Printed: {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    <div class="main-content">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Report #{{ $report->report_id }}</h4>
                    </div>
                    <div class="d-flex align-items-center">
                        @php
                            $riskColor = match ($report->risk_level) {
                                'low' => 'success',
                                'medium' => 'warning',
                                'high' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <button onclick="window.print()" class="btn btn-success me-2">
                            <i class="fas fa-print me-1"></i> Print Report
                        </button>
                        <span class="badge bg-{{ $riskColor }} me-2 fs-6 px-3 py-2">
                            {{ ucfirst($report->risk_level ?? 'No Risk') }}
                        </span>
                        <span
                            class="badge bg-{{ match ($report->report_status) {
                                'submitted' => 'warning',
                                'under_inspection' => 'info',
                                'resolved' => 'success',
                                'closed' => 'secondary',
                                default => 'secondary',
                            } }} fs-6 px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $report->report_status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Report Overview --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Report Overview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-3">
                                        <i class="fas fa-user text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Submitted By</small>
                                        <div class="fw-semibold">{{ $report->user->name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-3">
                                        <i class="fas fa-calendar text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Date Submitted</small>
                                        <div class="fw-semibold">{{ $report->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Attached Images --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-images me-2"></i>Attached Images</h6>
                    </div>
                    <div class="card-body">
                        @if ($report->images->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">No images uploaded for this report</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach ($report->images as $index => $image)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                class="img-fluid rounded shadow-sm w-100 image-thumbnail"
                                                alt="Pig Image {{ $index + 1 }}"
                                                style="height: 200px; object-fit: cover; cursor: pointer;"
                                                onclick="openImageModal('{{ asset('storage/' . $image->image_path) }}', '{{ $index + 1 }}')">
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-dark">{{ $index + 1 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Image Modal --}}
                            <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                                            <div class="ms-auto d-flex align-items-center">
                                                <a href="#" id="downloadLink" download
                                                    class="btn btn-sm btn-outline-success me-2" title="Download Image">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn-close"
                                                    onclick="closeImageModal()"></button>
                                            </div>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img id="modalImage" src="" class="img-fluid rounded" alt="Pig Image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Health Information --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-heart-pulse-fill me-2"></i>Health Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Pig Health Status --}}
                            <div class="col-md-4">
                                @php
                                    $healthColor = match ($report->pig_health_status) {
                                        'infected' => 'success',
                                        'isolate' => 'danger',
                                        'unassessed' => 'secondary',
                                        default => 'secondary',
                                    };
                                @endphp
                                <div class="border-start border-4 border-{{ $healthColor }} ps-3">
                                    <small class="text-muted">Health Status</small>
                                    <div class="fw-semibold text-{{ $healthColor }}">
                                        {{ ucfirst($report->pig_health_status ?? 'unassessed') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Symptom Onset --}}
                            <div class="col-md-4">
                                <div class="border-start border-4 border-info ps-3">
                                    <small class="text-muted">Symptom Onset</small>
                                    <div class="fw-semibold">
                                        {{ $report->symptom_onset_date ? \Carbon\Carbon::parse($report->symptom_onset_date)->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Affected Pigs --}}
                            @if (!empty($report->affected_pig_count))
                                <div class="col-md-4">
                                    <div class="border-start border-4 border-warning ps-3">
                                        <small class="text-muted">Affected Pig(s)</small>
                                        <div class="fw-semibold">{{ $report->affected_pig_count }}</div>
                                    </div>
                                </div>
                            @endif

                            {{-- Reported Symptoms --}}
                            <div class="col-12">
                                <div class="border-start border-4 border-primary ps-3 py-2">
                                    <small class="text-muted">Reported Symptoms</small>
                                    <div class="mt-2">
                                        @if ($report->symptoms && $report->symptoms->count())
                                            <div class="row g-2">
                                                @foreach ($report->symptoms as $symptom)
                                                    <div class="col-md-6">
                                                        <div class="p-2 border rounded shadow-sm bg-light h-100">
                                                            <div class="fw-semibold">{{ $symptom->name }}</div>
                                                            <small class="text-muted">{{ $symptom->description }}</small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No specific symptoms reported</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Symptoms Description --}}
                            @if ($report->symptoms_description)
                                <div class="col-12">
                                    <div class="bg-light rounded p-3">
                                        <h6 class="fw-bold mb-2">
                                            <i class="bi bi-clipboard-pulse text-primary me-2"></i>Symptoms Description
                                        </h6>
                                        <p class="mb-0">{{ $report->symptoms_description }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Location Details --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location Details</h6>
                        @if ($report->latitude && $report->longitude)
                            <button type="button" class="btn btn-light btn-sm no-print" data-bs-toggle="modal"
                                data-bs-target="#locationModal">
                                <i class="fas fa-map me-1"></i> View Location
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Barangay</small>
                                <div class="fw-semibold">{{ $report->barangay }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">City</small>
                                <div class="fw-semibold">{{ $report->city }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Province</small>
                                <div class="fw-semibold">{{ $report->province }}</div>
                            </div>
                            @if ($report->latitude && $report->longitude)
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">GPS Coordinates</small>
                                        <div class="fw-semibold font-monospace">{{ $report->latitude }},
                                            {{ $report->longitude }}</div>
                                    </div>
                                    <div class="no-print">
                                        <button type="button" class="btn btn-outline-success btn-sm me-2"
                                            onclick="copyCoordinates('{{ $report->latitude }}, {{ $report->longitude }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                                            target="_blank" class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-google"></i>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 text-muted">
                                    <i class="fas fa-exclamation-triangle me-2"></i>No GPS coordinates available
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px;">

                    {{-- Print-Friendly Status Overview --}}
                    <div class="card shadow-sm mb-4 d-none d-print-block print-status-card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Status Overview</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold mb-2">Report Status</h6>
                            <span
                                class="badge bg-{{ match ($report->report_status) {
                                    'submitted' => 'warning',
                                    'under_inspection' => 'info',
                                    'resolved' => 'success',
                                    'closed' => 'secondary',
                                    default => 'secondary',
                                } }} fs-6 px-3 py-2">
                                {{ ucfirst(str_replace('_', ' ', $report->report_status)) }}
                            </span>

                            <h6 class="fw-bold mt-3 mb-2">Risk Level</h6>
                            <span class="badge bg-{{ $riskColor }} me-2 fs-6 px-3 py-2">
                                {{ ucfirst($report->risk_level ?? 'No Risk') }}
                            </span>

                            <h6 class="fw-bold mt-3 mb-2">Pig Health Status</h6>
                            <span class="badge bg-{{ $healthColor }} fs-6 px-3 py-2">
                                {{ ucfirst($report->pig_health_status ?? 'unassessed') }}
                            </span>
                        </div>
                    </div>

                    {{-- Status Update (Form) - HIDE THIS ON PRINT --}}
                    <div class="card shadow-sm mb-4 border-0 status-card d-print-none">
                        <div class="card-header bg-success text-white d-flex align-items-center">
                            <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Report Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label small text-secondary">Current Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="report_status"
                                            id="status_under_inspection" value="under_inspection"
                                            {{ $report->report_status == 'under_inspection' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="status_under_inspection">
                                            <i class="fas fa-search text-info me-1"></i>For Inspection
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="report_status"
                                            id="status_resolved" value="resolved"
                                            {{ $report->report_status == 'resolved' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="status_resolved">
                                            <i class="fas fa-check-circle text-success me-1"></i>Resolved
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4 border-0 status-card d-print-none">
                        <div class="card-header bg-teal text-white d-flex align-items-center"
                            style="background-color:#20c997;">
                            <h6 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Health Status</h6>
                        </div>
                        <div class="card-body">
                            {{-- Risk Level --}}
                            <div class="mb-3">
                                <label class="form-label text-secondary fw-semibold">Risk Level</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="risk_level" value="low"
                                            {{ $report->risk_level == 'low' ? 'checked' : '' }} required>
                                        <label class="form-check-label">
                                            <i class="fas fa-smile text-success me-1"></i>Low
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="risk_level" value="medium"
                                            {{ $report->risk_level == 'medium' ? 'checked' : '' }} required>
                                        <label class="form-check-label">
                                            <i class="fas fa-exclamation-circle text-warning me-1"></i>Medium
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="risk_level" value="high"
                                            {{ $report->risk_level == 'high' ? 'checked' : '' }} required>
                                        <label class="form-check-label">
                                            <i class="fas fa-exclamation-triangle text-danger me-1"></i>High
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Pig Health Status --}}
                            <div class="mb-3">
                                <label class="form-label text-secondary fw-semibold">Pig Health Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pig_health_status"
                                            value="infected"
                                            {{ $report->pig_health_status == 'infected' ? 'checked' : '' }} required>
                                        <label class="form-check-label">
                                            <i class="fas fa-virus text-danger me-1"></i>Infected
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pig_health_status"
                                            value="isolate" {{ $report->pig_health_status == 'isolate' ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label">
                                            <i class="fas fa-user-shield text-info me-1"></i>Isolate
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Add Note --}}
                    <div class="card shadow-sm mb-4 no-print">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add Note</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.notes.store', $report->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="note_type" value="admin_review">
                                <div class="mb-3">
                                    <label for="content" class="form-label small">Content</label>
                                    <textarea class="form-control" id="content" name="content" rows="4"
                                        placeholder="Enter your note or diagnosis here..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-info w-100"><i class="fas fa-plus me-2"></i>Save
                                    Note</button>
                            </form>
                        </div>
                    </div>

                    {{-- Case Notes --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Case Notes <span
                                    class="badge bg-light text-dark ms-2">{{ $report->notes->count() }}</span></h6>
                        </div>
                        <div class="card-body p-0">
                            @if ($report->notes->isEmpty())
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No notes or diagnoses yet</p>
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach ($report->notes as $note)
                                        <div class="list-group-item border-0">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span
                                                    class="badge bg-{{ $note->note_type == 'vet_diagnosis' ? 'success' : 'warning' }}">{{ ucfirst(str_replace('_', ' ', $note->note_type)) }}</span>
                                                <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-2 small">{{ $note->content }}</p>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle p-1 me-2"
                                                    style="width: 20px; height: 20px;">
                                                    <i class="fas fa-user-md text-success" style="font-size: 10px;"></i>
                                                </div>
                                                <small class="text-muted">{{ $note->user->name }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Modal --}}
            @if ($report->latitude && $report->longitude)
                <div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i>Report Location</h5>
                                <div class="ms-auto d-flex align-items-center">
                                    <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                                        target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fab fa-google me-1"></i> Google Maps
                                    </a>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                            </div>
                            <div class="modal-body p-0">
                                <div id="locationMap" style="height: 500px; width: 100%;"></div>
                            </div>
                            <div class="modal-footer bg-light">
                                <div class="row w-100 g-0">
                                    <div class="col-md-3"><strong>Barangay:</strong> {{ $report->barangay }}</div>
                                    <div class="col-md-3"><strong>City:</strong> {{ $report->city }}</div>
                                    <div class="col-md-3"><strong>Province:</strong> {{ $report->province }}</div>
                                    <div class="col-md-3">
                                        <strong>Coordinates:</strong> <span
                                            class="font-monospace">{{ $report->latitude }},
                                            {{ $report->longitude }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1"
                                            onclick="copyCoordinates('{{ $report->latitude }}, {{ $report->longitude }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Print Footer (Hidden on screen, visible on print) --}}
            <div class="print-footer">
                <div class="print-watermark">
                    ✓ This report is generated by SMART ASF DISEASE DETECTION AND MONITORING SYSTEM
                </div>
            </div>

            @push('scripts')
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Map Initialization
                        let locationMap;
                        document.getElementById('locationModal')?.addEventListener('shown.bs.modal', function() {
                            if (!locationMap) {
                                const lat = {{ $report->latitude ?? 'null' }};
                                const lng = {{ $report->longitude ?? 'null' }};
                                if (lat && lng) {
                                    locationMap = L.map('locationMap').setView([lat, lng], 15);
                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 19
                                    }).addTo(locationMap);
                                    const marker = L.marker([lat, lng]).addTo(locationMap);
                                    marker.bindPopup(
                                        `<strong>Report #{{ $report->report_id }}</strong><br><small>{{ $report->barangay }}, {{ $report->city }}</small>`
                                    ).openPopup();
                                    L.circle([lat, lng], {
                                        color: 'red',
                                        fillColor: '#f03',
                                        fillOpacity: 0.1,
                                        radius: 500
                                    }).addTo(locationMap);
                                }
                            } else setTimeout(() => locationMap.invalidateSize(), 100);
                        });

                        // Image modal
                        window.openImageModal = function(src, num) {
                            const modal = document.getElementById('imageModal');
                            document.getElementById('modalImage').src = src;
                            document.getElementById('imageModalLabel').textContent = 'Image ' + num;
                            document.getElementById('downloadLink').href = src;
                            modal.style.display = 'block';
                            modal.classList.add('show');
                            const backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade show';
                            backdrop.id = 'modal-backdrop';
                            document.body.appendChild(backdrop);
                            document.body.style.overflow = 'hidden';
                            document.body.style.paddingRight = '17px';
                            backdrop.onclick = closeImageModal;
                            document.addEventListener('keydown', handleEscapeKey);
                        };
                        window.closeImageModal = function() {
                            const modal = document.getElementById('imageModal');
                            modal.style.display = 'none';
                            modal.classList.remove('show');
                            const backdrop = document.getElementById('modal-backdrop');
                            backdrop?.remove();
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                            document.removeEventListener('keydown', handleEscapeKey);
                        };

                        function handleEscapeKey(e) {
                            if (e.key === 'Escape') {
                                closeImageModal();
                            }
                        }

                        // Copy coordinates
                        window.copyCoordinates = function(coords) {
                            if (navigator.clipboard) {
                                navigator.clipboard.writeText(coords).then(() => showAlert('Coordinates copied!',
                                    'success')).catch(() => fallbackCopy(coords));
                            } else fallbackCopy(coords);
                        };

                        function fallbackCopy(text) {
                            const ta = document.createElement('textarea');
                            ta.value = text;
                            ta.style.position = 'fixed';
                            ta.style.opacity = '0';
                            document.body.appendChild(ta);
                            ta.focus();
                            ta.select();
                            try {
                                document.execCommand('copy');
                                showAlert('Coordinates copied!', 'success');
                            } catch {
                                showAlert('Failed to copy', 'error');
                            }
                            ta.remove();
                        }

                        function showAlert(msg, type = 'info') {
                            const a = document.createElement('div');
                            a.className =
                                `alert alert-${type==='error'?'danger':type} alert-dismissible fade show position-fixed`;
                            a.style.cssText = 'top:20px; right:20px; z-index:9999; min-width:300px;';
                            a.innerHTML =
                                `${msg}<button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>`;
                            document.body.appendChild(a);
                            setTimeout(() => a.remove(), 5000);
                        }
                    });
                </script>
                <style>
                    /* Screen Styles */
                    .print-header,
                    .print-footer {
                        display: none;
                    }

                    .border-start {
                        border-left-width: 4px !important;
                    }

                    .list-group-item:hover {
                        background-color: #f8f9fa;
                        transition: 0.2s;
                    }

                    .card {
                        border: none;
                        border-radius: 10px;
                    }

                    .card-header {
                        border-radius: 10px 10px 0 0 !important;
                        font-weight: 500;
                    }

                    .badge {
                        font-weight: 500;
                    }

                    .image-thumbnail {
                        transition: transform 0.2s;
                    }

                    .status-card {
                        pointer-events: none;
                        user-select: none;
                    }

                    /* Print Styles */
                    @media print {

                        /* Show print elements */
                        .print-header,
                        .print-footer {
                            display: block !important;
                        }

                        /* Print Header Styles */
                        .print-header {
                            position: fixed;
                            top: 0;
                            left: 0;
                            right: 0;
                            background: white;
                            padding: 20px;
                            border-bottom: 3px solid #198754;
                            margin-bottom: 30px;
                            z-index: 1000;
                        }

                        .print-logo-container {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 15px;
                        }

                        .print-logo {
                            height: 60px;
                            width: auto;
                            margin-right: 20px;
                        }

                        .print-header-text {
                            text-align: center;
                        }

                        .print-header-text h1 {
                            margin: 0;
                            font-size: 24px;
                            font-weight: bold;
                            color: #198754;
                        }

                        .print-system-name {
                            margin: 5px 0 0 0;
                            font-size: 12px;
                            color: #666;
                            font-weight: 600;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }

                        .print-report-id {
                            text-align: center;
                            font-size: 18px;
                            font-weight: bold;
                            color: #333;
                            margin-top: 10px;
                        }

                        .print-date {
                            text-align: center;
                            font-size: 11px;
                            color: #666;
                            margin-top: 5px;
                        }

                        /* Print Footer Styles */
                        .print-footer {
                            position: fixed;
                            bottom: 0;
                            left: 0;
                            right: 0;
                            background: white;
                            padding: 15px 20px;
                            border-top: 2px solid #e9ecef;
                        }

                        .print-watermark {
                            text-align: center;
                            font-size: 10px;
                            color: #666;
                            font-style: italic;
                        }

                        .print-watermark::before {
                            content: "";
                            display: inline-block;
                            width: 14px;
                            height: 14px;
                            background-color: #198754;
                            border-radius: 50%;
                            margin-right: 8px;
                            vertical-align: middle;
                            position: relative;
                        }

                        /* Hide elements that shouldn't be printed */
                        .no-print,
                        .btn,
                        button,
                        .modal,
                        .modal-backdrop,
                        .sticky-top form,
                        nav,
                        .sidebar,
                        header,
                        footer,
                        .d-print-none {
                            display: none !important;
                        }

                        /* Page setup */
                        @page {
                            size: A4;
                            margin: 2cm 1.5cm;
                        }

                        body {
                            print-color-adjust: exact;
                            -webkit-print-color-adjust: exact;
                            padding-top: 180px !important;
                            padding-bottom: 80px !important;
                        }

                        /* Layout adjustments */
                        .main-content {
                            width: 100% !important;
                            max-width: 100% !important;
                            margin-top: 0 !important;
                        }

                        .row {
                            display: block !important;
                        }

                        .col-lg-8,
                        .col-lg-4 {
                            width: 100% !important;
                            max-width: 100% !important;
                            float: none !important;
                        }

                        .sticky-top {
                            position: relative !important;
                            top: auto !important;
                        }

                        .status-card {
                            pointer-events: auto !important;
                            user-select: text !important;
                            opacity: 1 !important;
                        }

                        /* Card styling for print */
                        .card {
                            page-break-inside: avoid;
                            margin-bottom: 20px !important;
                            border: 1px solid #dee2e6 !important;
                            box-shadow: none !important;
                        }

                        .card-header {
                            background-color: #198754 !important;
                            color: white !important;
                            padding: 10px 15px !important;
                        }

                        /* Image sizing */
                        .image-thumbnail {
                            max-width: 200px !important;
                            height: auto !important;
                            page-break-inside: avoid;
                        }

                        /* Badge colors */
                        .badge {
                            border: 1px solid #000;
                            padding: 5px 10px !important;
                        }

                        /* Typography */
                        h4,
                        h5,
                        h6 {
                            page-break-after: avoid;
                        }

                        /* Ensure proper spacing */
                        .mb-4 {
                            margin-bottom: 1rem !important;
                        }

                        /* Remove duplicate header */
                        .main-content .row.mb-3:first-child {
                            display: none !important;
                        }
                    }
                </style>
            @endpush
        @endsection
