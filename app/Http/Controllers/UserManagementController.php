<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->requireAdministrator();

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($filters['q'] ?? '');

        $users = User::query()
            ->when($search !== '', fn(Builder $query) => $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $this->requireAdministrator();

        $data = $request->validate([
            'role' => ['required', 'string', Rule::in(['user', 'admin'])],
        ]);

        DB::transaction(function () use ($user, $data): void {
            $administrators = User::query()
                ->where('role', 'admin')
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);
            $managedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            if ($managedUser->is(auth()->user()) && $data['role'] !== 'admin') {
                throw ValidationException::withMessages([
                    'role' => 'Não podes remover o teu próprio acesso de administrador.',
                ]);
            }

            if ($managedUser->isAdministrator() && $data['role'] === 'user' && $administrators->count() <= 1) {
                throw ValidationException::withMessages([
                    'role' => 'A aplicação precisa de pelo menos um administrador.',
                ]);
            }

            $managedUser->forceFill(['role' => $data['role']])->save();
        });

        return redirect()->route('admin.users.index')->with('status', 'Permissão do utilizador atualizada.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->requireAdministrator();

        if ($user->is(auth()->user())) {
            return redirect()->route('admin.users.index')
                ->with('status', 'Não podes apagar a tua própria conta por aqui. Usa as definições da conta.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', 'Utilizador removido com sucesso.');
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password:web'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('status', 'A tua conta foi eliminada permanentemente.');
    }

    private function requireAdministrator(): void
    {
        abort_unless(auth()->user()?->isAdministrator(), 403);
    }
}
