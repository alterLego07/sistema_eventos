<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvitationRequest;
use App\Models\Event;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Middleware;

class InvitationController extends Controller
{
    #[Middleware('permission:invitations.view')]
    public function index(Event $event)
    {
        $invitations = $event->invitations()->latest()->paginate(20);
        return view('admin.invitations.index', compact('event', 'invitations'));
    }

    #[Middleware('permission:invitations.create')]
    public function create(Event $event)
    {
        return view('admin.invitations.create', compact('event'));
    }

    #[Middleware('permission:invitations.create')]
    public function store(StoreInvitationRequest $request, Event $event)
    {
        $event->invitations()->create($request->validated());

        return redirect()->route('admin.events.invitations.index', $event)
            ->with('success', 'Invitación creada correctamente.');
    }

    #[Middleware('permission:invitations.edit')]
    public function edit(Invitation $invitation)
    {
        $invitation->load('event');
        return view('admin.invitations.edit', compact('invitation'));
    }

    #[Middleware('permission:invitations.edit')]
    public function update(Request $request, Invitation $invitation)
    {
        $validated = $request->validate([
            'guest_name'     => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email'],
            'table_number'   => ['nullable', 'integer', 'min:1'],
            'allowed_guests' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $invitation->update($validated);

        return redirect()->route('admin.events.invitations.index', $invitation->event_id)
            ->with('success', 'Invitación actualizada correctamente.');
    }

    #[Middleware('permission:invitations.delete')]
    public function destroy(Invitation $invitation)
    {
        $eventId = $invitation->event_id;
        $invitation->delete();

        return redirect()->route('admin.events.invitations.index', $eventId)
            ->with('success', 'Invitación eliminada correctamente.');
    }
}
