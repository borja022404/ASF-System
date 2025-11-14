@extends('layouts.Farmer.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"> <i
                        class="bi bi-check-circle me-2"></i> {{ session('success') }} <button type="button" class="btn-close"
                        data-bs-dismiss="alert"></button> </div>
            @endif
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-4">
                <h4 class="mb-0 fw-bold">
                    {{ $report->report_id }}</h4>
                <div>
                    @if ($report->report_status === 'submitted')
                        <a href="{{ route('farmer.reports.edit', $report->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    @endif
                    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- General Info -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Status:</strong>
                        <span
                            class="badge fs-6 bg-{{ $report->report_status == 'submitted' ? 'warning text-dark' : ($report->report_status == 'under_inspection' ? 'info' : 'success') }}">
                            {{ ucfirst(str_replace('_', ' ', $report->report_status)) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Risk Level:</strong>
                        <span
                            class="badge bg-{{ $report->risk_level == 'low' ? 'success' : ($report->risk_level == 'medium' ? 'warning' : 'danger') }}">
                            {{ ucfirst($report->risk_level) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Affected Pigs:</strong> {{ $report->affected_pig_count }}
                    </div>
                    <div class="col-md-3">
                        <strong>Submitted On:</strong> {{ $report->created_at->format('M d, Y h:i A') }}
                    </div>
                </div>

                <hr>

                <!-- Symptoms -->
                <h5 class="fw-bold mb-3"><i class="bi bi-clipboard-check me-2"></i> Reported Symptoms</h5>
                @php
                    $symptomsByRisk = $report->symptoms->groupBy('risk_level');
                @endphp

                @forelse($symptomsByRisk as $level => $symptoms)
                    <div class="mb-4">
                        <span
                            class="badge {{ $level === 'low' ? 'bg-success' : ($level === 'medium' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst($level) }} Risk
                        </span>
                        <ul class="mt-2 mb-0">
                            @foreach ($symptoms as $symptom)
                                <li>
                                    <strong>{{ ucwords(str_replace('_', ' ', $symptom->name)) }}</strong>
                                    @if ($symptom->description)
                                        – {{ $symptom->description }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-muted">No symptoms reported.</p>
                @endforelse

                <div class="mb-3">
                    <strong>Additional Description:</strong>
                    <p class="mb-0">{{ $report->symptoms_description ?? 'No additional details provided.' }}</p>
                </div>

                <hr>

                <!-- Location -->
                <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt me-2"></i> Location</h5>
                <p>{{ $report->barangay }}</p>
                @if ($report->latitude && $report->longitude)
                    <p class="text-muted"><strong>GPS Coordinates:</strong> {{ $report->latitude }}, {{ $report->longitude }}</p>
                @endif

                <hr>

                <!-- Images -->
                <h5 class="fw-bold mb-3"><i class="bi bi-images me-2"></i> Images</h5>
                @if ($report->images->isEmpty())
                    <p class="text-muted">No images uploaded.</p>
                @else
                    <div class="row g-3">
                        @foreach ($report->images as $image)
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top rounded"
                                        alt="Pig Image">
                                    <div class="card-body p-2">
                                        <p class="small text-muted mb-0">For notice: Uploaded on
                                            {{ $image->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <!-- Case Notes -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-journal-text me-2"></i> Case Notes</h5>
                    <!-- Add Note Button -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Note
                    </button>
                </div>

                @if ($report->notes->isEmpty())
                    <p class="text-muted">No notes or diagnoses added yet.</p>
                @else
                    <div class="list-group mb-4">
                        @foreach ($report->notes as $note)
                            <div class="list-group-item border-0 shadow-sm mb-2 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span
                                        class="badge {{ $note->note_type === 'vet_diagnosis' ? 'bg-success' : 'bg-info text-dark' }}">
                                        {{ ucfirst(str_replace('_', ' ', $note->note_type)) }}
                                    </span>
                                    <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $note->content }}</p>
                                <small class="text-muted">By: {{ $note->user->name ?? 'Unknown' }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Add Note Modal -->
                <div class="modal fade" id="addNoteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add a Note</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('farmer.notes.store', $report->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="noteContent" class="form-label">Note Content</label>
                                        <textarea name="content" id="noteContent" class="form-control" rows="4" placeholder="Type your note..." required></textarea>
                                    </div>



                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Add Note</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
