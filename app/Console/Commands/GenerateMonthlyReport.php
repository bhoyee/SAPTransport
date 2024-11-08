<?php

// app/Console/Commands/GenerateMonthlyReport.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReportService;
use App\Jobs\SendMonthlyReportEmail;

class GenerateMonthlyReport extends Command
{
    protected $signature = 'report:monthly';
    protected $description = 'Generate a monthly system report and send it to the admin email';

    public function handle()
    {
        $reportData = app(ReportService::class)->generateStatistics();
        dispatch(new SendMonthlyReportEmail($reportData));
        
        $this->info('Monthly report generated and email queued.');
    }
}
