<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\ActivityLogger;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmation;
use App\Mail\AdminNotification;
use Illuminate\Support\Facades\Log;


class ContactController extends Controller
{
    // Display ticket creation form
    public function storeTicket(Request $request)
    {
        // Validate form inputs
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'department' => 'required|in:support,sales,billing',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
            'attachment' => 'nullable|mimes:docx,doc,pdf,jpg,jpeg,png|max:2048',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        // Generate a unique 6-digit ticket number
        $ticketNumber = strtoupper(Str::random(6));
    
        // Handle file upload
        $fileName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/attachments'), $fileName);
        }
    
        // Create the ticket
        $ticket = Contact::create([
            'ticket_num' => $ticketNumber,
            'fullname' => Auth::user()->name,
            'email_address' => Auth::user()->email,
            'subject' => $request->input('subject'),
            'department' => $request->input('department'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'attachment' => $fileName,
            'status' => 'open',
            'phone_number' => $request->input('phone'),
        ]);
    
        // Log the ticket creation
        Log::info('New support ticket created', [
            'ticket_id' => $ticket->id,
            'ticket_num' => $ticketNumber,
            'user_id' => Auth::id(),
            'subject' => $ticket->subject,
        ]);
    
        // Record the activity in the activity log
        ActivityLogger::log(
            'Ticket Created',
            'Created a new support ticket #' . $ticket->ticket_num,
            Auth::id()
        );
    
        // Send push notifications to all admin and consultant users
        $adminConsultantUsers = User::role(['admin', 'consultant'])->get(); // Assuming Spatie's roles are used
        foreach ($adminConsultantUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => 'A new support ticket has been created by ' . Auth::user()->name . ' with ticket number ' . $ticket->ticket_num,
                'type' => 'message',
                'status' => 'unread',
                'related_user_name' => Auth::user()->name,
            ]);
        }
    
        return redirect()->back()->with('success', "Ticket created successfully! Ticket Number: {$ticket->ticket_num}");
    }
    

    // Display the logged-in user's tickets
    public function fetchTickets()
    {
        $user = Auth::user();
        $tickets = Contact::where('email_address', $user->email)
                          ->orderBy('updated_at', 'desc')
                          ->get();
    
        return response()->json(['tickets' => $tickets]);
    }

    
    

    // View a specific ticket
    // public function viewTicket($id)
    // {
    //     $ticket = Contact::findOrFail($id);

    //     // Check if the user is authorized to view this ticket
    //     if ($ticket->email_address !== Auth::user()->email) {
    //         return redirect()->route('passenger.my-tickets')->with('error', 'Unauthorized access to this ticket.');
    //     }

    //     $replies = TicketReply::with('user')->where('ticket_id', $id)->get();

    //     return view('passenger.view-ticket', compact('ticket', 'replies'));
    // }
    public function viewTicket($id)
{
    if (!Auth::check()) {
        Log::warning('Unauthenticated user attempting to access a ticket.', ['ticket_id' => $id]);
        return redirect()->route('login')->with('error', 'You must log in to view this ticket.');
    }

    try {
        $ticket = Contact::findOrFail($id);

        if ($ticket->email_address !== Auth::user()->email) {
            Log::warning('Unauthorized ticket access.', [
                'ticket_id' => $id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'ticket_email' => $ticket->email_address
            ]);
            return redirect()->route('passenger.my-tickets')->with('error', 'Unauthorized access.');
        }

        $replies = TicketReply::with('user')->where('ticket_id', $id)->get();

        return view('passenger.view-ticket', compact('ticket', 'replies'));

    } catch (\Exception $e) {
        Log::error('Failed to view ticket.', [
            'ticket_id' => $id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);
        return redirect()->route('passenger.my-tickets')->with('error', 'Failed to retrieve ticket.');
    }
}


     // Display the logged-in user's tickets
     public function myTickets()
     {
         // Get the authenticated user
         $user = Auth::user();
 
         // Fetch the user's tickets ordered by the most recent 'updated_at'
         $tickets = Contact::where('email_address', $user->email)
                           ->orderBy('updated_at', 'desc')
                           ->get();
 
         // Return the view with the tickets data
         return view('passenger.my-tickets', compact('tickets'));
     }

    // Reply to a ticket

    public function replyToTicket(Request $request, $id)
    {
        try {
            $ticket = Contact::findOrFail($id);
    
            // Check if the user is authorized to reply
            if ($ticket->email_address !== Auth::user()->email) {
                Log::warning('Unauthorized attempt to reply to ticket.', ['ticket_id' => $id, 'user_id' => Auth::id()]);
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
    
            $message = $ticket->status == 'closed'
                ? Auth::user()->name . ' wants to reopen the ticket.'
                : $request->input('message');
    
            // If the ticket is closed, reopen it
            if ($ticket->status == 'closed') {
                $ticket->update(['status' => 'open']);
                Log::info('Ticket reopened.', ['ticket_id' => $id]);
            }
    
            // Create a new reply
            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => $message,
            ]);
    
            Log::info('Reply added to ticket.', ['ticket_id' => $id, 'reply_id' => $reply->id, 'user_id' => Auth::id()]);
    
            // Record the activity in the activity log
            ActivityLogger::log('Ticket Reply', 'User replied to ticket #' . $ticket->ticket_num);
    
            // Determine the role for frontend display
            $userRole = Auth::user()->hasRole('admin') ? 'admin' : (Auth::user()->hasRole('consultant') ? 'consultant' : 'owner');
    
            // Notify admin and consultants
            $adminConsultantUsers = User::role(['admin', 'consultant'])->get(); // Assuming using Spatie's role system
            foreach ($adminConsultantUsers as $adminConsultant) {
                Notification::create([
                    'user_id' => $adminConsultant->id,
                    'message' => 'A new reply has been posted by ' . Auth::user()->name . ' on ticket #' . $ticket->ticket_num,
                    'type' => 'message',
                    'status' => 'unread',
                    'related_user_name' => Auth::user()->name,
                ]);
            }
    
            // Return the reply data with the user role
            return response()->json([
                'message' => $message,
                'id' => $reply->id,
                'user_role' => $userRole,
                'user_name' => Auth::user()->name,
                'created_at' => now()->format('d M Y H:i'),
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Failed to reply to ticket.', ['ticket_id' => $id, 'user_id' => Auth::id(), 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to send reply.'], 500);
        }
    }
    
    public function createTicketForm()
{
    $user = Auth::user(); // Fetch the logged-in user's information

    return view('passenger.create-ticket', compact('user')); // Adjust the view path if necessary
}

    
    // Handle contact form submission
    public function submit(Request $request)
    {
        $request->validate([
            'categories' => 'required',
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        try {
            // Generate a unique ticket number
            $ticketNum = strtoupper(Str::random(6));

            // Create a contact record
            $contact = Contact::create([
                'category' => $request->categories,
                'fullname' => $request->fullname,
                'phone_number' => $request->phone,
                'email_address' => $request->email,
                'message' => $request->message,
                'ticket_num' => $ticketNum,
                'status' => 'open',
                'priority' => 'low',
                'department' => 'support',
            ]);

            // Send email to user
            Mail::to($contact->email_address)->send(new ContactConfirmation($contact));

            // Send email to admin
            Mail::to(config('mail.admin_email'))->send(new AdminNotification($contact));

            // Notify users with 'admin' or 'consultant' roles
            $adminConsultantUsers = User::role(['admin', 'consultant'])->get(); // Using Spatie's role system

            foreach ($adminConsultantUsers as $adminConsultant) {
                Notification::create([
                    'user_id' => $adminConsultant->id,
                    'message' => 'A new contact form submission from ' . $contact->fullname . ' with ticket number ' . $contact->ticket_num,
                    'type' => 'message',
                    'status' => 'unread',
                    'related_user_name' => $contact->fullname,
                ]);
            }

            // Redirect back with success message
            return redirect()->back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to send message. Please try again.');
        }
    }


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
                    // Determine the role for each reply
                    $userRole = $reply->user_id === $ticketOwnerId
                        ? 'owner'
                        : ($reply->user && $reply->user->hasRole('admin') ? 'admin' : 'consultant');
    
                    // Log role determination for debugging
                    Log::info('Reply Role Determination for Passenger Page', [
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
            Log::error('Failed to fetch new replies for passenger page: ' . $e->getMessage(), ['ticket_id' => $id]);
            return response()->json(['error' => 'Failed to fetch new replies.'], 500);
        }
    }
    


}
