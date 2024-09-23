@extends('layouts.passenger')

@section('title', 'Passenger Dashboard | SAP Transportation and Logistics')

@section('content')
<div class="container">
    <h1 class="mb-4">Passenger Dashboard</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Upcoming Trips</h5>
                    <p class="card-text">View and manage your upcoming trips.</p>
                    <a href="#" class="btn btn-primary">View Trips</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Profile Settings</h5>
                    <p class="card-text">Update your personal information and preferences.</p>
                    <a href="#" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
