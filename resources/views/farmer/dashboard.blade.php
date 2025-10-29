@extends('layouts.Farmer.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Welcome Section --}}
        <div class="welcome-section mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="fw-bold text-dark mb-2">Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="text-muted mb-0">Manage your African Swine Fever (ASF) reports</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('farmer.reports.create') }}" class="btn btn-primary btn-lg w-100 w-md-auto">
                        <i class="bi bi-plus-circle me-2"></i>Report New Case
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row g-3 mb-4">
            {{-- Submitted --}}
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('farmer.reports.submitted') }}" class="text-decoration-none">
                <div class="stats-card">
                    <div class="stats-icon bg-primary-subtle">
                        <i class="bi bi-file-earmark-text text-primary"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $unassessedReports->count() ?? 0 }}</h3>
                        <p class="stats-label">Submitted</p>
                        <small class="stats-desc">Awaiting inspection</small>
                    </div>
                </div>
                </a>
            </div>

            {{-- For Inspection --}}
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('farmer.reports.inspection') }}" class="text-decoration-none">
                <div class="stats-card">
                    <div class="stats-icon bg-warning-subtle">
                        <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                    <div class="stats-content">
                        <h3 class="stats-number">{{ $underReviewReports->count() ?? 0 }}</h3>
                        <p class="stats-label">For Inspection</p>
                        <small class="stats-desc">Currently reviewing</small>
                    </div>
                </div>
                </a>
            </div>

            {{-- Resolved --}}
            <div class="col-lg-4 col-md-12">
                <a href="{{ route('farmer.reports.resolved') }}" class="text-decoration-none">
                <div class="stats-card">
                    <div class="stats-icon bg-success-subtle">
                        <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $resolved->count() ?? 0 }}</div>
                        <p class="stats-label">Resolved</p>
                        <small class="stats-desc">Completed cases</small>
                    </div>
                </div>
                </a>
            </div>
        </div>

        {{-- Quick Links & Tips --}}
        <div class="row g-3">
            {{-- Quick Links --}}
            <div class="col-lg-7">
                <div class="content-card">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Quick Actions
                    </h5>
                    <div class="quick-links">
                        <a href="{{ route('farmer.reports.index') }}" class="border d-flex quick-link-item shadow-lg">
                            <div class="d-flex align-items-center">
                                <div class="quick-link-icon">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div>
                                    <div class="quick-link-title">View All Reports</div>
                                    <small class="text-muted">Check status and history</small>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="col-lg-5">
                <div class="content-card tips-card">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-lightbulb me-2"></i>Helpful Tips
                    </h5>
                    <ul class="tips-list">
                        <li>Report symptoms immediately when noticed</li>
                        <li>Verify location and affected animal count</li>
                        <li>Keep documentation of all reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Base Styles */
        body {
            background-color: #f8f9fa;
        }

        /* Welcome Section */
        .welcome-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Statistics Cards */
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
        }

        .stats-content {
            flex: 1;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            line-height: 1;
            color: #212529;
        }

        .stats-label {
            font-size: 1rem;
            font-weight: 600;
            margin: 0.5rem 0 0.25rem 0;
            color: #495057;
        }

        .stats-desc {
            color: #6c757d;
            font-size: 0.875rem;
        }

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .card-title {
            font-weight: 600;
            color: #212529;
            margin-bottom: 1rem;
        }

        

        .quick-link-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        .quick-link-item:hover {
            background: #e9ecef;
            transform: translateX(4px);
        }

        .quick-link-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.25rem;
            color: #495057;
        }

        .quick-link-title {
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.125rem;
        }

        .quick-link-item i.bi-chevron-right {
            color: #adb5bd;
        }

        /* Tips Card */
        .tips-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tips-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
            position: relative;
            padding-left: 1.5rem;
            color: #495057;
        }

        .tips-list li:last-child {
            border-bottom: none;
        }

        .tips-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #198754;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 767px) {
            .welcome-section {
                text-align: center;
            }

            .stats-card {
                flex-direction: column;
                text-align: center;
            }

            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }

            .stats-number {
                font-size: 1.75rem;
            }

            

            .quick-link-icon {
                margin-right: 0;
     
            }
        }

        @media (max-width: 575px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .welcome-section,
            .stats-card,
            .content-card {
                padding: 1rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .stats-number {
                font-size: 1.5rem;
            }
        }
    </style>
@endpush
