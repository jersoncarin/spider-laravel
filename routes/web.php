<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[App\Http\Controllers\Auth\SignInController::class, 'form'])->name('signin_view');
Route::post('/signin',[App\Http\Controllers\Auth\SignInController::class, 'signin'])->name('signin');


Route::get('/signup/', [App\Http\Controllers\Auth\SignUpController::class, 'form'])->name('signup_view');
Route::post('/signup/complete', [App\Http\Controllers\Auth\SignUpController::class, 'signup'])->name('signup');

Route::get('/logout',function() {
    return redirect('/')->with(Auth::logout());
});

Route::get('/arena', [App\Http\Controllers\ArenaController::class, 'index'])->name('arena');
Route::get('/user/betting/logs',[App\Http\Controllers\BettingLogsController::class, 'index'])->name('betting_logs');

Route::get('/user/profile', [App\Http\Controllers\UserController::class, 'user'])->middleware('revalidate')->middleware('auth');
Route::post('/user/profile/update', [App\Http\Controllers\UserController::class, 'update'])->middleware('revalidate')->middleware('auth');

Route::get('/request/{type}', [App\Http\Controllers\RequestController::class, 'request'])->middleware('revalidate')->middleware('auth');
Route::post('/request/submit', [App\Http\Controllers\RequestController::class, 'submit'])->middleware('revalidate')->middleware('auth');

Route::get('/request/{type}/history', [App\Http\Controllers\RequestController::class, 'history'])->middleware('revalidate')->middleware('auth');

Route::post('/bet/add', [App\Http\Controllers\ArenaController::class, 'addbet'])->middleware('revalidate')->middleware('auth');
Route::post('/bet/status', [App\Http\Controllers\ArenaController::class, 'betstatus'])->middleware('revalidate')->middleware('auth');
Route::post('/bet/msg', [App\Http\Controllers\ArenaController::class, 'msglog'])->middleware('revalidate')->middleware('auth')->middleware('admin');
Route::post('/bet/fire', [App\Http\Controllers\ArenaController::class, 'eventfire'])->middleware('revalidate')->middleware('auth')->middleware('admin');
Route::post('/bet/add/fight', [App\Http\Controllers\ArenaController::class, 'addfight'])->middleware('revalidate')->middleware('auth')->middleware('admin');
Route::post('/bet/declare/fight', [App\Http\Controllers\ArenaController::class, 'declarefight'])->middleware('revalidate')->middleware('auth')->middleware('admin');
Route::post('/reset/event', [App\Http\Controllers\ArenaController::class, 'resetevent'])->middleware('revalidate')->middleware('auth')->middleware('admin');
Route::get('/user/ask/', [App\Http\Controllers\ArenaController::class, 'csc'])->middleware('revalidate')->middleware('auth');
Route::post('/user/ask/create', [App\Http\Controllers\ArenaController::class, 'create_sub'])->middleware('revalidate')->middleware('auth');
Route::get('/user/ask/view/{subject_id}', [App\Http\Controllers\ArenaController::class, 'view_chat'])->middleware('revalidate')->middleware('auth');
Route::post('/user/ask/chat/send', [App\Http\Controllers\ArenaController::class, 'send_chat'])->middleware('revalidate')->middleware('auth');
Route::get('/user/ask/load', [App\Http\Controllers\ArenaController::class, 'load_chat'])->middleware('revalidate')->middleware('auth');

/* Super Admin Routing */
/*     Start here      */

// Main Dashify
Route::get('/admin/dashboard', [App\Http\Controllers\Admin::class, 'dashboard'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin',function() {
    return redirect('/admin/dashboard');
});

