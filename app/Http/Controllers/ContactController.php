<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Support\Site;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    /**
     * Submitted via fetch(), not a plain form post — see the route
     * definition for why. Always responds JSON; validation failures are
     * turned into a 422 JSON body automatically by the FormRequest.
     */
    public function store(StoreContactRequest $request, Site $site): JsonResponse
    {
        $message = ContactMessage::create($request->payload());

        try {
            Mail::to($site->contactRecipient())->send(new ContactMessageReceived($message));
        } catch (Throwable $e) {
            // The message is already stored — a mail outage must not lose the lead.
            Log::error('Contact notification failed to send.', [
                'contact_message_id' => $message->id,
                'exception' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => "Thanks, {$message->name} — your message landed. I usually reply within one business day.",
        ]);
    }
}
