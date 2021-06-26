<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/refresh', [AuthController::class, 'refresh']);
Route::get('/user', [AuthController::class, 'getUser']);

Route::get('/email/verify/{id}/{hash}', [AuthController::class,'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');


Route::group(['middleware' => ['auth:api', 'verified'], 'prefix'=>'tasks'], function (){
    Route::get('/', [TaskController::class, 'index']);
    Route::get('/{task}', [TaskController::class, 'show']);
    Route::post('/create', [TaskController::class, 'store']);
    Route::post('/{task}/update', [TaskController::class, 'update']);
    Route::post('/{task}/delete', [TaskController::class, 'destroy']);
});
