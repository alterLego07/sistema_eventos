<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;

class PublicInvitationController extends Controller
{
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->with('event.template')
            ->firstOrFail();

        $event  = $invitation->event;
        $config = $event->merged_template_config;
        $slug   = $event->template?->slug ?? 'default';

        // Cada plantilla puede tener su propio Blade en invitation-templates/{slug}
        $view = view()->exists("invitation-templates.{$slug}")
            ? "invitation-templates.{$slug}"
            : 'invitation-templates.default';

        return view($view, compact('invitation', 'event', 'config'));
    }

    public function rsvp(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)
            ->with('event')
            ->firstOrFail();

        if ($invitation->confirmed) {
            return redirect()->route('invitation.show', $token)
                ->with('rsvp_already', true);
        }

        $validated = $request->validate([
            'confirmed'           => ['required', 'boolean'],
            'confirmed_guests'    => [
                'required_if:confirmed,1',
                'integer',
                'min:1',
                "max:{$invitation->allowed_guests}",
            ],
            'dietary_restrictions' => ['nullable', 'string', 'max:500'],
            'message'              => ['nullable', 'string', 'max:1000'],
            'song_suggestion'      => ['nullable', 'string', 'max:255'],
        ], [
            'confirmed.required'           => 'Indicá si confirmás tu asistencia.',
            'confirmed_guests.required_if' => 'Indicá cuántas personas asistirán.',
            'confirmed_guests.min'         => 'Al menos 1 persona debe asistir.',
            'confirmed_guests.max'         => "Máximo {$invitation->allowed_guests} invitado(s) permitido(s).",
        ]);

        $attending = (bool) $validated['confirmed'];

        $invitation->update([
            'confirmed'            => $attending,
            'confirmed_guests'     => $attending ? (int) ($validated['confirmed_guests'] ?? 1) : 0,
            'confirmed_at'         => now(),
            'dietary_restrictions' => $validated['dietary_restrictions'] ?? null,
            'message'              => $validated['message'] ?? null,
            'song_suggestion'      => $validated['song_suggestion'] ?? null,
        ]);

        return redirect()->route('invitation.show', $token)
            ->with($attending ? 'rsvp_confirmed' : 'rsvp_declined', true);
    }
}
