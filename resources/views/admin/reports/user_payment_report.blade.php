@extends('admin.layouts.admin-layout')

@section('content')
<h1 class="app-page-title">Generate User Payment Report</h1>

<!-- Error Message -->
@if (session('error'))
    <div id="error-message" class="alert alert-danger alert-dismissible fade show" role="alert">
        <span id="error-message-text">{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Success Message -->
<div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
    <span id="success-message-text">PDF generated successfully!</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<div class="app-card app-card-details shadow-sm mb-4">
    <div class="app-card-body p-4">
        <!-- Form for Report Generation -->
        <form id="generate-user-payment-report-form" method="POST" action="{{ route('admin.reports.user-payment.pdf') }}">
            @csrf

            <!-- Date Range Filter -->
            <div class="form-group mb-3">
                <label for="date-from">From Date</label>
                <input type="date" id="date-from" name="date_from" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="date-to">To Date</label>
                <input type="date" id="date-to" name="date_to" class="form-control" required>
            </div>

            <!-- Button and Spinner for PDF Generation -->
            <button type="submit" class="btn btn-primary mt-3" id="generate-pdf-btn">
                Generate PDF
            </button>
            <button class="btn btn-primary mt-3" type="button" id="generate-pdf-spinner" disabled style="display: none;">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Generating PDF...
            </button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Handle the PDF generation form submission
    document.getElementById('generate-user-payment-report-form').addEventListener('submit', function(event) {
        console.log('Form submitted'); // This will tell you if the form is triggered

    event.preventDefault(); // Prevent default form submission

    // Hide any previous messages
    document.getElementById('error-message').style.display = 'none';
    document.getElementById('success-message').style.display = 'none';

    // Show the spinner and hide the button
    document.getElementById('generate-pdf-btn').style.display = 'none';
    document.getElementById('generate-pdf-spinner').style.display = 'inline-block';


    

    // Send the request via AJAX
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        console.log('{{ csrf_token() }}');

        body: new FormData(this),
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => { throw new Error(data.error || 'An error occurred'); });
        }

        return response.blob().then(blob => {
            document.getElementById('generate-pdf-spinner').style.display = 'none';
            document.getElementById('generate-pdf-btn').style.display = 'inline-block';

            document.getElementById('success-message').style.display = 'block';
            setTimeout(() => {
                document.getElementById('success-message').style.display = 'none';
            }, 5000);

            const downloadLink = document.createElement('a');
                downloadLink.href = URL.createObjectURL(blob);
                downloadLink.download = filename;
                downloadLink.click();
        });
    })
    .catch(error => {
        document.getElementById('error-message-text').innerText = error.message;
        document.getElementById('error-message').style.display = 'block';
        setTimeout(() => {
            document.getElementById('error-message').style.display = 'none';
        }, 5000);

        document.getElementById('generate-pdf-spinner').style.display = 'none';
        document.getElementById('generate-pdf-btn').style.display = 'inline-block';
    });
});

</script>
@endpush
