<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyUser as MyUser; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller {

	public function loginUser(Request $request) {

		$credentials = request(['mail', 'password']);
		
		if (Auth::attempt($credentials)) { //SUCCESSFULL LOGIN
			error_log("success");
			return redirect()->route('/mytasks');
		}

		else {
			if (MyUser::where('mail', '=', request('mail'))->exists()) {
				return response(1); //USER EXISTS, INCORRECT PASS
			}
			else {
				return response(2); //USER DOESNT EXIST
			}
		}
	}

	public function logOut(Request $request) {	

		if(Auth::user()) {
			Auth::logout();
			return response("Successfull logout");
		}
		else {
			return redirect()->route('/');
		}
	}

	public function checkAuthHp(Request $request) {	

		if(Auth::user()) {
			$currentUser = Auth::user();
			return redirect()->route('/mytasks');
		}
		else {
			return view('welcome', ['loggedUser' => null]);
		}
	}	

	public function checkAuthMyTasks(Request $request) {	

		if(Auth::user()) {
			$currentUser = Auth::user();
			return view('welcome', ['loggedUser' => "Welcome, " . $currentUser['name']]);
		}
		else {
			return redirect()->route('/');
		}
	}	
}