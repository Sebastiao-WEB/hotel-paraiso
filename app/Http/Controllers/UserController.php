<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('reservasCriadas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('cargo')) {
            $query->where('cargo', $request->cargo);
        }

        $users = $query->orderBy('name')->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'cargo' => 'required|in:admin,recepcionista,limpeza',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'cargo' => $validated['cargo'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show($id)
    {
        $user = User::with(['reservasCriadas', 'reservasCheckin', 'reservasCheckout'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.form', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'cargo' => 'required|in:admin,recepcionista,limpeza',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cargo' => $validated['cargo'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Não permitir excluir o próprio usuário
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Você não pode excluir seu próprio usuário!');
        }

        // Não permitir excluir se houver reservas associadas
        if ($user->reservasCriadas()->count() > 0 || 
            $user->reservasCheckin()->count() > 0 || 
            $user->reservasCheckout()->count() > 0) {
            return redirect()->route('admin.users.index')->with('error', 'Não é possível excluir este usuário pois possui reservas associadas!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído com sucesso!');
    }
}

