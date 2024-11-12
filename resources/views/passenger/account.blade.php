{{-- resources/views/account/settings.blade.php --}}

@php
    if (auth()->user()->hasRole('admin')) {
        $layout = 'admin.layouts.admin-layout';
    } elseif (auth()->user()->hasRole('consultant')) {
        $layout = 'staff.layouts.staff-layout';
    } else {
        $layout = 'layouts.passenger';
    }
@endphp

@extends($layout)

@section('title', 'Account Settings')

@section('content')
<h1 class="app-page-title">Account Settings</h1>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">Update Profile</div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data" onsubmit="return showSpinner()">
                @csrf

                <!-- Full Name and Email (Read-Only) -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="mb-3">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <!-- Profile Image Upload -->
                <div class="mb-3">
                    <label for="profile_image">Profile Image (Optional)</label>
                    <input type="file" name="profile_image" class="form-control" id="profile_image">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/profile_images/' . $user->profile_image) }}" alt="Profile Image" class="img-thumbnail mt-2" width="150">
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="mb-3">
                    <button type="submit" id="submitButton" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showSpinner() {
        const submitButton = document.getElementById('submitButton');
        submitButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Updating...";
        submitButton.disabled = true;
        return true;
    }
</script>
@endpush
