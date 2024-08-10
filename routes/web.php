<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\EventController;
use GuzzleHttp\Middleware;

// Esses Nomes são padrões do Laravel :

// Index: para mostrar todos os registros
Route::get('/', [EventController::class, 'index']);

// Create: para mostrar o formulario de criar evento no banco
Route::get('/events/create', [EventController::class, 'create'])->Middleware('auth');

// Store: para enviar dados para o banco
Route::post('/events', [EventController::class, 'store']);

// Show: mostrar um dado do banco
Route::get('/events/{id}', [EventController::class, 'show']);

// Deletar evento
Route::delete('/events/{id}', [EventController::class, 'destroy'])->middleware('auth');

// Rota para pagina de editar
Route::get('/events/edit/{id}', [EventController::class, 'edit'])->middleware('auth');

// Update
Route::put('/events/update/{id}', [EventController::class, 'update'])->middleware('auth');

Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth');

Route::post('/events/join/{id}', [EventController::class, 'joinEvent'])->middleware('auth');

Route::delete('/events/leave/{id}', [EventController::class, 'leaveEvent'])->middleware('auth');

