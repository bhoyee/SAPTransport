@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('title', 'View Message')

@section('content')
<div class="container mt-5">
    
    <h1 class="app-page-title">Message Details</h1>
<br>
<a href="{{ route('admin.manage-messages') }}" class="btn btn-secondary mb-3">Back to Manage Messages</a> <!-- Back button -->


    <div class="card">
        <div class="card-body">
            <h5><strong>Subject:</strong> {{ $message->subject }}</h5>
            <p><strong>Message:</strong></p>
            <div>{!! $message->message !!}</div> <!-- Maintain HTML format -->

            <hr>
            <h6><strong>Sender Details:</strong></h6>
            <p><strong>Name:</strong> {{ $sender->name }}</p>
            <p><strong>Email:</strong> {{ $sender->email }}</p>
            <p><strong>Sent At:</strong> {{ $message->created_at->format('Y-m-d H:i:s') }}</p>

            <hr>
            <h6><strong>Recipient Details:</strong></h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $receiver->name }}</td>
                        <td>{{ $receiver->email }}</td>
                        <td>{{ ucfirst($message->status) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
