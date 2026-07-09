<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetItemRequest;
use App\Http\Requests\UpdateBudgetItemRequest;
use App\Models\BudgetItem;
use App\Models\Event;
use Illuminate\Routing\Attributes\Middleware;

/**
 * Manage the budget (expense lines) of an event. Tenant isolation is handled
 * by the CompanyScope global scope on Event and BudgetItem, so route model
 * binding already prevents cross-company access.
 */
class BudgetItemController extends Controller
{
    #[Middleware('permission:budget.view')]
    public function index(Event $event)
    {
        $items = $event->budgetItems()
            ->orderBy('category')
            ->orderBy('concept')
            ->get();

        // Breakdown by category for the summary table
        $byCategory = $items->groupBy('category')->map(function ($group) {
            return [
                'estimated' => (float) $group->sum('estimated_amount'),
                'actual' => (float) $group->sum(fn ($i) => $i->actual_amount ?? 0),
                'count' => $group->count(),
            ];
        });

        return view('admin.budget.index', compact('event', 'items', 'byCategory'));
    }

    #[Middleware('permission:budget.create')]
    public function create(Event $event)
    {
        $categories = BudgetItem::CATEGORIES;

        return view('admin.budget.create', compact('event', 'categories'));
    }

    #[Middleware('permission:budget.create')]
    public function store(StoreBudgetItemRequest $request, Event $event)
    {
        $event->budgetItems()->create($request->validated());

        return redirect()->route('admin.events.budget.index', $event)
            ->with('success', 'Partida de presupuesto agregada.');
    }

    #[Middleware('permission:budget.edit')]
    public function edit(BudgetItem $budgetItem)
    {
        $budgetItem->load('event');
        $categories = BudgetItem::CATEGORIES;

        return view('admin.budget.edit', compact('budgetItem', 'categories'));
    }

    #[Middleware('permission:budget.edit')]
    public function update(UpdateBudgetItemRequest $request, BudgetItem $budgetItem)
    {
        $budgetItem->update($request->validated());

        return redirect()->route('admin.events.budget.index', $budgetItem->event_id)
            ->with('success', 'Partida actualizada correctamente.');
    }

    #[Middleware('permission:budget.delete')]
    public function destroy(BudgetItem $budgetItem)
    {
        $eventId = $budgetItem->event_id;
        $budgetItem->delete();

        return redirect()->route('admin.events.budget.index', $eventId)
            ->with('success', 'Partida eliminada correctamente.');
    }
}
