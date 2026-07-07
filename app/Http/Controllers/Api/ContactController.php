<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Customer submits message
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        Contact::create($data);

        return response()->json([
            'message' => 'Your message has been sent successfully.'
        ]);
    }

    // Admin message list
    public function index()
    {
        return Contact::latest()->paginate(10);
    }

    // View one message
    public function show(Contact $contact)
{
    if (!$contact->is_read) {
        $contact->update([
            'is_read' => true
        ]);
    }

    return response()->json($contact);
}

    // Mark as read
    public function markAsRead(Contact $contact)
    {
        $contact->update([
            'is_read' => true
        ]);

        return response()->json([
            'message' => 'Message marked as read.'
        ]);
    }

    // Delete message
   public function destroy(Contact $contact)
{
    $contact->delete();

    return response()->json([
        'message' => 'Message deleted successfully.'
    ]);
}

    // Sidebar unread badge
    public function unreadCount()
    {
        return response()->json([
            'count' => Contact::where('is_read', false)->count()
        ]);
    }
}