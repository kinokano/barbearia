<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    public function index(): View
    {
        $professionals = Professional::with(['user', 'services'])->get();

        return view('admin.professionals.index', compact('professionals'));
    }

    public function create(): View
    {
        $services = Service::active()->orderBy('name')->get();

        return view('admin.professionals.form', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'password'  => ['required', 'string', 'min:6'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'services'  => ['required', 'array'],
            'services.*' => ['exists:services,id'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id'  => 2, // Profissional
        ]);

        $professional = Professional::create([
            'user_id'   => $user->id,
            'specialty' => $validated['specialty'] ?? null,
            'active'    => true,
        ]);

        $professional->services()->sync($validated['services']);

        return redirect()->route('admin.professionals.index')
                         ->with('success', 'Profissional cadastrado com sucesso.');
    }

    public function edit(Professional $professional): View
    {
        $professional->load(['user', 'services']);
        $services = Service::active()->orderBy('name')->get();

        return view('admin.professionals.form', compact('professional', 'services'));
    }

    public function update(Request $request, Professional $professional): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email,' . $professional->user_id],
            'phone'     => ['nullable', 'string', 'max:20'],
            'password'  => ['nullable', 'string', 'min:6'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'active'    => ['sometimes', 'boolean'],
            'services'  => ['required', 'array'],
            'services.*' => ['exists:services,id'],
        ]);

        $userData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $professional->user->update($userData);

        $professional->update([
            'specialty' => $validated['specialty'] ?? null,
            'active'    => $request->boolean('active'),
        ]);

        $professional->services()->sync($validated['services']);

        return redirect()->route('admin.professionals.index')
                         ->with('success', 'Profissional atualizado com sucesso.');
    }

    public function destroy(Professional $professional): RedirectResponse
    {
        $professional->user->delete();

        return redirect()->route('admin.professionals.index')
                         ->with('success', 'Profissional excluído com sucesso.');
    }
}
