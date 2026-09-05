<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();

        return view('admin.messages.index', [
            'messages' => ContactMessage::query()
                ->when($filter === 'unread', fn ($query) => $query->unread())
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'filter' => $filter,
            'unreadCount' => ContactMessage::unread()->count(),
        ]);
    }

    public function show(ContactMessage $message): View
    {
        if ($message->isUnread()) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function toggleRead(ContactMessage $message): RedirectResponse
    {
        $message->update(['read_at' => $message->isUnread() ? now() : null]);

        return back()->with('status', $message->isUnread() ? 'Marked as unread.' : 'Marked as read.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()
            ->route('admin.messages.index')
            ->with('status', 'Message deleted.');
    }
}
