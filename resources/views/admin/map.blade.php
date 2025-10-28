@extends('layouts.Admin.app')

@section('content')
<div class="main-content">
    <!-- Map Section -->
    <div id="map-section" class="dashboard-section">
        <h3 class="mb-4">ASF Reports Map</h3>
        <div class="card">
            <div class="card-body">
                <div id="reportsMap" class="map-container" style="height: 500px; width: 100%;"></div>
            </div>
        </div>

        <!-- Map Controls -->
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h6>Legend:</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <span><i class="bi bi-geo-alt-fill text-success"></i> Low Risk</span>
                            <span><i class="bi bi-geo-alt-fill text-warning"></i> Medium Risk</span>
                            <span><i class="bi bi-geo-alt-fill text-danger"></i> High Risk</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6>Map Controls:</h6>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="centerMap()">
                            <i class="bi bi-crosshair"></i> Center Map
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="refreshMap()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Leaflet CSS - Move to styles section -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
      
<!-- Custom styles for markers -->
<style>
.custom-marker {
    background: none;
    border: none;
}

.leaflet-popup-content {
    margin: 8px 12px;
    line-height: 1.4;
}

.map-container {
    border-radius: 0.375rem;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<!-- Leaflet JS already loaded in layout, so we just need our map script -->
<script>
    let map;
    let markers = [];
    let reportsData = [];

    document.addEventListener("DOMContentLoaded", function () {
        console.log('DOM loaded, initializing map...');
        
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded!');
            return;
        }
        
        // Initialize map
        try {
            map = L.map('reportsMap').setView([17.613, 121.727], 8);
            console.log('Map initialized successfully');
        } catch (error) {
            console.error('Error initializing map:', error);
            return;
        }

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Wait a bit for the map container to be fully ready
        setTimeout(() => {
            map.invalidateSize();
            console.log('Map size invalidated');
            loadReports();
        }, 100);
    });

    function loadReports() {
        // Clear existing markers
        clearMarkers();
        
        try {
            // Get reports data from Laravel
            reportsData = @json($reports ?? []);
            console.log('Reports data:', reportsData);
            
            if (!reportsData || reportsData.length === 0) {
                console.warn('No reports data available');
                return;
            }

            let validMarkers = 0;
            
            reportsData.forEach((report, index) => {
                // Validate coordinates
                const lat = parseFloat(report.latitude);
                const lng = parseFloat(report.longitude);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.warn(`Invalid coordinates for report ${report.report_id || index}:`, lat, lng);
                    return;
                }
                
                if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                    console.warn(`Out of bounds coordinates for report ${report.report_id || index}:`, lat, lng);
                    return;
                }

                // Determine marker color based on risk level
                let color = getMarkerColor(report.risk_level);
                
                try {
                    // Create marker with custom icon
                    let marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: "custom-marker",
                            html: `<i class="bi bi-geo-alt-fill" style="color:${color};font-size:1.5rem;text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"></i>`,
                            iconSize: [25, 25],
                            iconAnchor: [12, 25]
                        })
                    }).addTo(map);

                    // Create popup content
                    const popupContent = `
                        <div style="min-width: 200px;">
                            <strong>Report ID:</strong> ${report.report_id || 'N/A'}<br>
                            <strong>Location:</strong> ${report.barangay || 'N/A'}, ${report.city || 'N/A'}<br>
                            <strong>Status:</strong> <span class="badge bg-${getStatusColor(report.report_status)}">${report.report_status || 'N/A'}</span><br>
                            <strong>Risk Level:</strong> <span class="badge bg-${getRiskColor(report.risk_level)}">${report.risk_level || 'N/A'}</span><br>
                            <small class="text-muted">${formatDate(report.created_at)}</small>
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    markers.push(marker);
                    validMarkers++;
                    
                } catch (markerError) {
                    console.error(`Error creating marker for report ${report.report_id || report.id || index}:`, markerError);
                }
            });
            
            console.log(`Successfully loaded ${validMarkers} markers out of ${reportsData.length} reports`);
            
            // Auto-fit bounds if we have markers
            if (markers.length > 0) {
                setTimeout(() => {
                    centerMap();
                }, 100);
            }
            
        } catch (error) {
            console.error('Error in loadReports:', error);
        }
    }

    function clearMarkers() {
        markers.forEach(marker => {
            try {
                map.removeLayer(marker);
            } catch (e) {
                console.warn('Error removing marker:', e);
            }
        });
        markers = [];
    }

    function getMarkerColor(riskLevel) {
        switch (String(riskLevel).toLowerCase()) {
            case 'high':
                return 'red';
            case 'medium':
                return 'orange';
            case 'low':
                return 'green';
            default:
                return 'blue';
        }
    }

    function getStatusColor(status) {
        switch (String(status).toLowerCase()) {
            case 'pending':
                return 'warning';
            case 'confirmed':
                return 'danger';
            case 'resolved':
                return 'success';
            case 'investigating':
                return 'info';
            default:
                return 'secondary';
        }
    }

    function getRiskColor(riskLevel) {
        switch (String(riskLevel).toLowerCase()) {
            case 'high':
                return 'danger';
            case 'medium':
                return 'warning';
            case 'low':
                return 'success';
            default:
                return 'secondary';
        }
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    }

    function centerMap() {
        if (markers.length === 0) {
            console.warn('No markers to center on');
            return;
        }
        
        try {
            if (markers.length === 1) {
                map.setView(markers[0].getLatLng(), 15);
            } else {
                let group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds(), {
                    padding: [20, 20],
                    maxZoom: 15
                });
            }
            console.log('Map centered successfully');
        } catch (error) {
            console.error('Error centering map:', error);
        }
    }

    function refreshMap() {
        console.log('Refreshing map...');
        loadReports();
    }

    // Add map resize handler for responsive behavior
    window.addEventListener('resize', function() {
        if (map) {
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }
    });
</script>
@endpush