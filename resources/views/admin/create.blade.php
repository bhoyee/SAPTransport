@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">Add Users</h1>
<p>Passengers, Staffs  or addtional system admin</p> 

<div class="app-card app-card-details shadow-sm mb-4">
<div class="app-card-body p-4 gx-5 gy-3">
      

    <!-- Display Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Display Error Message -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Please fix the following errors:
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- User Creation Form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Create User
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}" id="create-user-form">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">Full Name</label>
                            <input id="name" type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password-confirm">Confirm Password</label>
                            <input id="password-confirm" type="password" 
                                   class="form-control" 
                                   name="password_confirmation" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="role">Role</label>
                            <select id="role" 
                                    class="form-control @error('role') is-invalid @enderror" 
                                    name="role" required>
                                <option value="passenger" {{ old('role') == 'passenger' ? 'selected' : '' }}>Passenger</option>
                                <option value="consultant" {{ old('role') == 'consultant' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="gender">Gender</label>
                            <select id="gender" 
                                    class="form-control @error('gender') is-invalid @enderror" 
                                    name="gender" required>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Submit Button and Spinner -->
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary" id="create-user-btn">
                                Create User
                            </button>
                            <button class="btn btn-primary" type="button" id="loading-spinner" disabled style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Creating...
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('create-user-form').addEventListener('submit', function() {
        // Hide the submit button
        document.getElementById('create-user-btn').style.display = 'none';
        // Show the loading spinner
        document.getElementById('loading-spinner').style.display = 'inline-block';
    });
</script>
@endpush