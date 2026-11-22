<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Decide se o utilizador autenticado pode alterar conteúdo.
 *
 * Os formulários de criação e edição estão abertos a todos os utilizadores
 * autenticados, mas só os administradores podem guardar alterações.
 */
trait ChecksAdminAccess
{
    protected function canManageContent(?Request $request = null): bool
    {
        return (bool) ($request ?? request())->user()?->isAdministrator();
    }
}
