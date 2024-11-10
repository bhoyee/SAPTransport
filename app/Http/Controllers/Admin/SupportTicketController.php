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
use App\Models\Notification;


class SupportTicketController extends Controller
{

        // Display the support tickets page
        public function index()
        {
            return view('admin.support-tickets.index'); // No need to pass tickets here
        }
    
        // Fetch data for DataTable
        public function getTicketsData(Request $request)
        {
            Log::info('getTicketsData called');
        
            try {
                $query = Contact::query();
        
                // Server-side pagination and sorting parameters from DataTables
                $totalRecords = $query->count();
                $filteredRecords = $totalRecords;
        
                // Search filter if provided
                if ($search = $request->input('search.value')) {
                    $query->where('department', 'like', "%{$search}%")
                          ->orWhere('subject', 'like', "%{$search}%")
                          ->orWhere('ticket_num', 'like', "%{$search}%");
                    $filteredRecords = $query->count();
                }
        
                // Sorting and pagination
                $start = $request->input('start', 0);
                $length = $request->input('length', 10);
                $query->skip($start)->take($length);
        
                // Execute the query
                $tickets = $query->orderBy('updated_at', 'desc')->get();
        
                // Transform data for DataTable format
                $data = $tickets->map(function ($ticket, $index) use ($start) {
                    return [
                        'id' => $start + $index + 1,
                        'department' => ucfirst($ticket->department),
                        'subject' => "<strong>#{$ticket->ticket_num}</strong><br><p>" . ($ticket->subject ?: ucfirst($ticket->category)) . "</p>",
                        'status' => "<span class='badge " . ($ticket->status == 'open' ? 'bg-danger' : 'bg-success') . "'>" . ucfirst($ticket->status) . "</span>",
                        'updated_at' => $ticket->updated_at->format('d M Y H:i'),
                        'actions' => '<a href="'.route('admin.support-tickets.view', $ticket->id).'" class="btn btn-primary btn-sm">View</a>
                                      <form action="'.route('admin.support-tickets.delete', $ticket->id).'" method="POST" class="d-inline-block">
                                          '.csrf_field().method_field('DELETE').'
                                          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this ticket?\')">Delete</button>
                                      </form>'
                    ];
                });
        
                return response()->json([
                    'draw' => $request->input('draw'),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $filteredRecords,
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to fetch support tickets: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to load support tickets.'], 500);
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
    
        $request->validate([
            'message' => 'required|string',
        ]);
    
        try {
            // Retrieve the ticket to check if the current user is the owner (passenger)
            $ticket = Contact::findOrFail($id);
    
            // Determine the role accurately: "owner" if the current user is the ticket creator
            if (Auth::id() === $ticket->user_id) {
                $userRole = 'owner';
            } elseif (Auth::user()->hasRole('admin')) {
                $userRole = 'admin';
            } else {
                $userRole = 'consultant';
            }
            
            Log::info('Assigned role for reply', [
                'user_id' => Auth::id(),
                'ticket_owner_id' => $ticket->user_id,
                'assigned_role' => $userRole
            ]);
    
            // Create the reply
            $reply = new TicketReply();
            $reply->ticket_id = $ticket->id;
            $reply->user_id = Auth::id();
            $reply->message = $request->input('message');
            $reply->save();
    
            // Update the ticket's last updated timestamp
            $ticket->touch();
    
            // Send notification if needed
            Mail::to($ticket->email_address)->send(new TicketReplyNotification($ticket, $reply));
    
            // Log the activity
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
            
    
            // Return the correct role in response
            return response()->json([
                'id' => $reply->id,
                'message' => $reply->message,
                'user_name' => Auth::user()->name,
                'user_role' => $userRole, // Should now be "owner", "admin", or "consultant" as applicable
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
    
    

    // SupportTicketController.php


    public function fetchNewReplies($id, Request $request)
    {
        $lastReplyTimestamp = $request->input('lastReplyTimestamp');
    
        try {
            // Fetch the ticket to identify the owner
            $ticket = Contact::findOrFail($id);
            $ticketOwnerId = $ticket->user_id; // Store the owner ID
    
            // Fetch replies made after the last timestamp
            $newReplies = TicketReply::with('user')
                ->where('ticket_id', $id)
                ->where('created_at', '>', $lastReplyTimestamp)
                ->get()
                ->map(function ($reply) use ($ticketOwnerId) {
                    // Check if reply's user_id matches the ticket owner, otherwise assign based on role
                    if ($reply->user_id === $ticketOwnerId) {
                        $userRole = 'owner'; // The ticket owner is typically the passenger
                    } elseif ($reply->user && $reply->user->hasRole('admin')) {
                        $userRole = 'admin';
                    } elseif ($reply->user && $reply->user->hasRole('consultant')) {
                        $userRole = 'consultant';
                    } else {
                        $userRole = 'guest'; // Fallback role if user role is unclear
                    }
    
                    // Log the role determined for each reply to help with debugging
                    Log::info('Reply Role Determination', [
                        'reply_id' => $reply->id,
                        'reply_user_id' => $reply->user_id,
                        'ticket_owner_id' => $ticketOwnerId,
                        'determined_role' => $userRole,
                        'user_name' => $reply->user->name ?? 'Unknown User'
                    ]);
    
                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'user_name' => $reply->user->name ?? 'Unknown User',
                        'user_role' => $userRole,
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
    
    


    // public function fetchNewReplies($id, Request $request)
    // {
    //     $lastReplyTimestamp = $request->input('lastReplyTimestamp');

    //     try {
    //         // Fetch replies created after the last timestamp
    //         $newReplies = TicketReply::with('user')
    //             ->where('ticket_id', $id)
    //             ->where('created_at', '>', $lastReplyTimestamp)
    //             ->get()
    //             ->map(function ($reply) {
    //                 return [
    //                     'id' => $reply->id,
    //                     'message' => $reply->message,
    //                     'user_name' => $reply->user->name ?? 'Unknown User',
    //                     'user_role' => $reply->user->hasRole('admin') ? 'operator' : 'owner',
    //                     'created_at' => $reply->created_at->format('d M Y H:i'),
    //                     'timestamp' => $reply->created_at->format('Y-m-d H:i:s'),
    //                 ];
    //             });

    //         return response()->json(['newReplies' => $newReplies], 200);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to fetch new replies: ' . $e->getMessage(), ['ticket_id' => $id]);
    //         return response()->json(['error' => 'Failed to fetch new replies.'], 500);
    //     }
    // }

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
