@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)
@section('content')
    <h1>Payment Failed</h1>
    <p>Unfortunately, your payment could not be processed. Please try again later.</p>
@endsection
