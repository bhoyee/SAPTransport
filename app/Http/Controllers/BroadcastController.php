<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BroadcastController extends Controller
{
    public function index()
    {
        return view('admin.broadcast');
    }


    public function sendBroadcast(Request $request)
    {
        // Log incoming request
        Log::info("Received broadcast request", $request->all());
    
        // Validate input
        try {
            $request->validate([
                'recipient_type' => 'required|string',
                'subject' => 'required|string|max:255',
                'message_body' => 'required|string',
                'individual_email' => 'nullable|email|required_if:recipient_type,individual',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error("Validation failed", ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation error: ' . implode(', ', $e->errors())], 400);
        }
    
        // Extract validated data
        $recipientType = $request->input('recipient_type');
        $subject = $request->input('subject');
        $messageBody = $this->processHtmlContent($request->input('message_body'));
        $individualEmail = $request->input('individual_email');
        $recipients = [];
    
        Log::info("Processing recipient type: {$recipientType}");
    
        // Determine recipients based on recipient type
        if ($recipientType === 'all') {
            $recipients = User::role(['passenger', 'consultant'])->pluck('id')->toArray();
            Log::info("Recipients fetched for 'all': ", ['count' => count($recipients)]);
        } elseif ($recipientType === 'passenger') {
            $recipients = User::role('passenger')->pluck('id')->toArray();
            Log::info("Recipients fetched for 'passenger': ", ['count' => count($recipients)]);
        } elseif ($recipientType === 'staff') {
            $recipients = User::role('consultant')->pluck('id')->toArray();
            Log::info("Recipients fetched for 'staff': ", ['count' => count($recipients)]);
        } elseif ($recipientType === 'individual' && $individualEmail) {
            $recipient = User::where('email', $individualEmail)->first();
            if ($recipient) {
                $recipients = [$recipient->id];
                Log::info("Recipient found for individual email: {$individualEmail}");
            } else {
                Log::warning("No user found with email: {$individualEmail}");
            }
        } else {
            Log::error("No recipients found for recipient type: {$recipientType}");
            return response()->json(['error' => 'No valid recipients found.'], 400);
        }
    
        if (empty($recipients)) {
            Log::error("No valid recipients found after processing.");
            return response()->json(['error' => "User with email {$individualEmail} does not exist."], 400);
        }
    
        try {
            // Create the message record
            $messageRecord = Message::create([
                'sender_id' => Auth::id(),
                'subject' => $subject,
                'message' => $messageBody,
                'status' => 'sent',
            ]);
    
            Log::info("Message record created", ['message_id' => $messageRecord->id]);
    
            // Attach recipients with status
            foreach ($recipients as $recipientId) {
                $messageRecord->recipients()->attach($recipientId, ['status' => 'sent']);
            }
    
            Log::info("Recipients attached to message", ['recipients_count' => count($recipients)]);
    
            // Send emails with tracking pixel
            foreach ($recipients as $recipientId) {
                $email = User::find($recipientId)->email;
    
                // Generate the tracking pixel URL with the message ID and recipient ID
                $trackingPixelUrl = route('email.tracking.pixel', [
                    'messageId' => $messageRecord->id,
                    'recipientId' => $recipientId,
                ]);
                $messageBodyWithPixel = $messageBody . "<img src='{$trackingPixelUrl}' alt='' width='1' height='1' style='display:none;'>";
    
                // Send the email
                try {
                    Mail::send([], [], function ($message) use ($email, $subject, $messageBodyWithPixel) {
                        $message->to($email)
                                ->subject($subject)
                                ->html($messageBodyWithPixel);
                    });
    
                    Log::info("Email sent successfully", ['recipient_email' => $email]);
                } catch (\Exception $e) {
                    Log::error("Failed to send email", ['recipient_email' => $email, 'error' => $e->getMessage()]);
                }
            }
    
            return response()->json(['success' => 'Broadcast message sent successfully!']);
        } catch (\Exception $e) {
            Log::error("Failed to send broadcast", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to send broadcast message.'], 500);
        }
    }
    

public function trackEmailOpen(Request $request)
{
    Log::info("Entering trackEmailOpen function");

    $messageId = $request->query('messageId');
    $recipientId = $request->query('recipientId'); // Capture the recipient ID from the tracking pixel request

    Log::info("Tracking pixel requested with messageId: {$messageId}, recipientId: {$recipientId}");

    // Find the message and update the recipient's status in the pivot table
    $message = Message::find($messageId);

    if ($message) {
        $recipient = $message->recipients()->where('user_id', $recipientId)->first();

        if ($recipient && $recipient->pivot->status === 'sent') {
            $message->recipients()->updateExistingPivot($recipientId, ['status' => 'read']);
            Log::info("Recipient ID {$recipientId} status updated to 'read' for Message ID {$messageId}");
        } else {
            Log::warning("Recipient ID {$recipientId} has already been marked as read or another status.");
        }
    } else {
        Log::warning("No message found with ID: {$messageId}");
    }

    // Return a 1x1 transparent pixel image
    $image = imagecreatetruecolor(1, 1);
    imagesavealpha($image, true);
    $transColor = imagecolorallocatealpha($image, 0, 0, 0, 127);
    imagefill($image, 0, 0, $transColor);

    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);

    Log::info("1x1 pixel image returned for messageId: {$messageId}");
    exit;
}

    
    
    public function manageMessages()
    {
        \Log::info("Accessing manage messages page.");
        return view('admin.manage-messages');
    }


//     public function fetchMessages()
// {
//     try {
//         \Log::info("Fetching messages for DataTable.");

//         // Fetch messages with the sender relationship, format created_at for date only
//         $messages = Message::with('sender:id,name')
//             ->select('id', 'subject', 'sender_id', 'created_at')
//             ->orderBy('created_at', 'desc')
//             ->get()
//             ->map(function ($message) {
//                 // Add only the date part of created_at
//                 $message->sent_at_date = $message->created_at->format('Y-m-d');
//                 return $message;
//             });

//         \Log::info("Fetched messages count:", ['count' => $messages->count()]);
//         return response()->json($messages);

//     } catch (\Exception $e) {
//         \Log::error("Error fetching messages:", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//         return response()->json(['error' => 'Failed to fetch messages.'], 500);
//     }
// }

public function fetchMessages()
{
    try {
        \Log::info("Fetching messages for DataTable.");

        $user = auth()->user();

        // Base query to fetch messages with sender relationship
        $query = Message::with('sender:id,name')
            ->select('id', 'subject', 'sender_id', 'created_at');

        // If the user has the "consultant" role, filter by sender_id
        if ($user->hasRole('consultant')) {
            \Log::info("Applying consultant filter for user:", ['user_id' => $user->id]);
            $query->where('sender_id', $user->id);
        }

        // Order by the most recent messages
        $messages = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) {
                // Add only the date part of created_at
                $message->sent_at_date = $message->created_at->format('Y-m-d');
                return $message;
            });

        \Log::info("Fetched messages count:", ['count' => $messages->count()]);
        return response()->json($messages);

    } catch (\Exception $e) {
        \Log::error("Error fetching messages:", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json(['error' => 'Failed to fetch messages.'], 500);
    }
}


    

    protected function processHtmlContent($htmlContent)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (strpos($src, 'data:image') === 0) {
                list($type, $data) = explode(';', $src);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);

                $imageName = 'emails/' . uniqid() . '.png';
                Storage::disk('public')->put($imageName, $data);

                $imageUrl = Storage::url($imageName);
                $img->setAttribute('src', $imageUrl);
            }
        }

        return $dom->saveHTML();
    }



    public function viewMessage($id)
{
    // Fetch the message by ID
    $message = Message::with(['sender', 'recipients'])->findOrFail($id);

    // Pass data to the view
    return view('admin.message.view', compact('message'));
}

    
    // In Message.php model

// In Message.php model

public function sender()
{
    return $this->belongsTo(User::class, 'sender_id');
}


public function recipients()
{
    return $this->belongsToMany(User::class, 'message_recipients', 'message_id', 'user_id')
                ->withPivot('status'); // Ensure the pivot table has a 'status' column
}



}
