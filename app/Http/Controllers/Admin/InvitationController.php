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
        // Pasamos company_id directamente desde el evento para evitar que
        // booted() haga un SELECT adicional a events durante el INSERT,
        // lo cual puede causar lock contention en MySQL bajo carga.
        $event->invitations()->create(array_merge(
            $request->validated(),
            ['company_id' => $event->company_id, 'invited' => $request->boolean('invited')]
        ));

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
            'invited'        => ['sometimes', 'boolean'],
        ]);

        $invitation->update($validated + ['invited' => $request->boolean('invited')]);

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

    /**
     * Confirma o rechaza la asistencia en nombre del invitado.
     *
     * Solo admin/super-admin, para los casos en que el invitado no puede
     * usar el enlace público de confirmación.
     */
    #[Middleware('role:super-admin|admin')]
    public function confirmByAdmin(Request $request, Invitation $invitation)
    {
        $validated = $request->validate([
            'confirmed'        => ['required', 'boolean'],
            'confirmed_guests' => ['required_if:confirmed,1', 'integer', 'min:1', "max:{$invitation->allowed_guests}"],
        ]);

        $attending = (bool) $validated['confirmed'];

        $invitation->update([
            'confirmed'        => $attending,
            'confirmed_guests' => $attending ? (int) $validated['confirmed_guests'] : 0,
            'confirmed_at'     => now(),
        ]);

        return redirect()->route('admin.events.invitations.index', $invitation->event_id)
            ->with('success', 'Asistencia registrada por el administrador.');
    }
}
