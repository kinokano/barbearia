<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'profile_photo' => ['nullable', 'image', 'max:2048'],
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

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('professionals', 'public');
        }

        $professional = Professional::create([
            'user_id'   => $user->id,
            'specialty' => $validated['specialty'] ?? null,
            'active'    => true,
            'profile_photo' => $photoPath,
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
            'profile_photo' => ['nullable', 'image', 'max:2048'],
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

        $photoPath = $professional->profile_photo;
        if ($request->hasFile('profile_photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('profile_photo')->store('professionals', 'public');
        }

        $professional->update([
            'specialty' => $validated['specialty'] ?? null,
            'active'    => $request->boolean('active'),
            'profile_photo' => $photoPath,
        ]);

        $professional->services()->sync($validated['services']);

        return redirect()->route('admin.professionals.index')
                         ->with('success', 'Profissional atualizado com sucesso.');
    }

    public function destroy(Professional $professional): RedirectResponse
    {
        if ($professional->profile_photo) {
            Storage::disk('public')->delete($professional->profile_photo);
        }

        $professional->user->delete();

        return redirect()->route('admin.professionals.index')
                         ->with('success', 'Profissional excluído com sucesso.');
    }
}
