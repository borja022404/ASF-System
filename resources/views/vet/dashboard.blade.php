@extends('layouts.Vet.app')

@section('content')
<div class="">
    {{-- Header --}}
    <div class="card-header bg-dark text-white text-center py-4">
        <h3 class="mb-1">
            <i class="bi bi-shield-plus me-2"></i> Vet Dashboard
        </h3>
        <p class="mb-0 fs-6 opacity-75">
            Welcome back, {{ Auth::user()->name }}! Stay updated and respond quickly to farmer reports.
        </p>
    </div>

    <div class="card-body p-5">
        <p class="lead text-center mb-5 fw-normal">
            A quick overview of reports that need your **immediate attention**.
        </p>

        {{-- Report Status Cards --}}
        <div class="row g-4">
            {{-- High Risk --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <a href="{{ route('vet.reports.high_risk') }}" class="dashboard-card high-risk">
                    <div class="card-icon">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <div class="card-content">
                        <h5 class="card-title">Deadly / High-Risk</h5>
                        <div class="card-number">{{ $highRiskReports->count() }}</div>
                    </div>
                    <div class="card-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </a>
            </div>

            {{-- unassessed --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <a href="{{ route('vet.reports.unassessed') }}" class="dashboard-card unassessed">
                    <div class="card-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="card-content">
                        <h5 class="card-title">Unassessed Reports</h5>
                        <div class="card-number">{{ $unassessedReports->count() }}</div>
                        <p class="card-description">Awaiting your inspection</p>
                    </div>
                    <div class="card-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </a>
            </div>

            {{-- Under Review --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <a href="{{ route('vet.reports.underreview') }}" class="dashboard-card under-review">
                    <div class="card-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="card-content">
                        <h5 class="card-title">For Inspection</h5>
                        <div class="card-number">{{ $underReviewReports->count() }}</div>
                        <p class="card-description">Currently being evaluated</p>
                    </div>
                    <div class="card-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </a>
            </div>

            {{-- Completed Reports --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <a href="{{ route('vet.reports.resolved') }}" class="dashboard-card completed">
                    <div class="card-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="card-content">
                        <h5 class="card-title">Resolved Reports</h5>
                        <div class="card-number">{{ $resolved->count() ?? 0 }}</div>
                        <p class="card-description">Cases you've successfully handled</p>
                    </div>
                    <div class="card-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </a>
            </div>

            {{-- All Reports --}}
            <div class="col-sm-12 col-md-6 col-lg-4">
                <a href="{{ route('vet.reports.index') }}" class="dashboard-card all-reports">
                    <div class="card-icon">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div class="card-content">
                        <h5 class="card-title">All Reports</h5>
                        <div class="card-number">{{ $allReports->count() ?? 0 }}</div>
                        <p class="card-description">Browse the full report list</p>
                    </div>
                    <div class="card-arrow">
                        <i class="bi bi-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dashboard-card {
        display: flex;
        align-items: center;
        padding: 2rem;
        border-radius: 1rem;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        border: 1px solid #e2e8f0;
        min-height: 120px;
        position: relative;
        overflow: hidden;
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
        border-color: transparent;
    }

    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.5rem;
        flex-shrink: 0;
    }

    .card-icon i {
        font-size: 1.5rem;
        color: white;
    }

    .card-content {
        flex-grow: 1;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .card-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .card-description {
        font-size: 0.875rem;
        margin: 0;
        color: #6b7280;
    }

    .card-arrow {
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
        color: #9ca3af;
    }

    .dashboard-card:hover .card-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    /* High Risk Theme */
    .high-risk::before {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }
    .high-risk .card-icon {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
    }
    .high-risk .card-number {
        color: #dc2626;
    }
    .high-risk:hover {
        border-color: #dc2626;
    }

    /* unassessed Theme */
    .unassessed::before {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .unassessed .card-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .unassessed .card-number {
        color: #f59e0b;
    }
    .unassessed:hover {
        border-color: #f59e0b;
    }

    /* Under Review Theme */
    .under-review::before {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .under-review .card-icon {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }
    .under-review .card-number {
        color: #3b82f6;
    }
    .under-review:hover {
        border-color: #3b82f6;
    }

    /* Completed Theme */
    .completed::before {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .completed .card-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }
    .completed .card-number {
        color: #10b981;
    }
    .completed:hover {
        border-color: #10b981;
    }

    /* All Reports Theme */
    .all-reports::before {
        background: linear-gradient(135deg, #6b7280, #4b5563);
    }
    .all-reports .card-icon {
        background: linear-gradient(135deg, #6b7280, #4b5563);
    }
    .all-reports .card-number {
        color: #6b7280;
    }
    .all-reports:hover {
        border-color: #6b7280;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dashboard-card {
            padding: 1.5rem;
            min-height: 100px;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            margin-right: 1rem;
        }
        
        .card-icon i {
            font-size: 1.25rem;
        }
        
        .card-number {
            font-size: 1.75rem;
        }
        
        .card-title {
            font-size: 0.9rem;
        }
        
        .card-description {
            font-size: 0.8rem;
        }
    }
</style>
@endpush