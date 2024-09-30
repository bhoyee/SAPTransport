@extends('layouts.passenger')

@section('title', 'Settings')

@section('content')
<h1 class="app-page-title">Settings</h1>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">Change Password</div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
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

            <form action="{{ route('passenger.change-password') }}" method="POST" onsubmit="return showSpinner()">
                @csrf

                <!-- New Password -->
                <div class="mb-3">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" class="form-control" id="new_password" required>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="new_password_confirmation">Confirm Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation" required>
                </div>

                <!-- Submit Button -->
                <div class="mb-3">
                    <button type="submit" id="submitButton" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add spinner to button when form is submitted
    function showSpinner() {
        const submitButton = document.getElementById('submitButton');
        submitButton.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Changing...";
        submitButton.disabled = true;
        return true;
    }
</script>
@endpush
