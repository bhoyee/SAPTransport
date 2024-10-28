<?php

// app/Http/Controllers/Admin/SupportTicketController.php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\TicketReplyNotification;
use Illuminate\Support\Facades\Mail;
use App\Services\ActivityLogger;

class SupportTicketController extends Controller
{
    // Display the list of support tickets
    public function index()
    {
        Log::info('Fetching support tickets for admin.');

        try {
            $tickets = Contact::orderBy('updated_at', 'desc')->paginate(10); // Fetch support tickets
            Log::info('Support tickets fetched successfully.', ['total_tickets' => $tickets->total()]);

            return view('admin.support-tickets.index', compact('tickets'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch support tickets: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to load support tickets.');
        }
    }

    // View a specific support ticket
    public function view($id)
    {
        Log::info('Viewing support ticket.', ['ticket_id' => $id]);

        try {
            $ticket = Contact::with('replies')->findOrFail($id); // Load the ticket with its replies
            Log::info('Support ticket found.', ['ticket_id' => $id]);

            return view('admin.support-tickets.view', compact('ticket'));
        } catch (\Exception $e) {
            Log::error('Failed to view support ticket: ' . $e->getMessage(), ['ticket_id' => $id]);
            return redirect()->back()->withErrors('Failed to view support ticket.');
        }
    }

    // Delete a support ticket
    public function delete($id)
    {
        Log::info('Deleting support ticket.', ['ticket_id' => $id]);
    
        try {
            $ticket = Contact::findOrFail($id);
    
            // Delete all associated replies
            $ticket->replies()->delete();
    
            // Delete the ticket
            $ticket->delete();
    
            Log::info('Support ticket and associated replies deleted successfully.', ['ticket_id' => $id]);
    
            return redirect()->route('admin.support-tickets.index')->with('success', 'Support ticket and associated replies deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete support ticket: ' . $e->getMessage(), ['ticket_id' => $id]);
            return redirect()->back()->withErrors('Failed to delete support ticket.');
        }
    }

    public function reply(Request $request, $id)
    {
        Log::info('Attempting to reply to support ticket.', ['ticket_id' => $id]);
    
        // Validate the request input
        $request->validate([
            'message' => 'required|string',
        ]);
    
        try {
            // Find the support ticket
            $ticket = Contact::findOrFail($id);
            Log::info('Support ticket found for replying.', ['ticket_id' => $id]);
    
            // Create the reply
            $reply = new TicketReply();
            $reply->ticket_id = $ticket->id;
            $reply->user_id = Auth::id(); // The authenticated user (admin or consultant) replying to the ticket
            $reply->message = $request->input('message');
            $reply->save();
    
            // Update the ticket's `updated_at` timestamp
            $ticket->touch();
    
            // Send email notification to the user who initiated the ticket
            Mail::to($ticket->email_address)->send(new TicketReplyNotification($ticket, $reply));
    
            Log::info('Reply added to the support ticket successfully.', [
                'ticket_id' => $id,
                'reply_id' => $reply->id,
            ]);
    

                // Record the activity in the activity log
                ActivityLogger::log(
                    'Reply to Ticket',
                    'Replied to ticket #' . $ticket->ticket_num,
                    Auth::id()
                );
    
            // Get the user associated with the email address (if any)
            $user = \App\Models\User::where('email', $ticket->email_address)->first();
    
            // Notify the user who initiated the ticket if found
            if ($user) {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => 'A new reply has been posted by ' . Auth::user()->name . ' on your ticket #' . $ticket->ticket_num,
                    'type' => 'message',
                    'status' => 'unread',
                    'related_user_name' => Auth::user()->name,
                ]);
            }
    
            // Return a JSON response with the reply data
            return response()->json([
                'id' => $reply->id,
                'message' => $reply->message,
                'user_name' => Auth::user()->name,
                'user_role' => Auth::user()->hasRole('admin') ? 'operator' : 'owner',
                'created_at' => $reply->created_at->format('d M Y H:i'),
                'timestamp' => $reply->created_at->format('Y-m-d H:i:s'),
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Failed to reply to support ticket: ' . $e->getMessage(), ['ticket_id' => $id]);
    
            return response()->json([
                'error' => 'Failed to send reply.',
            ], 500);
        }
    }
    

    public function fetchNewReplies($id, Request $request)
    {
        $lastReplyTimestamp = $request->input('lastReplyTimestamp');

        try {
            // Fetch replies created after the last timestamp
            $newReplies = TicketReply::with('user')
                ->where('ticket_id', $id)
                ->where('created_at', '>', $lastReplyTimestamp)
                ->get()
                ->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'user_name' => $reply->user->name ?? 'Unknown User',
                        'user_role' => $reply->user->hasRole('admin') ? 'operator' : 'owner',
                        'created_at' => $reply->created_at->format('d M Y H:i'),
                        'timestamp' => $reply->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json(['newReplies' => $newReplies], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch new replies: ' . $e->getMessage(), ['ticket_id' => $id]);
            return response()->json(['error' => 'Failed to fetch new replies.'], 500);
        }
    }

    // app/Http/Controllers/Admin/SupportTicketController.php

    // Update the ticket status and add a reply if closed
    public function updateStatus(Request $request, $id)
    {
        Log::info('Updating ticket status.', ['ticket_id' => $id]);

        try {
            $ticket = Contact::findOrFail($id);
            $previousStatus = $ticket->status;
            $newStatus = $request->input('status');

            // Update the ticket status
            $ticket->status = $newStatus;
            $ticket->save();

            // Log the status update
            Log::info('Ticket status updated.', ['ticket_id' => $id, 'new_status' => $newStatus]);

            // If the ticket is closed, add a reply indicating the status change
            if ($newStatus === 'closed') {
                TicketReply::create([
                    'ticket_id' => $id,
                    'user_id' => auth()->id(),
                    'message' => 'This ticket has been closed now.',
                ]);

                Log::info('Reply added for ticket closure.', ['ticket_id' => $id]);
            }

            return redirect()->route('support-tickets.view', $id)
                            ->with('success', 'Ticket status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update ticket status: ' . $e->getMessage(), ['ticket_id' => $id]);
            return redirect()->back()->withErrors('Failed to update ticket status.');
        }
    }


    
}
