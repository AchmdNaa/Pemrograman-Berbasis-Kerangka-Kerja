<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Manajemen akun kasir oleh admin.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('users.index', [
            'users' => User::withCount(['transactions as trx_today' => fn ($q) => $q->whereDate('created_at', today())])
                ->withSum('transactions as omzet', 'total')->orderBy('role')->orderBy('name')->get(),
            'edit'  => $request->query('edit') ? User::find($request->query('edit')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        User::create($data);

        return redirect()->route('users.index')->with('success', "Akun {$data['name']} dibuat.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if ($user->is($request->user()) && ($data['role'] !== 'admin' || ! $data['is_active'])) {
            return back()->with('error', 'Anda tidak dapat menurunkan peran atau menonaktifkan akun sendiri.');
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);   // password otomatis di-hash oleh cast 'hashed' di model User

        return redirect()->route('users.index')->with('success', "Akun {$user->name} diperbarui.");
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:100'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)],
            'role'     => ['required', Rule::in(['admin', 'kasir'])],
            'password' => [$user ? 'nullable' : 'required', Password::min(8)->letters()->numbers()],
        ], [], ['name' => 'nama', 'role' => 'peran']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
