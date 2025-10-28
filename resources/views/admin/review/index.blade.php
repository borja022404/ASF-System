@extends('layouts.admin.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5 text-light-green">Report #{{ $report->id }}</h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-success">
            <i class="fas fa-arrow-left me-2"></i>Back to Reports
        </a>
    </div>

    <div class="card bg-dark-green text-light-green border-0 shadow-lg p-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom-0">
            @php
                $status_color = '';
                $status_icon = '';
                switch ($report->status) {
                    case 'pending':
                        $status_color = 'warning';
                        $status_icon = 'fas fa-hourglass-half';
                        break;
                    case 'healthy':
                        $status_color = 'success';
                        $status_icon = 'fas fa-check-circle';
                        break;
                    case 'infected':
                        $status_color = 'danger';
                        $status_icon = 'fas fa-exclamation-triangle';
                        break;
                    default:
                        $status_color = 'secondary';
                        $status_icon = 'fas fa-question-circle';
                        break;
                }
            @endphp
            <span class="badge rounded-pill bg-{{ $status_color }} fs-6">
                <i class="{{ $status_icon }} me-2"></i>{{ ucfirst($report->status) }}
            </span>
            <small class="text-muted">Submitted: {{ $report->created_at->format('M d, Y') }} at {{ $report->created_at->format('h:i A') }}</small>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <h5 class="text-highlight-green"><i class="fas fa-info-circle me-2"></i>Description</h5>
                <p class="card-text">{{ $report->description }}</p>
            </div>

            <div class="mb-4">
                <h5 class="text-highlight-green"><i class="fas fa-stethoscope me-2"></i>Symptoms Reported</h5>
                @php 
                    $symptoms = json_decode($report->symptoms, true); 
                @endphp
                @if(!empty($symptoms))
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($symptoms as $symptom)
                            <span class="badge bg-secondary-green px-3 py-2">{{ str_replace('_', ' ', ucfirst($symptom)) }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted fst-italic">No symptoms were specified.</p>
                @endif
            </div>

            <div>
                <h5 class="text-highlight-green"><i class="fas fa-images me-2"></i>Attached Images</h5>
                <hr>
                @php 
                    $images = json_decode($report->images, true); 
                @endphp
                <div class="image-gallery d-flex flex-wrap gap-3">
                    @if(!empty($images))
                        @foreach($images as $img)
                            <a href="{{ asset('storage/' . $img) }}" data-lightbox="report-images">
                                <img src="{{ asset('storage/' . $img) }}" class="img-fluid rounded shadow-sm thumbnail-lg-img" alt="Pig Image">
                            </a>
                        @endforeach
                    @else
                        <p class="text-muted">No images were uploaded for this report.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --dark-green: #1a422a;
        --deep-green: #0c2317;
        --light-green: #c4d7b9;
        --highlight-green: #688e58;
        --secondary-green: #3d6046;
    }
    
    body {
        background-color: var(--deep-green);
    }
    
    .text-light-green {
        color: var(--light-green) !important;
    }
    
    .text-highlight-green {
        color: var(--highlight-green) !important;
    }
    
    .btn-outline-success {
        color: var(--highlight-green);
        border-color: var(--highlight-green);
    }

    .btn-outline-success:hover {
        background-color: var(--highlight-green);
        color: var(--dark-green);
    }

    .card {
        background-color: var(--dark-green);
        color: var(--light-green);
        border: 1px solid var(--highlight-green);
    }
    
    .badge.rounded-pill {
        font-weight: bold;
    }

    .badge.bg-secondary-green {
        background-color: var(--secondary-green) !important;
        color: var(--light-green);
    }

    .thumbnail-lg-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 2px solid var(--highlight-green);
        transition: transform 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    .thumbnail-lg-img:hover {
        transform: scale(1.05);
        border-color: #82e0aa;
    }
</style>
@endpush

@push('scripts')
{{-- Consider adding a lightbox library here for image previews --}}
{{-- Example: <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script> --}}
@endpush