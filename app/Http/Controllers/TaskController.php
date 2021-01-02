<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;


class TaskController extends Controller {

	public function createTask(Request $request) {

		if (request('taskdescription') == NULL) {
			$taskdescription = '/';
		}
		else {
			$taskdescription = request('taskdescription');
		}

		if (request('taskduedate') == NULL) {
			$taskduedate = '~';
		}
		else {
			$taskduedate = request('taskduedate');
		}
		
        $taskInfoArray = array (
			'taskname' => request('taskname'),
			'taskdescription' => $taskdescription,
			'taskduedate' => $taskduedate,
			'importance' => request('taskimportance'),
			'taskcolor' => request('taskcolor'),
			'status' => "Active",
			'user_id' => Auth::user()['id']
		);
		$task = Task::create($taskInfoArray);		

		if($task) {
			return response()->json([$task]); 
		}
	}

	public function deleteTask(Request $request) {

		$affectedRows = Task::where('id', request('id'))->delete();
		if($affectedRows) {
			return response($affectedRows);
		}
	}

	public function completeTask(Request $request) {

		$affectedRows = Task::where('id', request('id'))->update(['status' => "Completed"]);
		if($affectedRows) {
			return response($affectedRows);
		}
	}

	public function redoTask(Request $request) {

		$affectedRows = Task::where('id', request('id'))->update(['status' => "Active"]);
		if($affectedRows) {
			return response($affectedRows);
		}
	}

	public function displayTasks(Request $request) {

		$tasks = DB::table('tasks')->where('user_id', Auth::user()['id'])->get();
		if($tasks) {
			return response()->json([$tasks]); 
		}
	}
}