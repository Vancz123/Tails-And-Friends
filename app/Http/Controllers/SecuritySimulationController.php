<?php

namespace App\Http\Controllers;

use App\Models\SecurityIncident;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SecuritySimulationController extends Controller
{
    private const SIMULATED_ATTEMPTS = [
        ['category' => 'Acesso não autorizado', 'summary' => 'Nice! Este teste aponta para uma rota protegida. Vamos verificar as permissões! Desta Forma o Eggman não consegue aceder a Database!'],
        ['category' => 'Acesso não autorizado', 'summary' => 'Este pedido de demonstração veio sem sessão. Um bom teste para a autenticação! Não Seria a primeira nem ultima vez que o Eggman tenta roubar a identidade a um dos meus amigos...'],
        ['category' => 'Acesso não autorizado', 'summary' => 'Esta área é só para administradores. Vou confirmar as regras de acesso! Aquele Eggman! Sempre a tentar mexer com a minha base de dados!'],
        ['category' => 'Limite de pedidos', 'summary' => 'A tentativa passou do limite. O rate limiter entra em ação! Realmente foi uma boa ideia implementar..'],
        ['category' => 'Limite de pedidos', 'summary' => 'Mais uma acima do limite? Apanhada! A defesa está a funcionar.Aquele Eggman! DDOS e tão a cara dele!'],
    ];

    public function index(Request $request)
    {
        $this->requireAdministrator();

        $incidents = SecurityIncident::query()->latest()->limit(30)->get();

        return view('security.index', compact('incidents'));
    }

    public function simulate(Request $request)
    {
        $this->requireAdministrator();

        $limiterKey = 'eggman-simulation:' . Str::uuid();
        $incidents = collect(self::SIMULATED_ATTEMPTS)->map(function (array $attempt, int $index) use ($request, $limiterKey) {
            $blocked = RateLimiter::tooManyAttempts($limiterKey, 3);

            if (!$blocked) {
                RateLimiter::hit($limiterKey, 60);
            }

            $incident = SecurityIncident::create([
                'user_id' => $request->user()->id,
                'category' => $attempt['category'],
                'summary' => $attempt['summary'],
                'attempt' => $index + 1,
                'blocked' => $blocked,
            ]);

            Log::notice('Evento sintético do simulador de segurança.', [
                'simulation' => 'eggman-demo',
                'user_id' => $request->user()->id,
                'attempt' => $incident->attempt,
                'blocked' => $blocked,
                'category' => $incident->category,
            ]);

            return $incident;
        });

        RateLimiter::clear($limiterKey);

        $blockedCount = $incidents->where('blocked', true)->count();

        return redirect()->route('security.index')
            ->with('status', 'Boa! ' . $blockedCount . ' tentativas de teste foram bloqueadas pelo limite de pedidos. A defesa está a funcionar!');
}

    public function destroy(Request $request, SecurityIncident $incident): RedirectResponse
    {
        $this->requireAdministrator();
        $incident->delete();

        return redirect()->route('security.index')
            ->with('status', 'Registo de segurança removido.');
    }

    public function destroyAll(Request $request): RedirectResponse
    {
        $this->requireAdministrator();
        $deletedCount = SecurityIncident::query()->delete();

        return redirect()->route('security.index')
            ->with('status', 'Número de registos de segurança removidos: ' . $deletedCount . '.');
    }

    private function requireAdministrator(): void
    {
        abort_unless(auth()->user()?->isAdministrator(), 403);
    }
}
