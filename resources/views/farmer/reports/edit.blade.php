@extends('layouts.Farmer.app')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i> Edit Report <span class="fw-bold">#{{ $report->report_id }}</span>
            </h5>
            <a href="{{ route('farmer.reports.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('farmer.reports.update', $report->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                {{-- Reported Symptoms --}}
                {{-- Reported Symptoms --}}
                <section class="mb-4">
                    <label class="form-label fw-bold"><i class="bi bi-clipboard-check me-2"></i> Reported Symptoms</label>
                    <p class="text-muted small mb-3">Select all symptoms that apply to the pig's condition.</p>

                    @php
                        $selectedSymptoms = old('symptoms', $report->symptoms->pluck('id')->toArray() ?? []);
                    @endphp

                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        @foreach ($allSymptoms as $riskLevel => $symptoms)
                            <div class="col">
                                <div class="card h-100 shadow-sm p-3">
                                    <span
                                        class="badge {{ $riskLevel === 'low' ? 'bg-success' : ($riskLevel === 'medium' ? 'bg-warning text-dark' : 'bg-danger') }} mb-3">
                                        {{ ucfirst($riskLevel) }} Risk
                                    </span>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach ($symptoms as $symptom)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="symptoms[]"
                                                    id="symptom_{{ $symptom->id }}" value="{{ $symptom->id }}"
                                                    {{ in_array($symptom->id, $selectedSymptoms) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="symptom_{{ $symptom->id }}">
                                                    {{ ucwords(str_replace('_', ' ', $symptom->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('symptoms')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </section>


                <hr>

                {{-- Symptoms & Description --}}
                <section class="mb-4">
                    <label for="symptoms_description" class="form-label fw-bold">
                        <i class="bi bi-file-earmark-text me-2"></i> Symptoms & Description
                    </label>
                    <p class="text-muted small mb-3">Provide more details about the pig's symptoms and behavior.</p>
                    <textarea class="form-control @error('symptoms_description') is-invalid @enderror" id="symptoms_description"
                        name="symptoms_description" rows="4" required>{{ old('symptoms_description', $report->symptoms_description) }}</textarea>
                    @error('symptoms_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </section>

                <hr>

                {{-- Dates, Count & Status --}}
                <section class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="symptom_onset_date" class="form-label fw-bold">
                                <i class="bi bi-calendar-check me-2"></i> Symptom Onset Date
                            </label>
                            <p class="text-muted small mb-3">The date you first noticed the symptoms.</p>
                            <input type="date" class="form-control @error('symptom_onset_date') is-invalid @enderror"
                                id="symptom_onset_date" name="symptom_onset_date"
                                value="{{ old('symptom_onset_date', $report->symptom_onset_date) }}" required>
                            @error('symptom_onset_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="affected_pig_count" class="form-label fw-bold">
                                <i class="bi bi-piggy-bank me-2"></i> Number of Affected Pigs
                            </label>
                            <p class="text-muted small mb-3">Enter how many pigs showed symptoms.</p>
                            <input type="number" min="1"
                                class="form-control @error('affected_pig_count') is-invalid @enderror"
                                id="affected_pig_count" name="affected_pig_count"
                                value="{{ old('affected_pig_count', $report->affected_pig_count) }}" placeholder="e.g. 3"
                                required>
                            @error('affected_pig_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <hr>

                {{-- Location --}}
                <section class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="bi bi-geo-alt me-2"></i> Location
                    </label>
                    <p class="text-muted small mb-3">Drag the map marker or click on the map to specify the exact location.
                    </p>

                    <div id="map" class="rounded shadow-sm mb-3" style="height: 350px; width: 100%;"></div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="barangay" class="form-label">Barangay</label>
                            <input type="text" class="form-control @error('barangay') is-invalid @enderror"
                                id="barangay" name="barangay" value="{{ old('barangay', $report->barangay) }}" required>
                            @error('barangay')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Hidden inputs for city and province --}}
                    <input type="hidden" name="city" value="Allacapan">
                    <input type="hidden" name="province" value="Cagayan">

                    {{-- Hidden coordinates --}}
                    <input type="hidden" id="latitude" name="latitude"
                        value="{{ old('latitude', $report->latitude) }}">
                    <input type="hidden" id="longitude" name="longitude"
                        value="{{ old('longitude', $report->longitude) }}">
                </section>

                <hr>

                {{-- Note on Images (KEPT as requested) --}}
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>
                        You cannot edit images for this report. If you need to add or remove images, please contact support
                        with the report ID.
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-grid d-md-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bi bi-save me-2"></i> Update Report
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const defaultLat = {{ old('latitude', $report->latitude ?? 17.933) }};
            const defaultLng = {{ old('longitude', $report->longitude ?? 121.905) }};

            const map = L.map('map').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);

            async function updateLocationFromCoordinates(lat, lng) {
                try {
                    const res = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                            headers: {
                                'User-Agent': 'FarmReportApp/1.0'
                            }
                        });
                    const data = await res.json();
                    if (data.address) {
                        document.getElementById('barangay').value = data.address.suburb || data.address
                            .village || 'Unknown Barangay';
                        document.getElementById('city').value = data.address.city || data.address.town || data
                            .address.municipality || 'Unknown City';
                        document.getElementById('province').value = data.address.state || data.address
                            .province || 'Unknown Province';
                    }
                } catch (err) {
                    console.error('Error fetching location:', err);
                }
            }

            marker.on('dragend', e => {
                const {
                    lat,
                    lng
                } = marker.getLatLng();
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
                updateLocationFromCoordinates(lat, lng);
            });

            map.on('click', e => {
                marker.setLatLng(e.latlng);
                document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
                document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
                updateLocationFromCoordinates(e.latlng.lat, e.latlng.lng);
            });
        });
    </script>
@endpush
