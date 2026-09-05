<x-mail::message>
# New enquiry

**{{ $contactMessage->name }}**{{ $contactMessage->company ? ' — '.$contactMessage->company : '' }}
{{ $contactMessage->email }}

@if ($contactMessage->budget)
**Budget:** {{ $contactMessage->budget }}
@endif

@if ($contactMessage->subject)
**Subject:** {{ $contactMessage->subject }}
@endif

---

{{ $contactMessage->message }}

---

<x-mail::button :url="route('admin.messages.show', $contactMessage)">
Open in admin
</x-mail::button>

<small>Received {{ $contactMessage->created_at->format('d M Y, H:i') }} · IP {{ $contactMessage->ip_address }}</small>
</x-mail::message>
