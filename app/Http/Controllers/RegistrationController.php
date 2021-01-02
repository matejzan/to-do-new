<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class RegistrationController extends Controller {

	public function storeUserToDB(Request $request) {
	
		$this->validate(request(), [
            'name' => 'required',
            'surname' => 'required',
            'mail' => 'required|email',
            'password' => 'required',
            'registerPasswordRepeat' => 'required'
        ]);

        $credentialsArray = array (
			'name' => request('name'),
			'surname' => request('surname'),
			'mail' => request('mail'),
			'password' => Hash::make(request('password'))
		);

		$credentials = array (
			'mail' => request('mail'),
			'password' => request('password')
		);

        if(!MyUser::where('mail', '=', request('mail'))->exists()) {
			$user = MyUser::create($credentialsArray);
			Auth::attempt($credentials);
			return redirect()->route('/mytasks');
		}

		else {
			return response(1);
		}
	}
}