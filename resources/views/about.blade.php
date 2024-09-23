<!-- resources/views/about.blade.php -->
@extends('layouts.main-layout')

@section('title', 'About-us | SAP Transportation and Logistics')

@section('content')
    <!-- Include your specific page content here -->
    @include('partials.about.about-banner')
    @include('partials.about.about-info')
    @include('partials.about.services')
@endsection
