<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Template;
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    #[Middleware('permission:events.view')]
    public function stats(Event $event)
    {
        $totals = $event->invitations()
            ->selectRaw('
                count(*) as total,
                sum(case when confirmed = 1 then 1 else 0 end) as confirmed,
                sum(case when confirmed = 0 then 1 else 0 end) as pending,
                sum(allowed_guests) as total_allowed,
                sum(case when confirmed = 1 then confirmed_guests else 0 end) as total_expected
            ')
            ->first();

        $confirmationRate = $totals->total > 0
            ? round(($totals->confirmed / $totals->total) * 100, 1)
            : 0;

        $recentConfirmations = $event->invitations()
            ->where('confirmed', true)
            ->orderByDesc('confirmed_at')
            ->limit(10)
            ->get(['guest_name', 'confirmed_guests', 'confirmed_at', 'dietary_restrictions', 'message', 'song_suggestion']);

        $dietaryRestrictions = $event->invitations()
            ->whereNotNull('dietary_restrictions')
            ->where('dietary_restrictions', '!=', '')
            ->get(['guest_name', 'dietary_restrictions']);

        $songSuggestions = $event->invitations()
            ->whereNotNull('song_suggestion')
            ->where('song_suggestion', '!=', '')
            ->get(['guest_name', 'song_suggestion']);

        $tableDistribution = $event->invitations()
            ->whereNotNull('table_number')
            ->selectRaw('
                table_number,
                count(*) as total,
                sum(case when confirmed = 1 then 1 else 0 end) as confirmed_count,
                sum(case when confirmed = 1 then confirmed_guests else 0 end) as expected_guests
            ')
            ->groupBy('table_number')
            ->orderBy('table_number')
            ->get();

        return view('admin.events.stats', compact(
            'event', 'totals', 'confirmationRate',
            'recentConfirmations', 'dietaryRestrictions',
            'songSuggestions', 'tableDistribution'
        ));
    }

    #[Middleware('permission:events.view')]
    public function index()
    {
        $events = Event::with('template')
            ->withCount(['invitations', 'invitations as confirmed_count' => fn ($q) => $q->where('confirmed', true)])
            ->latest()
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    #[Middleware('permission:events.create')]
    public function create()
    {
        $templates = Template::active()->orderBy('name')->get();
        return view('admin.events.create', compact('templates'));
    }

    #[Middleware('permission:events.create')]
    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['name']) . '-' . Str::lower(Str::random(5));
        $data['status'] = $data['status'] ?? 'draft';

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento creado correctamente.');
    }

    #[Middleware('permission:events.edit')]
    public function edit(Event $event)
    {
        $templates = Template::active()->orderBy('name')->get();
        return view('admin.events.edit', compact('event', 'templates'));
    }

    #[Middleware('permission:events.edit')]
    public function update(UpdateEventRequest $request, Event $event)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento actualizado correctamente.');
    }

    #[Middleware('permission:events.delete')]
    public function destroy(Event $event)
    {
        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Evento eliminado correctamente.');
    }
}
