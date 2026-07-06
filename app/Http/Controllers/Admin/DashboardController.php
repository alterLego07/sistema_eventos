<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\Template;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalEvents'         => Event::count(),
            'publishedEvents'     => Event::published()->count(),
            'totalInvitations'    => Invitation::count(),
            'confirmedInvitations'=> Invitation::confirmed()->count(),
            'pendingInvitations'  => Invitation::pending()->count(),
            'totalTemplates'      => Template::active()->count(),
            'recentEvents'        => Event::with('template')
                                        ->withCount(['invitations', 'invitations as confirmed_invitations_count' => fn ($q) => $q->where('confirmed', true)])
                                        ->latest()
                                        ->take(5)
                                        ->get(),
        ]);
    }
}
