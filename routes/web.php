<?php

use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\SecuritySimulationController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/characters')->name('welcome');
<<<<<<< HEAD

// Catálogo público.
Route::get('/characters', [CharacterController::class, 'index'])->name('characters.index');
Route::get('/games', [GameController::class, 'index'])->name('games.index');
=======
Route::get('/characters', [CharacterController::class, 'index'])->name('characters.index');
Route::get('/characters/create', [CharacterController::class, 'create'])
    ->middleware('auth')->name('characters.create');
Route::get('/characters/{character}', [CharacterController::class, 'show'])->name('characters.show');
Route::post('/characters', [CharacterController::class, 'store'])
    ->middleware('auth')->name('characters.store');
Route::get('/characters/{character}/edit', [CharacterController::class, 'edit'])
    ->middleware('auth')->name('characters.edit');
Route::put('/characters/{character}', [CharacterController::class, 'update'])
    ->middleware('auth')->name('characters.update');
Route::delete('/characters/{character}', [CharacterController::class, 'destroy'])
    ->middleware('auth')->name('characters.destroy');
Route::put('/characters/{character}/verifications', [CharacterVerificationController::class, 'update'])
    ->middleware('auth')->name('characters.verifications.update');

Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/create', [GameController::class, 'create'])
    ->middleware('auth')->name('games.create');
Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
Route::post('/games', [GameController::class, 'store'])
    ->middleware('auth')->name('games.store');
Route::get('/games/{game}/edit', [GameController::class, 'edit'])
    ->middleware('auth')->name('games.edit');
Route::put('/games/{game}', [GameController::class, 'update'])
    ->middleware('auth')->name('games.update');
Route::delete('/games/{game}', [GameController::class, 'destroy'])
    ->middleware('auth')->name('games.destroy');
>>>>>>> 51ef348453b200e99430a06005b28634517d253b

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');
Route::middleware('auth')->group(function () {
<<<<<<< HEAD
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    /*
     | Formulários de criação e edição: qualquer utilizador autenticado pode abri-los.
     | Quem não é administrador vê a ficha em modo de confirmação; só o administrador
     | pode submeter alterações. As rotas estáticas (/create) ficam antes de /{modelo}.
     */
    Route::get('/characters/create', [CharacterController::class, 'create'])->name('characters.create');
    Route::get('/characters/{character}/edit', [CharacterController::class, 'edit'])->name('characters.edit');
    Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
    Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');

    Route::put('/characters/{character}/verifications', [CharacterVerificationController::class, 'update'])
        ->name('characters.verifications.update');

    Route::view('/settings', 'auth.settings')->name('settings.index');
    Route::view('/settings/password', 'auth.update-password')->name('user-password.edit');
    Route::delete('/settings/account', [UserManagementController::class, 'destroyAccount'])
        ->name('account.destroy');

    Route::middleware('admin')->group(function () {
        // Mutação do conteúdo: exclusivo dos administradores.
        Route::post('/characters', [CharacterController::class, 'store'])->name('characters.store');
        Route::put('/characters/{character}', [CharacterController::class, 'update'])->name('characters.update');
        Route::delete('/characters/{character}', [CharacterController::class, 'destroy'])->name('characters.destroy');

        Route::post('/games', [GameController::class, 'store'])->name('games.store');
        Route::put('/games/{game}', [GameController::class, 'update'])->name('games.update');
        Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');

        Route::get('/security-demo', [SecuritySimulationController::class, 'index'])->name('security.index');
        Route::post('/security-demo/simulate', [SecuritySimulationController::class, 'simulate'])
            ->middleware('throttle:eggman-demo')
            ->name('security.simulate');
        Route::delete('/security-demo/incidents', [SecuritySimulationController::class, 'destroyAll'])
            ->name('security.incidents.destroy-all');
        Route::delete('/security-demo/incidents/{incident}', [SecuritySimulationController::class, 'destroy'])
            ->name('security.incidents.destroy');

        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::put('/admin/users/{user}/role', [UserManagementController::class, 'updateRole'])
            ->name('admin.users.update-role');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])
            ->name('admin.users.destroy');
    });
=======
    Route::view('/settings', 'auth.settings')->name('settings.index');
    Route::view('/user/password', 'auth.update-password')->name('user-password.edit');
    Route::delete('/settings/account', [UserManagementController::class, 'destroyAccount'])
        ->name('account.destroy');
    Route::get('/security-demo', [SecuritySimulationController::class, 'index'])->name('security.index');
    Route::post('/security-demo/simulate', [SecuritySimulationController::class, 'simulate'])
        ->middleware('throttle:eggman-demo')
        ->name('security.simulate');
    Route::delete('/security-demo/incidents', [SecuritySimulationController::class, 'destroyAll'])
        ->name('security.incidents.destroy-all');
    Route::delete('/security-demo/incidents/{incident}', [SecuritySimulationController::class, 'destroy'])
        ->name('security.incidents.destroy');
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::put('/admin/users/{user}/role', [UserManagementController::class, 'updateRole'])
        ->name('admin.users.update-role');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])
        ->name('admin.users.destroy');
>>>>>>> 51ef348453b200e99430a06005b28634517d253b
});

// Detalhe público, registado depois das rotas estáticas para não capturar /create.
Route::get('/characters/{character}', [CharacterController::class, 'show'])->name('characters.show');
Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
