@extends('layouts.signup-layout')

@section('title', 'Complete Your Profile | SAP Transportation and Logistics')

@section('content')
<div class="container mt-5">
    <h2>Complete Your Profile</h2>

    <!-- Display validation error messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="saveForm" method="POST" action="{{ route('complete.profile') }}">
        @csrf

        <!-- Phone Number Field -->
        <div class="form-group mt-3">
            <label for="phone">Phone Number</label>
            <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone ?? '') }}"  placeholder="Enter phone number"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" required>
            @error('phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>


        <!-- Gender Field -->
        <div class="form-group mt-3">
            <label for="gender">Gender</label>
            <select id="gender" class="form-control @error('gender') is-invalid @enderror" name="gender" required>
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3" id="saveBtn">Save Profile</button>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const signupForm = document.getElementById('saveForm');
        const signupBtn = document.getElementById('saveBtn');
        
        // Listen for form submission
        signupForm.addEventListener('submit', function(event) {
            // Show spinner and disable button immediately
            signupBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Signing up...";
            signupBtn.disabled = true;

            // Allow the form to submit naturally to the backend
        });
    });
</script>
@endsection
