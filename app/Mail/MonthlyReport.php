<?php

// app/Mail/MonthlyReport.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyReport extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function build()
    {
        return $this->view('emails.monthly_report')
                    ->subject('Monthly System Report')
                    ->with('reportData', $this->reportData);
    }
}
