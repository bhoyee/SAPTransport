<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\TicketReply; // Import TicketReply model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Services\ActivityLogger;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactConfirmation;
use App\Mail\AdminNotification;
class ContactController extends Controller
{
    // Display ticket creation form
    public function createTicketForm()
    {
        $user = Auth::user(); // Fetch logged-in user's details
        return view('passenger.create-ticket', compact('user'));
    }

    // Store the newly created ticket
    public function storeTicket(Request $request)
    {
        // Validate form inputs
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255', // Added subject validation
            'department' => 'required|in:support,sales,billing',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
            'attachment' => 'nullable|mimes:docx,doc,pdf,jpg,jpeg,png|max:2048' // File validation
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
            'fullname' => Auth::user()->name, // Automatically get user's name
            'email_address' => Auth::user()->email, // Automatically get user's email
            'subject' => $request->input('subject'), // Store the subject
            'department' => $request->input('department'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'), // HTML content from Quill
            'attachment' => $fileName,
            'status' => 'open', // New tickets are marked as 'open' by default
        ]);

        return redirect()->back()->with('success', "Ticket created successfully! Ticket Number: {$ticket->ticket_num}");
    }

    // Display the logged-in user's tickets
    public function myTickets()
    {
        $user = Auth::user();
        $tickets = Contact::where('email_address', $user->email)
                          ->orderBy('updated_at', 'desc')
                          ->get();

        return view('passenger.my-tickets', compact('tickets'));
    }

    // View a specific ticket
    public function viewTicket($id)
    {
        $ticket = Contact::findOrFail($id);

        // Check if the user is authorized to view this ticket
        if ($ticket->email_address !== Auth::user()->email) {
            return redirect()->route('passenger.my-tickets')->with('error', 'Unauthorized access to this ticket.');
        }

        $replies = TicketReply::with('user')->where('ticket_id', $id)->get();  // Load replies with user info

        return view('passenger.view-ticket', compact('ticket', 'replies'));
    }

    // Reply to a ticket
    public function replyToTicket(Request $request, $id)
    {
        // Validate the reply
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $ticket = Contact::findOrFail($id);

        // Check if the user is authorized to reply
        if ($ticket->email_address !== Auth::user()->email) {
            return redirect()->route('passenger.my-tickets')->with('error', 'Unauthorized action.');
        }

        // Create a new reply
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->input('message'), // Store the reply as HTML
        ]);

        // Mark ticket as 'open' again after a reply
        $ticket->update(['status' => 'open']);

        return redirect()->route('viewTicket', $id)->with('success', 'Your reply has been added.');
    }

    // contact form 

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
            ]);

            // Send email to user
            Mail::to($contact->email_address)->send(new ContactConfirmation($contact));

            // Send email to admin
            Mail::to(config('mail.admin_email'))->send(new AdminNotification($contact));

            // Notify users with admin and consultant roles
            $adminConsultantUsers = User::whereIn('role', ['admin', 'consultant'])->get();

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
    
    
    
}
