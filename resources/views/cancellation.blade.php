<!-- resources/views/about.blade.php -->
@extends('layouts.main-layout')

@section('title', 'Cancellation Policy | SAP Transportation and Logistics')

@section('content')
    <!-- Include your specific page content here -->
    @include('partials.cancellation.cancellation-banner')
    @include('partials.cancellation.cancellation-info')
   
@endsection
