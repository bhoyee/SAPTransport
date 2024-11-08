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
        $request->validate([
            'recipient_type' => 'required|string',
            'subject' => 'required|string',
            'message_body' => 'required|string',
            'individual_email' => 'nullable|email|required_if:recipient_type,individual',
        ]);
    
        $recipientType = $request->input('recipient_type');
        $subject = $request->input('subject');
        $messageBody = $this->processHtmlContent($request->input('message_body'));
        $individualEmail = $request->input('individual_email');
        $recipients = [];
    
        // Determine recipients based on recipient type
        if ($recipientType === 'all') {
            $recipients = User::role(['passenger', 'consultant'])->pluck('email')->toArray();
        } elseif ($recipientType === 'passenger') {
            $recipients = User::role('passenger')->pluck('email')->toArray();
        } elseif ($recipientType === 'staff') {
            $recipients = User::role('consultant')->pluck('email')->toArray();
        } elseif ($recipientType === 'individual' && $individualEmail) {
            $recipients = [$individualEmail];
        } else {
            Log::error("No recipients found for recipient type: {$recipientType}");
            return response()->json(['error' => 'No valid recipients found.'], 400);
        }
    
        foreach ($recipients as $email) {
            try {
                // Log the sending attempt
                Log::info("Attempting to send email to {$email}");
    
                // Create the message record
                $messageRecord = Message::create([
                    'sender_id' => Auth::id(),
                    'receiver_id' => User::where('email', $email)->value('id'),
                    'subject' => $subject,
                    'message' => $messageBody,
                    'status' => 'received',
                ]);
    
                $trackingPixelUrl = route('email.tracking.pixel', ['messageId' => $messageRecord->id]);
                $messageBodyWithPixel = $messageBody . "<img src='{$trackingPixelUrl}' alt='' width='1' height='1' style='display:none;'>";
    
                // Send actual email
                Mail::send([], [], function ($message) use ($email, $subject, $messageBodyWithPixel) {
                    $message->to($email)
                            ->subject($subject)
                            ->html($messageBodyWithPixel);
                });
    
                Log::info("Email sent to {$email}");
    
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$email}", ['error' => $e->getMessage()]);
            }
        }
    
        return response()->json(['success' => 'Broadcast message sent successfully!']);
    }
    

    public function trackEmailOpen(Request $request)
    {
        // Log the entry into the function
        Log::info("Entering trackEmailOpen function");
    
        // Capture and log the message ID from the request
        $messageId = $request->query('messageId');
        Log::info("Tracking pixel requested with messageId: {$messageId}");
    
        // Attempt to find the message by ID
        $message = Message::find($messageId);
    
        // Check if the message was found and if it has status 'received'
        if ($message) {
            Log::info("Message found with ID: {$messageId} and current status: {$message->status}");
            
            if ($message->status === 'received') {
                // Update status to 'read'
                $message->update(['status' => 'read']);
                Log::info("Message ID {$messageId} status updated to 'read'");
            } else {
                Log::warning("Message ID {$messageId} has already been marked as read or has another status.");
            }
        } else {
            Log::warning("No message found with ID: {$messageId}");
        }
    
        // Return a 1x1 transparent pixel image
        $image = imagecreatetruecolor(1, 1);
        imagesavealpha($image, true);
        $trans_color = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $trans_color);
    
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

    public function fetchMessages()
    {
        try {
            \Log::info("Fetching messages for DataTable.");

            $messages = Message::with('sender:id,name')
                ->select('id', 'subject', 'sender_id', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info("Fetched messages count:", ['count' => $messages->count()]);
            return response()->json($messages);
        } catch (\Exception $e) {
            \Log::error("Error fetching messages:", ['error' => $e->getMessage()]);
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
        $message = Message::findOrFail($id);
    
        // Fetch sender and receiver details
        $sender = User::find($message->sender_id);
        $receiver = User::find($message->receiver_id);
    
        // Pass data to the view
        return view('admin.message.view', compact('message', 'sender', 'receiver'));
    }
    
    

}
