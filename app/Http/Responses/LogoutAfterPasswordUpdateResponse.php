<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\PasswordUpdateResponse as PasswordUpdateResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LogoutAfterPasswordUpdateResponse implements PasswordUpdateResponseContract
{
    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): Response
    {
        Auth::guard(config('fortify.guard'))->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if ($request->wantsJson()) {
            return new JsonResponse('', 200);
        }

        return redirect()->route('login')
            ->with('status', 'Palavra-passe alterada. Inicia sessão novamente com a nova palavra-passe.');
    }
}
