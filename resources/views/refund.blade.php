<!-- resources/views/about.blade.php -->
@extends('layouts.main-layout')

@section('title', 'Refund Policy | SAP Transportation and Logistics')

@section('content')
    <!-- Include your specific page content here -->
    @include('partials.refund.refund-banner')
    @include('partials.refund.refund-info')
   
@endsection
