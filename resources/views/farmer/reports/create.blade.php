@extends('layouts.Farmer.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0 fw-bold">
                    <i class="bi bi-plus-circle me-2"></i> Report a New Case
                </h4>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('farmer.reports.store') }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate id="reportForm">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-list-check me-2"></i> Symptoms (Select all that apply) <span
                                class="text-danger">*</span>
                        </label>

                        <div class="accordion" id="symptomsAccordion">
                            @foreach (['low' => 'LOW RISK (Early or Mild Stage)', 'medium' => 'MEDIUM RISK', 'high' => 'HIGH RISK (Severe or Terminal Stage)'] as $level => $label)
                                <div
                                    class="accordion-item border-{{ $level === 'low' ? 'success' : ($level === 'medium' ? 'warning' : 'danger') }} mb-2">
                                    <h2 class="accordion-header" id="heading{{ ucfirst($level) }}">
                                        <button
                                            class="accordion-button {{ $level !== 'low' ? 'collapsed' : '' }} text-{{ $level }} fw-semibold"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ ucfirst($level) }}"
                                            aria-expanded="{{ $level === 'low' ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ ucfirst($level) }}">
                                            @if ($level == 'low')
                                                <i class="bi bi-emoji-smile me-2"></i>
                                            @elseif($level == 'medium')
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                            @else
                                                <i class="bi bi-skull me-2"></i>
                                            @endif
                                            {{ $label }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ ucfirst($level) }}"
                                        class="accordion-collapse collapse {{ $level === 'low' ? 'show' : '' }}"
                                        aria-labelledby="heading{{ ucfirst($level) }}" data-bs-parent="#symptomsAccordion">
                                        <div class="accordion-body bg-light">
                                            <div class="d-flex flex-column gap-2">
                                                @foreach ($symptoms[$level] ?? [] as $symptom)
                                                    @php
                                                        $checked = in_array($symptom->id, old('symptoms', []))
                                                            ? 'checked'
                                                            : '';
                                                    @endphp
                                                    <div class="form-check">
                                                        <input class="form-check-input symptom-checkbox" type="checkbox"
                                                            id="symptom_{{ $symptom->id }}" name="symptoms[]"
                                                            value="{{ $symptom->id }}" {{ $checked }}>
                                                        <label class="form-check-label" for="symptom_{{ $symptom->id }}">
                                                            {{ $symptom->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="symptomsError" class="text-danger small mt-2" style="display: none;">
                            Please select at least one symptom.
                        </div>
                        @error('symptoms')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ADDITIONAL DESCRIPTION --}}
                    <div class="mb-4">
                        <label for="symptoms_description" class="form-label fw-semibold">
                            <i class="bi bi-clipboard-pulse me-2"></i> Additional Description <span
                                class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('symptoms_description') is-invalid @enderror" id="symptoms_description"
                            name="symptoms_description" rows="4" placeholder="Describe the symptoms you observed in your pigs..."
                            required>{{ old('symptoms_description') }}</textarea>
                        @error('symptoms_description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- SYMPTOM ONSET DATE --}}
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="symptom_onset_date" class="form-label fw-semibold">
                                <i class="bi bi-calendar-date me-2"></i> Symptom Onset Date <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('symptom_onset_date') is-invalid @enderror"
                                id="symptom_onset_date" name="symptom_onset_date" value="{{ old('symptom_onset_date') }}"
                                max="{{ date('Y-m-d') }}" required>
                            @error('symptom_onset_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- AFFECTED PIG COUNT --}}
                        <div class="col-md-6 mb-4">
                            <label for="affected_pig_count" class="form-label fw-semibold">
                                <i class="bi bi-piggy-bank me-2"></i> Number of Affected Pigs <span
                                    class="text-danger">*</span>
                            </label>
                            <input type="number" min="1"
                                class="form-control @error('affected_pig_count') is-invalid @enderror"
                                id="affected_pig_count" name="affected_pig_count" value="{{ old('affected_pig_count') }}"
                                placeholder="Enter the number of affected pigs" required>
                            @error('affected_pig_count')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- LOCATION DETAILS --}}
                    <h5 class="fw-bold mb-3 mt-4"><i class="bi bi-geo-alt me-2"></i> Location Details</h5>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label for="barangay" class="form-label fw-semibold">Barangay <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('barangay') is-invalid @enderror"
                                id="barangay" name="barangay" value="{{ old('barangay') }}" required>
                            @error('barangay')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label for="city" class="form-label fw-semibold">City/Municipality <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                                name="city" value="{{ old('city') }}" required>
                            @error('city')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label for="province" class="form-label fw-semibold">Province <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('province') is-invalid @enderror"
                                id="province" name="province" value="{{ old('province') }}" required>
                            @error('province')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>

                    {{-- MAP & IMAGE UPLOAD sections remain unchanged --}}
                    @include('farmer.reports.partials.report-map-and-upload')

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i> Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {


            const symptomsData = {
                "low": [
                    "Loss of appetite – pig eats less or refuses to eat",
                    "Lethargy / Weakness – reduced activity, lying down more often",
                    "Slight fever (up to 40–41°C) – noticeable if measured or through warm skin",
                    "Reduced social behavior – isolation from herd or less playful",
                    "Dry or rough hair coat – dull appearance of the skin and hair",
                    "Mild coughing or breathing difficulty – occasional signs of discomfort",
                    "Slight weight loss – may not be immediately obvious",
                    "Constipation followed by diarrhea",
                    "Reduced fertility in sows – irregular heat cycles or abortion"
                ],
                "medium": [
                    "High fever (40.5–42°C) – persistent, not responsive to antibiotics",
                    "Red to purplish skin discoloration – especially on ears, snout, abdomen, and legs",
                    "Swelling around the eyes and neck – visible puffiness",
                    "Watery discharge from eyes or nose",
                    "Vomiting – may contain food or bile",
                    "Diarrhea (sometimes bloody) – can cause dehydration",
                    "Labored or rapid breathing – noticeable chest movement",
                    "Unsteady walking / staggering gait – weakness in legs",
                    "Shivering or trembling – due to fever and internal pain",
                    "Reduced body temperature in extremities – ears and limbs feel cold",
                    "Abortion in pregnant sows – fetal death or premature birth",
                    "Pale mucous membranes – inside mouth or eyes appear whitish"
                ],
                "high": [
                    "Severe skin hemorrhages (dark red to black patches) – especially on ears, abdomen, and legs",
                    "Open skin sores or lesions – visible bleeding or crusted spots",
                    "Bloody diarrhea – with foul odor",
                    "Bloody froth from nose or mouth",
                    "Difficulty standing or complete collapse – extreme weakness",
                    "Severe vomiting (may contain blood)",
                    "Foaming at the mouth – due to respiratory distress",
                    "Convulsions or muscle twitching – just before death",
                    "Blue-purple discoloration of ears, tail, and legs",
                    "Sudden death without visible symptoms – common in acute outbreaks",
                    "Coma or unresponsiveness – final phase before death"
                ]
            };

            const oldSelected = @json(old('symptoms', $report->symptoms ?? []));

            function renderSymptoms() {
                Object.keys(symptomsData).forEach(level => {
                    const container = document.getElementById(level);
                    if (!container) return;

                    const wrapper = document.createElement("div");
                    wrapper.className = "d-flex flex-wrap gap-3";

                    symptomsData[level].forEach((symptom, index) => {
                        const value = `${level}_${index}`;
                        const checked = oldSelected.includes(value) ? 'checked' : '';

                        const item = document.createElement("div");
                        item.className = "form-check";
                        item.innerHTML = `
                    <input class="form-check-input symptom-checkbox" type="checkbox" id="${value}" name="symptoms[]" value="${value}" ${checked}>
                    <label class="form-check-label" for="${value}">
                        ${symptom}
                    </label>
                `;
                        wrapper.appendChild(item);
                    });
                    container.appendChild(wrapper);
                });
            }

            renderSymptoms();

            // Symptom validation
            function validateSymptoms() {
                const checkboxes = document.querySelectorAll('.symptom-checkbox');
                const checked = Array.from(checkboxes).some(cb => cb.checked);
                const errorDiv = document.getElementById('symptomsError');

                if (!checked) {
                    errorDiv.style.display = 'block';
                    return false;
                } else {
                    errorDiv.style.display = 'none';
                    return true;
                }
            }

            // Add change listener to all symptom checkboxes
            document.querySelectorAll('.symptom-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', validateSymptoms);
            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let map, marker;
            const mapLoadingDiv = document.getElementById('mapLoading');

            // Philippines center coordinates (adjust to your specific area)
            const defaultLat = 14.5995; // Manila area - change to your region
            const defaultLng = 120.9842;
            const defaultZoom = 10;

            // Initialize the map
            function initMap() {
                try {
                    // Hide loading indicator
                    mapLoadingDiv.style.display = 'none';

                    // Create map
                    map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19
                    }).addTo(map);

                    // Add marker
                    marker = L.marker([defaultLat, defaultLng], {
                        draggable: true
                    }).addTo(map);

                    // Bind popup to marker
                    marker.bindPopup("Drag me to select location!").openPopup();

                    // Set initial location if old values exist
                    const oldLat = document.getElementById('latitude').value;
                    const oldLng = document.getElementById('longitude').value;

                    if (oldLat && oldLng) {
                        const lat = parseFloat(oldLat);
                        const lng = parseFloat(oldLng);
                        map.setView([lat, lng], 15);
                        marker.setLatLng([lat, lng]);
                        updateLocation(lat, lng);
                    } else {
                        // Set initial location
                        updateLocation(defaultLat, defaultLng);
                    }

                    // Map click event
                    map.on('click', function(e) {
                        const lat = e.latlng.lat;
                        const lng = e.latlng.lng;
                        marker.setLatLng([lat, lng]);
                        updateLocation(lat, lng);
                    });

                    // Marker drag event
                    marker.on('dragend', function(e) {
                        const coords = marker.getLatLng();
                        updateLocation(coords.lat, coords.lng);
                    });

                } catch (error) {
                    console.error('Map initialization error:', error);
                    mapLoadingDiv.innerHTML =
                        '<div class="alert alert-danger">Failed to load map. Please refresh the page.</div>';
                }
            }

            // Function to update coordinates & reverse geocode
            function updateLocation(lat, lng) {
                // Update hidden inputs
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);

                // Update marker popup
                marker.setPopupContent(`Location: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);

                // Reverse geocoding with timeout and error handling
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&limit=1`, {
                        signal: controller.signal,
                        headers: {
                            'User-Agent': 'PigHealthReportSystem/1.0'
                        }
                    })
                    .then(response => {
                        clearTimeout(timeoutId);
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.address) {
                            const address = data.address;

                            // More comprehensive address parsing for Philippines
                            const barangay = address.suburb || address.village || address.hamlet ||
                                address.neighbourhood || address.city_district || '';

                            const city = address.city || address.town || address.municipality ||
                                address.county || '';

                            const province = address.state || address.province || '';

                            // Update form fields
                            document.getElementById('barangay').value = barangay;
                            document.getElementById('city').value = city;
                            document.getElementById('province').value = province;

                            console.log('Location updated:', {
                                barangay,
                                city,
                                province
                            });
                        } else {
                            console.warn('No address data found for coordinates');
                        }
                    })
                    .catch(error => {
                        clearTimeout(timeoutId);
                        if (error.name === 'AbortError') {
                            console.warn('Geocoding request timed out');
                        } else {
                            console.error('Geocoding error:', error);
                        }
                        // Don't show error to user, just log it
                    });
            }

            // "Use My Location" button
            document.getElementById('getLocationBtn').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;

                btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1 spin"></i>Getting location...';
                btn.disabled = true;

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            map.setView([lat, lng], 16);
                            marker.setLatLng([lat, lng]);
                            updateLocation(lat, lng);

                            btn.innerHTML = originalText;
                            btn.disabled = false;

                            // Show success message
                            showAlert('Location updated successfully!', 'success');
                        },
                        function(error) {
                            let message = 'Unable to retrieve your location. ';
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    message += 'Please allow location access.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    message += 'Location information unavailable.';
                                    break;
                                case error.TIMEOUT:
                                    message += 'Location request timed out.';
                                    break;
                            }
                            showAlert(message, 'warning');

                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 300000
                        }
                    );
                } else {
                    showAlert('Geolocation is not supported by your browser.', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            });

            // "Clear Location" button
            document.getElementById('clearLocationBtn').addEventListener('click', function() {
                document.getElementById('latitude').value = "";
                document.getElementById('longitude').value = "";
                document.getElementById('barangay').value = "";
                document.getElementById('city').value = "";
                document.getElementById('province').value = "";

                marker.setLatLng([defaultLat, defaultLng]);
                map.setView([defaultLat, defaultLng], defaultZoom);
                marker.setPopupContent("Drag me to select location!");

                showAlert('Location cleared.', 'info');
            });

            // Image preview functionality
            const imageInput = document.getElementById('images');
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const files = e.target.files;
                    const previewContainer = document.getElementById('imagePreview');
                    previewContainer.innerHTML = '';

                    if (files.length > 5) {
                        showAlert('Maximum 5 images allowed.', 'warning');
                        this.value = '';
                        return;
                    }

                    Array.from(files).forEach((file, index) => {
                        if (file.size > 2 * 1024 * 1024) { // 2MB limit
                            showAlert(`File "${file.name}" is too large. Maximum size is 2MB.`,
                                'warning');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-md-3 col-sm-4 col-6 mb-3';
                            col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 120px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                            previewContainer.appendChild(col);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            // Enhanced form validation
            document.getElementById('reportForm').addEventListener('submit', function(e) {
                const lat = document.getElementById('latitude').value;
                const lng = document.getElementById('longitude').value;
                const barangay = document.getElementById('barangay').value.trim();
                const city = document.getElementById('city').value.trim();
                const province = document.getElementById('province').value.trim();
                const imageInput = document.getElementById('images');

                // Validate symptoms
                const checkboxes = document.querySelectorAll('.symptom-checkbox');
                const hasCheckedSymptom = Array.from(checkboxes).some(cb => cb.checked);

                let isValid = true;
                let errorMessage = '';

                if (!hasCheckedSymptom) {
                    isValid = false;
                    errorMessage = 'Please select at least one symptom.';
                    document.getElementById('symptomsError').style.display = 'block';
                }

                if (!lat || !lng) {
                    isValid = false;
                    errorMessage = 'Please select a location on the map.';
                }

                if (!barangay || !city || !province) {
                    isValid = false;
                    errorMessage =
                        'Please ensure all location fields (Barangay, City, Province) are filled.';
                }

                // Validate image upload
                if (imageInput && (!imageInput.files || imageInput.files.length === 0)) {
                    isValid = false;
                    errorMessage = 'Please upload at least one image.';
                }

                if (!isValid) {
                    e.preventDefault();
                    showAlert(errorMessage, 'error');
                    return false;
                }
            });

            // Utility function to show alerts
            function showAlert(message, type = 'info') {
                const alertDiv = document.createElement('div');
                alertDiv.className =
                    `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                document.body.appendChild(alertDiv);

                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            // Initialize map
            initMap();
        });
    </script>


    <style>
        .accordion-button {
            font-size: 1rem;
        }

        .form-check-label {
            cursor: pointer;
        }

        .form-check-input:checked+.form-check-label {
            color: #198754;
            font-weight: 600;
        }

        .text-danger {
            color: #dc3545 !important;
        }
    </style>
@endpush
