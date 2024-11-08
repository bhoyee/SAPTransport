@php
    $layout = auth()->user()->hasRole('admin') 
        ? 'admin.layouts.admin-layout' 
        : 'staff.layouts.staff-layout';
@endphp

@extends($layout)

@section('content')
    <h2>Admin Settings</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Setting Key</th>
                <th>Setting Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settings as $setting)
                <tr>
                    <td>{{ $setting->key }}</td>
                    <td>
                        <form action="{{ route('admin.settings.update', $setting->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            @if($setting->key === 'booking_status')
                                <select name="value" class="form-control">
                                    <option value="open" {{ $setting->value === 'open' ? 'selected' : '' }}>Enable Bookings</option>
                                    <option value="closed" {{ $setting->value === 'closed' ? 'selected' : '' }}>Disable Bookings</option>
                                </select>
                            @else
                                <input type="text" name="value" value="{{ $setting->value }}" class="form-control">
                            @endif
                            
                            <button type="submit" class="btn btn-primary mt-2">Update</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
