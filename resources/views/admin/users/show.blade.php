@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">User Details</h1>

<div class="row mb-4">
<!-- User Details Column -->
<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $userDetails->name }}'s Details</h5>
            
            <!-- Table to display user details in two columns -->
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th class="w-50"><strong>Name:</strong></th>
                        <td>{{ $userDetails->name }}</td>
                    </tr>
                    <tr>
                        <th><strong>Email:</strong></th>
                        <td>{{ $userDetails->email }}</td>
                    </tr>
                    <tr>
                        <th><strong>Email Verified At:</strong></th>
                        <td>{{ $userDetails->email_verified_at ?? 'Not Verified' }}</td>
                    </tr>
                    <tr>
                        <th><strong>Phone:</strong></th>
                        <td>{{ $userDetails->phone }}</td>
                    </tr>
                    <tr>
                        <th><strong>Gender:</strong></th>
                        <td>{{ ucfirst($userDetails->gender) }}</td>
                    </tr>
                    <tr>
                    <th><strong>Role:</strong></th>
                    <td>
                        @if($userDetails->roles->isNotEmpty())
                            {{ $userDetails->roles->pluck('name')->implode(', ') }}
                        @else
                            No role assigned
                        @endif
                    </td>
                </tr>

                    <tr>
                        <th><strong>Status:</strong></th>
                        <td>
                            @if($userDetails->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($userDetails->status === 'inactive')
                                <span class="badge bg-warning">Inactive</span>
                            @else
                                <span class="badge bg-danger">Suspended</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                <th><strong>Created By:</strong></th>
                <td>
                    @if($userDetails->creatorByEmail)
                        {{ $userDetails->creatorByEmail->name }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>

                    <tr>
                        <th><strong>Created At:</strong></th>
                        <td>{{ $userDetails->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th><strong>Updated At:</strong></th>
                        <td>{{ $userDetails->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Buttons -->
            <div class="mt-3 d-flex justify-content-start">
                <a href="{{ route('admin.users.edit', $userDetails->id) }}" class="btn btn-primary me-2">Edit</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Close</a>
            </div>
        </div>
    </div>
</div>


      <!-- Transaction Activities Column -->
<!-- Transaction Activities Column -->
<div class="col-md-12 mt-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Transaction Activities</h5>
            <table id="transaction-activities-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Booking Ref</th>
                        <th>Booking Date</th>
                        <th>Booking Status</th>
                        <th>Invoice Number</th>
                        <th>Invoice Amount</th>
                        <th>Invoice Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($userBookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_reference }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <span class="badge 
                                    {{ $booking->booking_status == 'pending' ? 'bg-warning' : 
                                       ($booking->booking_status == 'expired' ? 'bg-secondary' : 
                                       ($booking->booking_status == 'completed' ? 'bg-success' : 
                                       ($booking->booking_status == 'cancelled' ? 'bg-danger' : 
                                       ($booking->booking_status == 'confirmed' ? 'bg-info' : '')))) }}">
                                    {{ ucfirst($booking->booking_status) }}
                                </span>
                            </td>
                            <td>{{ $booking->invoice_number ?? 'N/A' }}</td>
                            <td>{{ $booking->invoice_amount ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    {{ $booking->invoice_status == 'Unpaid' ? 'bg-danger' : 
                                       ($booking->invoice_status == 'Paid' ? 'bg-success' : 'bg-secondary') }}">
                                    {{ ucfirst($booking->invoice_status) ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>


    <!-- User Activity Logs Column -->
    <div class="col-md-12 mt-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Activity Logs</h5>
                <table id="activity-logs-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userActivities as $activity)
                            <tr>
                                <td>{{ $activity->action }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->ip_address }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d H:i:s') }}</td>
                                <td>{{ \Carbon\Carbon::parse($activity->updated_at)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection



@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
    <!-- Include jQuery, Bootstrap, DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#activity-logs-table').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            order: [[ 3, "desc" ]],
        });

        // Initialize Transaction Activities DataTable
        $('#transaction-activities-table').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            order: [[ 1, "desc" ]],
        });
    });
</script>
@endpush
