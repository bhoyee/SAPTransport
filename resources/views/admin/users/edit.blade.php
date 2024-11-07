@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
    $isConsultant = auth()->user()->hasRole('consultant');
@endphp

@extends($layout)

@section('content')
<h1 class="app-page-title">Edit User</h1>

<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="update-user-form">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="form-group mb-3">
                <label for="name">Full Name</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ $user->name }}" required>
            </div>

            <!-- Email -->
            <div class="form-group mb-3">
                <label for="email">Email Address</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>
            </div>

            <!-- Phone -->
            <div class="form-group mb-3">
                <label for="phone">Phone</label>
                <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}">
            </div>

            <!-- Gender -->
            <div class="form-group mb-3">
                <label for="gender">Gender</label>
                <select id="gender" class="form-control" name="gender" required>
                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <!-- Role (conditionally readonly for consultants) -->
        <!-- Role -->
<div class="form-group mb-3">
    <label for="role">Role</label>
    <select id="role" class="form-control" name="role" 
            {{ auth()->user()->hasRole('consultant') ? 'disabled' : '' }} required>
        <option value="passenger" {{ $user->role == 'passenger' ? 'selected' : '' }}>Passenger</option>
        <option value="consultant" {{ $user->role == 'consultant' ? 'selected' : '' }}>Staff</option>
        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
    <!-- Hidden role field to ensure it's submitted -->
    @if(auth()->user()->hasRole('consultant'))
        <input type="hidden" name="role" value="{{ $user->role }}">
    @endif
</div>


            <!-- Status -->
            <div class="form-group mb-3">
                <label for="status">Status</label>
                <select id="status" class="form-control" name="status" required>
                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspend" {{ $user->status == 'suspend' ? 'selected' : '' }}>Suspend</option>
                </select>
            </div>

            <!-- Submit and Back Buttons -->
            <div class="mt-3">
                <button type="submit" class="btn btn-primary" id="update-user-btn">Update User</button>
                <button class="btn btn-primary" type="button" id="loading-spinner" disabled style="display: none;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Updating...
                </button>
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('update-user-form').addEventListener('submit', function() {
        document.getElementById('update-user-btn').style.display = 'none';
        document.getElementById('loading-spinner').style.display = 'inline-block';
    });
</script>
@endpush
