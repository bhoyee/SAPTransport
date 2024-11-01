<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    //
    public function getEvents(Request $request)
{
    $events = [
        [
            'title' => 'All Day Event',
            'start' => '2024-10-01'
        ],
        [
            'title' => 'Long Event',
            'start' => '2024-10-07',
            'end' => '2024-10-10'
        ],
        [
            'title' => 'Conference',
            'start' => '2024-10-30',
            'end' => '2024-10-31'
        ],
        [
            'title' => 'Birthday Party',
            'start' => '2024-10-31T07:00:00'
        ],
    ];

    return response()->json($events);
}

}
