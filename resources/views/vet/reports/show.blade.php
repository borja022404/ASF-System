@extends('layouts.Vet.app')


@section('content')
    <div class="container-fluid my-3">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Report #{{ $report->report_id }}</h4>
                    </div>
                    <div class="d-flex align-items-center">

                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm me-2 d-flex">
                            <i class="bi bi-arrow-left me-2"></i><span>Back</span>
                        </a>
                        {{-- Added Risk Level Badge --}}
                        @php
                            $riskColor = 'secondary';
                            if ($report->risk_level == 'low') {
                                $riskColor = 'success';
                            } elseif ($report->risk_level == 'medium') {
                                $riskColor = 'warning';
                            } elseif ($report->risk_level == 'high') {
                                $riskColor = 'danger';
                            }
                        @endphp
                        <span class="badge bg-{{ $riskColor }} me-2 fs-6 px-3 py-2">
                            {{ ucfirst($report->risk_level) ?? 'No Risk' }}
                        </span>
                        {{-- End of Risk Level Badge --}}
                        <span
                            class="badge bg-{{ $report->report_status == 'submitted' ? 'warning' : ($report->report_status == 'under_inspection' ? 'info' : ($report->report_status == 'resolved' ? 'success' : 'secondary')) }} fs-6 px-3 py-2">
                            {{ ucfirst(str_replace('_', ' ', $report->report_status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
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

                            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel"
                                aria-hidden="true" style="z-index: 9999">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                                            <div class="ms-auto d-flex align-items-center">
                                                <a href="#" id="downloadLink" download
                                                    class="btn btn-sm btn-outline-success me-2" title="Download Image">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn-close" onclick="closeImageModal()"></button>
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

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-heart-pulse-fill me-2"></i> Health Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">

                            {{-- Pig Health Status --}}
                            <div class="col-md-4">
                                @php
                                    $healthColor = match ($report->pig_health_status) {
                                        'infected' => 'success',
                                        'sick' => 'warning',
                                        'isolate' => 'danger',
                                        'dead' => 'dark',
                                        default => 'secondary',
                                    };
                                @endphp
                                <div class="border-start border-4 border-{{ $healthColor }} ps-3">
                                    <small class="text-muted">Health Status</small>
                                    <div class="fw-semibold text-{{ $healthColor }}">
                                        {{ ucfirst($report->pig_health_status ?? 'Unassessed') }}
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


                            {{-- User Description --}}
                            @if ($report->symptoms_description)
                                <div class="col-12">
                                    <div class="bg-light rounded p-3">
                                        <h6 class="fw-bold mb-2">
                                            <i class="bi bi-clipboard-pulse text-primary me-2"></i> symptoms Description
                                        </h6>
                                        <p class="mb-0">{{ $report->symptoms_description }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Mortality Date --}}
                            @if (!empty($report->num_mortality) && $report->num_mortality > 0 && $report->mortality_date)
                                <div class="col-12">
                                    <div class="border-start border-4 border-danger ps-3">
                                        <small class="text-muted">Date of Mortality</small>
                                        <div class="fw-semibold">
                                            {{ \Carbon\Carbon::parse($report->mortality_date)->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>



                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location Details</h6>
                        @if ($report->latitude && $report->longitude)
                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
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
                            @if ($report->latitude && $report->longitude)
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">GPS Coordinates</small>
                                            <div class="fw-semibold font-monospace">{{ $report->latitude }},
                                                {{ $report->longitude }}
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-success btn-sm me-2"
                                                onclick="copyCoordinates('{{ $report->latitude }}, {{ $report->longitude }}')"
                                                title="Copy Coordinates">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                                                target="_blank" class="btn btn-outline-primary btn-sm"
                                                title="Open in Google Maps">
                                                <i class="fab fa-google"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="text-muted">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No GPS coordinates available for this report
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
          <div class="col-lg-4">
    <div class="sticky-top" style="top: 20px;">

        {{-- If report is resolved, show a message --}}
        @if ($report->report_status == 'resolved')
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body text-center">
                    <h5 class="text-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>This report is resolved
                    </h5>
                </div>
            </div>
        @else
            {{-- Show these only if NOT resolved --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-success text-white d-flex align-items-center">
                    <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Report Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('vet.reports.reportupdate', $report->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        {{-- Report Status --}}
                        <div class="mb-3">
                            <label class="form-label small text-secondary">Current Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="report_status"
                                        id="status_under_inspection" value="under_inspection"
                                        {{ $report->report_status == 'under_inspection' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="status_under_inspection">
                                        <i class="fas fa-search me-1 text-info"></i>For Inspection
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="report_status"
                                        id="status_resolved" value="resolved"
                                        {{ $report->report_status == 'resolved' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="status_resolved">
                                        <i class="fas fa-check-circle me-1 text-success"></i>Resolved
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-sync-alt me-2"></i>Update Report Status
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-teal text-white d-flex align-items-center" style="background-color:#20c997;">
                    <h6 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Health Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('vet.reports.healthupdate', $report->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        {{-- Risk Level --}}
                        <div class="mb-3">
                            <label class="form-label text-secondary fw-semibold">Risk Level</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="risk_level"
                                        id="risk_low" value="low"
                                        {{ $report->risk_level == 'low' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="risk_low">
                                        <i class="fas fa-smile text-success me-1"></i>Low
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="risk_level"
                                        id="risk_medium" value="medium"
                                        {{ $report->risk_level == 'medium' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="risk_medium">
                                        <i class="fas fa-exclamation-circle text-warning me-1"></i>Medium
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="risk_level"
                                        id="risk_high" value="high"
                                        {{ $report->risk_level == 'high' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="risk_high">
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
                                        id="health_infected" value="infected"
                                        {{ $report->pig_health_status == 'infected' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="health_infected">
                                        <i class="fas fa-virus text-danger me-1"></i>Infected
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pig_health_status"
                                        id="health_isolate" value="isolate"
                                        {{ $report->pig_health_status == 'isolate' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="health_isolate">
                                        <i class="fas fa-user-shield text-info me-1"></i>Isolate
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-teal w-100 text-white" style="background-color:#20c997;">
                            <i class="fas fa-sync-alt me-2"></i>Update Health Status
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- These sections are always visible --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add Note</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('vet.notes.store', $report->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="note_type" value="vet_diagnosis">
                    <div class="mb-3">
                        <label for="content" class="form-label small">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="4"
                            placeholder="Enter your note or diagnosis here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-info w-100">
                        <i class="fas fa-plus me-2"></i>Save Note
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-notes-medical me-2"></i>Case Notes
                    <span class="badge bg-light text-dark ms-2">{{ $report->notes->count() }}</span>
                </h6>
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
                                        class="badge bg-{{ $note->note_type == 'vet_diagnosis' ? 'success' : ($note->note_type == 'admin_review' ? 'warning' : 'info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $note->note_type)) }}
                                    </span>
                                    <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-2 small">{{ $note->content }}</p>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-1 me-2" style="width: 20px; height: 20px;">
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

        </div>
    </div>

    @if ($report->latitude && $report->longitude)
        <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="locationModalLabel">
                            <i class="fas fa-map-marker-alt me-2"></i>Report Location
                        </h5>
                        <div class="ms-auto d-flex align-items-center">
                            <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                                target="_blank" class="btn btn-sm btn-outline-primary me-2" title="Open in Google Maps">
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
                            <div class="col-md-3">
                                <strong>Barangay:</strong> {{ $report->barangay }}
                            </div>
                            <div class="col-md-3">
                                <strong>City:</strong> {{ $report->city }}
                            </div>
                            <div class="col-md-3">
                                <strong>Province:</strong> {{ $report->province }}
                            </div>
                            <div class="col-md-3">
                                <strong>Coordinates:</strong>
                                <span class="font-monospace">{{ $report->latitude }}, {{ $report->longitude }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1"
                                    onclick="copyCoordinates('{{ $report->latitude }}, {{ $report->longitude }}')"
                                    title="Copy Coordinates">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let locationMap;

            // Initialize location map when modal is shown
            document.getElementById('locationModal')?.addEventListener('shown.bs.modal', function () {
                if (!locationMap) {
                    const lat = {{ $report->latitude ?? 'null' }};
                    const lng = {{ $report->longitude ?? 'null' }};

                    if (lat && lng) {
                        // Initialize map
                        locationMap = L.map('locationMap').setView([lat, lng], 15);

                        // Add tile layer
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                            maxZoom: 19
                        }).addTo(locationMap);

                        // Add marker
                        const marker = L.marker([lat, lng]).addTo(locationMap);

                        // Add popup with report info
                        marker.bindPopup(`
                            <div class="text-center">
                                <strong>Report #{{ $report->report_id }}</strong><br>
                                <small class="text-muted">{{ $report->barangay }}, {{ $report->city }}</small><br>
                                <small class="text-muted">{{ $report->province }}</small>
                            </div>
                        `).openPopup();

                        // Add circle to show approximate area
                        L.circle([lat, lng], {
                            color: 'red',
                            fillColor: '#f03',
                            fillOpacity: 0.1,
                            radius: 500
                        }).addTo(locationMap);
                    }
                } else {
                    // Invalidate size to fix map display issues
                    setTimeout(() => {
                        locationMap.invalidateSize();
                    }, 100);
                }
            });

            // Image modal functions
            window.openImageModal = function (imageSrc, imageNumber) {
                const modal = document.getElementById('imageModal');
                const modalImage = document.getElementById('modalImage');
                const modalTitle = document.getElementById('imageModalLabel');
                const downloadLink = document.getElementById('downloadLink');

                modalImage.src = imageSrc;
                modalTitle.textContent = 'Image ' + imageNumber;
                downloadLink.href = imageSrc;

                modal.style.display = 'block';
                modal.classList.add('show');
                modal.setAttribute('aria-modal', 'true');
                modal.setAttribute('role', 'dialog');

                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'modal-backdrop';
                document.body.appendChild(backdrop);

                // Prevent body scroll
                document.body.style.overflow = 'hidden';
                document.body.style.paddingRight = '17px';

                // Close on backdrop click
                backdrop.onclick = closeImageModal;

                // Close on Escape key
                document.addEventListener('keydown', handleEscapeKey);
            };

            window.closeImageModal = function () {
                const modal = document.getElementById('imageModal');
                const backdrop = document.getElementById('modal-backdrop');

                modal.style.display = 'none';
                modal.classList.remove('show');
                modal.removeAttribute('aria-modal');
                modal.removeAttribute('role');

                if (backdrop) {
                    backdrop.remove();
                }

                document.body.style.overflow = '';
                document.body.style.paddingRight = '';

                document.removeEventListener('keydown', handleEscapeKey);
            };

            function handleEscapeKey(event) {
                if (event.key === 'Escape') {
                    closeImageModal();
                }
            }

            // Copy coordinates function
            window.copyCoordinates = function (coordinates) {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(coordinates).then(() => {
                        showAlert('Coordinates copied to clipboard!', 'success');
                    }).catch(() => {
                        fallbackCopyText(coordinates);
                    });
                } else {
                    fallbackCopyText(coordinates);
                }
            };

            function fallbackCopyText(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    document.execCommand('copy');
                    showAlert('Coordinates copied to clipboard!', 'success');
                } catch (err) {
                    showAlert('Failed to copy coordinates', 'error');
                }

                document.body.removeChild(textArea);
            }

            // Utility function to show alerts
            function showAlert(message, type = 'info') {
                const alertDiv = document.createElement('div');
                alertDiv.className =
                    `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        });
    </script>

    <style>
        .border-start {
            border-left-width: 4px !important;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s;
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
    </style>
@endpush