// For Loggings
Route::get('/admin/activity/logs', [App\Http\Controllers\Admin::class, 'visitors'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/bet/logs', [App\Http\Controllers\Admin::class, 'betlogs'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');


// For active users
Route::get('/admin/active/users', [App\Http\Controllers\Admin::class, 'active_users'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/active/users/delete/{id}', [App\Http\Controllers\Admin::class, 'delete_active_user'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/active/users/edit', [App\Http\Controllers\Admin::class, 'edit_active_user'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/active/users/data', [App\Http\Controllers\Admin::class, 'fetch_users_json'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');

// For pending users
Route::get('/admin/pending/users', [App\Http\Controllers\Admin::class, 'pending_users'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/pending/users/action/{action}/{id}', [App\Http\Controllers\Admin::class, 'action_pending_users'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');

// For Agent users
Route::get('/admin/agents', [App\Http\Controllers\Admin::class, 'agent_users'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/agents/add', [App\Http\Controllers\Admin::class, 'agent_users_add'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/agents/delete/{id}', [App\Http\Controllers\Admin::class, 'agent_users_delete'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/agents/edit', [App\Http\Controllers\Admin::class, 'agents_users_edit'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/agents/data', [App\Http\Controllers\Admin::class, 'fetch_users_json'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');

// For Requests
Route::get('/admin/requests', [App\Http\Controllers\Admin::class, 'requests'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/requests/approve/{id}', [App\Http\Controllers\Admin::class, 'requests_approve'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/requests/deleteorreject/{id}', [App\Http\Controllers\Admin::class, 'requests_delete_or_reject'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/requests/transfer', [App\Http\Controllers\Admin::class, 'transfer_request_points'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');

// For CSC
Route::get('/admin/csc/requests', [App\Http\Controllers\Admin::class, 'csc_requests'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/requests/approve/{id}', [App\Http\Controllers\Admin::class, 'requests_approve'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/csc/requests/close/{id}', [App\Http\Controllers\Admin::class, 'csc_close_requests'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/csc/requests/load/{id}', [App\Http\Controllers\Admin::class, 'csc_load_requests'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/csc/requests/chat', [App\Http\Controllers\Admin::class, 'csc_load_chat'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/csc/requests/send', [App\Http\Controllers\Admin::class, 'csc_send_chat'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');

// For Admin
Route::get('/admin/official/users', [App\Http\Controllers\Admin::class, 'official_users'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/official/users/delete/{id}', [App\Http\Controllers\Admin::class, 'delete_active_user'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/official/users/edit', [App\Http\Controllers\Admin::class, 'edit_official_user'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/official/users/data', [App\Http\Controllers\Admin::class, 'fetch_users_json'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/official/users/add', [App\Http\Controllers\Admin::class, 'add_official_user'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');

// For Verifier
Route::get('/verifier/dashboard', [App\Http\Controllers\Admin::class, 'verifier_pending_users'])->middleware('revalidate')->middleware('auth')->middleware('verifier');
Route::get('/verifier/users/action/{action}/{id}', [App\Http\Controllers\Admin::class, 'verifier_action_pending_users'])->middleware('revalidate')->middleware('auth')->middleware('verifier');

// For Gcash Account
Route::get('/admin/gcash/number', [App\Http\Controllers\Admin::class, 'gcash_account'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/gcash/number/add', [App\Http\Controllers\Admin::class, 'gcash_account_add'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::get('/admin/gcash/number/delete/{id}', [App\Http\Controllers\Admin::class, 'gcash_account_delete'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/gcash/number/edit', [App\Http\Controllers\Admin::class, 'gcash_account_edit'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');
Route::post('/admin/gcash/number/data', [App\Http\Controllers\Admin::class, 'fetch_gcash_account'])->middleware('revalidate')->middleware('auth')->middleware('superadmin');


/* Agent Routing */
/*  Start here   */

//Main Dashify
Route::get('/agents/dashboard', [App\Http\Controllers\Agent::class, 'main_dashboard'])->middleware('revalidate')->middleware('auth')->middleware('agent');
Route::get('/agents/pending/users/action/approve/{id}', [App\Http\Controllers\Agent::class, 'approve_pending_users'])->middleware('revalidate')->middleware('auth')->middleware('agent');
Route::post('/agents/transfer/credits/users/data', [App\Http\Controllers\Agent::class, 'fetch_user_data'])->middleware('revalidate')->middleware('auth')->middleware('agent');
Route::post('/agents/transfer/credits/users/edit', [App\Http\Controllers\Agent::class, 'transfer_credits'])->middleware('revalidate')->middleware('auth')->middleware('agent');
Route::post('/agents/agent/transfer', [App\Http\Controllers\Agent::class, 'transfer_credits_agent'])->middleware('revalidate')->middleware('auth')->middleware('agent');
