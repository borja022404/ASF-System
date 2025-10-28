@can('admin-access')
    @extends('layouts.Admin.app')

    @section('content')
        <div class="main-content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div id="users-section" class="dashboard-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>User Management</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus me-2"></i>Add User
                    </button>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-primary">{{ $adminCount }}</div>
                                    <div class="stat-label">Administrators</div>
                                </div>
                                <i class="bi bi-person-fill-gear text-primary" style="font-size: 2rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-success">{{ $vetCount }}</div>
                                    <div class="stat-label">Veterinarians</div>
                                </div>
                                <i class="bi bi-heart-pulse text-success" style="font-size: 2rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number text-info">{{ $farmerCount }}</div>
                                    <div class="stat-label">Farmers</div>
                                </div>
                                <i class="bi bi-person-fill text-info" style="font-size: 2rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Avatar</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($allusers as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td><img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=32"
                                                    class="rounded-circle"></td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                    class="btn btn-sm btn-warning">Edit</a>
                                               
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No users found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $allusers->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add New User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" required
                                    placeholder="Enter full name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" required
                                    placeholder="Enter email address">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required
                                    placeholder="Enter password" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" required
                                    placeholder="Confirm password" minlength="6">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Administrator</option>
                                    <option value="vet">Veterinarian</option>
                                    <option value="farmer">Farmer</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check me-1"></i>Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
@endcan
