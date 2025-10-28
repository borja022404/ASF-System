<x-app-layout>
    @section('content')
        @php
            $role = auth()->user()->roles->first()?->name;
        @endphp
        @if ($role === 'admin')
            <div class="main-content">
            @else
                <div class="container py-5">
        @endif
        <div class="row justify-content-center g-5">

            <!-- Profile Information -->
            <div class="col-12 col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-success text-white d-flex flex-column">
                        <h5 class="mb-1"><i class="fas fa-user-circle me-2"></i>Profile Information</h5>
                        <small class="opacity-75">Update your account’s profile information and email
                            address.</small>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="col-12 col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-warning text-dark d-flex flex-column">
                        <h5 class="mb-1"><i class="fas fa-lock me-2"></i>Update Password</h5>
                        <small class="opacity-75">Ensure your account is using a strong password to stay
                            secure.</small>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="col-12 col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-danger text-white d-flex flex-column">
                        <h5 class="mb-1"><i class="fas fa-user-slash me-2"></i>Delete Account</h5>
                        <small class="opacity-75">Once your account is deleted, all resources and data will be
                            permanently removed.</small>
                    </div>
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
        </div>

        @push('styles')
            <style>
                .card {
                    border-radius: 1rem;
                    overflow: hidden;
                    transition: all 0.3s ease-in-out;
                }

                .card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
                }

                .card-header {
                    padding: 1rem 1.25rem;
                }

                .card-body {
                    padding: 2rem;
                }
            </style>
        @endpush
    @endsection
</x-app-layout>
