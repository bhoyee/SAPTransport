<?php

// app/Jobs/SendMonthlyReportEmail.php
namespace App\Jobs;

use App\Mail\MonthlyReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReportEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function handle()
    {
        $adminEmail = config('mail.admin_email'); // Fetch the admin email from the config
    
        Mail::to($adminEmail)->send(new MonthlyReport($this->reportData));
    }
    
}
