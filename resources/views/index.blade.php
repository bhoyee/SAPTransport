<!-- resources/views/index.blade.php -->
@extends('layouts.main-layout')

@section('title', 'Home | SAP Transportation and Logistics')

@section('content')
    <!-- Include your specific page content here -->
    @include('partials.home.hero')
    @include('partials.home.services')
    @include('partials.home.about-us')
    @include('partials.home.carousel')
@endsection
