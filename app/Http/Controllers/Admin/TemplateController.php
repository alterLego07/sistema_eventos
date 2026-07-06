<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Middleware;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    #[Middleware('permission:templates.view')]
    public function index()
    {
        $templates = Template::withCount('events')->orderBy('name')->paginate(12);
        return view('admin.templates.index', compact('templates'));
    }

    #[Middleware('permission:templates.create')]
    public function create()
    {
        $defaultConfig = Template::getDefaultConfiguration();
        return view('admin.templates.create', compact('defaultConfig'));
    }

    #[Middleware('permission:templates.create')]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                       => ['required', 'string', 'max:255', 'unique:templates,name'],
            'description'                => ['nullable', 'string'],
            'configuration.colors.primary'    => ['required', 'string'],
            'configuration.colors.secondary'  => ['required', 'string'],
            'configuration.colors.accent'     => ['required', 'string'],
            'configuration.colors.background' => ['required', 'string'],
            'configuration.colors.text'       => ['required', 'string'],
            'configuration.fonts.heading'     => ['required', 'string'],
            'configuration.fonts.body'        => ['required', 'string'],
        ]);

        Template::create([
            'name'          => $validated['name'],
            'slug'          => Str::slug($validated['name']),
            'description'   => $validated['description'] ?? null,
            'active'        => $request->boolean('active', true),
            'configuration' => $request->input('configuration'),
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Plantilla creada correctamente.');
    }

    #[Middleware('permission:templates.edit')]
    public function edit(Template $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    #[Middleware('permission:templates.edit')]
    public function update(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:templates,name,' . $template->id],
            'description' => ['nullable', 'string'],
        ]);

        $template->update([
            'name'          => $validated['name'],
            'description'   => $validated['description'] ?? null,
            'active'        => $request->boolean('active', true),
            'configuration' => $request->input('configuration', $template->configuration),
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    #[Middleware('permission:templates.delete')]
    public function destroy(Template $template)
    {
        $template->update(['active' => false]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Plantilla desactivada correctamente.');
    }
}
