<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\DisplayUsersController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;


Route::get('/', [LoginController::class, 'checkAuthHp'])->name('/');

Route::get('/mytasks', [LoginController::class, 'checkAuthMyTasks'])->name('/mytasks');

Route::post('logout', [LoginController::class, 'logOut']); //LOGOUT

Route::post('postregister', [RegistrationController::class, 'storeUserToDB']); //SUBMIT REGISTER FORM

Route::post('postlogin', [LoginController::class, 'loginUser']); //SUBMIT LOGIN FORM

Route::post('addtask', [TaskController::class, 'createTask']); //SUBMIT ADD TASK

Route::post('completetask', [TaskController::class, 'completeTask']); //SUBMIT COMPLETE TASK

Route::post('redotask', [TaskController::class, 'redoTask']); //CHANGE TASK TO ACTIVE

Route::post('deletetask', [TaskController::class, 'deleteTask']); //SUBMIT DELETE TASK

Route::post('displayTasks', [TaskController::class, 'displayTasks']); //DISPLAY USERS TASKS

Route::get('{catchall}', [LoginController::class, 'checkAuthHp'])->where('catchall', '.*');; //RANDOM/UNDEFINED URL