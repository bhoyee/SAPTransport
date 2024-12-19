<?php

namespace App\Http\Controllers;

use App\Conversations\BookingConversation;
use BotMan\BotMan\BotMan;

class BotManController extends Controller
{

    public function chatFrame()
    {
        return view('botman.chat'); // Ensure this is the chat.blade.php file
    }
    
    public function handle()
    {
        $botman = app('botman');

        // Start the BookingConversation for any input
        $botman->hears('.*', function (BotMan $bot) {
            $bot->startConversation(new BookingConversation());
        });

        // Fallback to redirect to the main menu for unmatched inputs
        $botman->fallback(function (BotMan $bot) {
            $bot->startConversation(new BookingConversation());
        });

        $botman->listen();
    }
}
