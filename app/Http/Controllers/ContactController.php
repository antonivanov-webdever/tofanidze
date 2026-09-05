<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Support\Site;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(StoreContactRequest $request, Site $site): RedirectResponse
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

        return redirect()
            ->route('contact.show')
            ->with('status', "Thanks, {$message->name} — your message landed. I usually reply within one business day.");
    }
}
