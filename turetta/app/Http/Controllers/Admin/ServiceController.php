<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::orderBy('name')->get();

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5'],
            'price'            => ['required', 'numeric', 'min:0'],
            'active'           => ['sometimes', 'boolean'],
        ]);

        $validated['active'] = $request->boolean('active');

        Service::create($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Serviço cadastrado com sucesso.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5'],
            'price'            => ['required', 'numeric', 'min:0'],
            'active'           => ['sometimes', 'boolean'],
        ]);

        $validated['active'] = $request->boolean('active');

        $service->update($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')
                         ->with('success', 'Serviço excluído com sucesso.');
    }
}
