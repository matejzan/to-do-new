<template>
	<div class="tasks-screen-wrapper">

		<div class="task-add-wrapper">			
			<h1>Add new task:</h1>

			<div class="task-input-wrapper" ref="inputsToOpen">
			
				<input type="hidden" :value="csrfToken" name="_token"/>

				<input class="taskname" ref="taskname" name="taskname" type="text" maxlength="50" placeholder="Task name (max 50 characters)" v-model="taskname">

			    <div class="taskduedate-wrapper">
			       <p>Due date (optional):</p>
			       <input class="taskduedate" ref="taskduedate" name="taskduedate" type="date" v-model="taskduedate" placeholder="Task due date">
			    </div>

			    <div class="importance-wrapper"><p>Importance 1-5 (optional):</p>
				    <button v-if="taskimportance > 0" v-on:click="taskimportance--" class="plusminus">
				        <div class="minus-horz"></div>
				    </button>{{ taskimportance }}
				        
				    <button v-if="taskimportance < 5" v-on:click="taskimportance++" class="plusminus">
				        <div class="minus-horz"></div><div class="plus-vert"></div>
				    </button>
				</div>

			    <textarea ref="taskdescription" class="taskdescription" name="taskdescription" v-model="taskdescription" maxlength="255" placeholder="Task description (optional, max 255 characters)"></textarea>

				<p>Task color:</p>
			    <div class="colors-wrapper" ref="colorsWrapper">
			    	<div class="color orange selectedcolor" ref="orange" v-on:click="color='#c97100'" @click="selectedColor('orange')"></div>
			    	<div class="color blue" ref="blue" v-on:click="color='#214055'" @click="selectedColor('blue')"></div>
			    	<div class="color green" ref="green" v-on:click="color='#1c7d3c'" @click="selectedColor('green')"></div>
			    </div>
			   
			    <div class="task-input-buttons-wrapper">

					<div class="task-add-button-wrapper">
						<button v-bind:class="{ submitable: taskname.length }" class="addTask" @click="addNewTask"><p>Add task!</p></button>
					</div>	

				</div>
			</div> 

			<div class="status" ref="status">
				<p ref="deletedstatus">Task deleted!</p>
				<p ref="completedstatus">Task completed!</p>
				<p ref="addedstatus">Task added!</p>
				<p ref="taskmovedtoactive">Task moved to active!</p>
			</div>

			<button v-if="screenWidth < 767" class="showMore" @click="showInputs()">
				<div class="cross-vert" ref="crossVert"></div><div class="cross-horz"></div>
			</button>

		</div>

		<div class="tasks-wrapper">

			<h2 v-if="screenWidth < 1280"><b ref="shownActive" v-bind:class="{ op07: shownCompleted }" @click="toggleTab('Active')">Active</b> / <b ref="shownCompleted" v-bind:class="{ op07: shownActive }" @click="toggleTab('Completed')">Missed & Completed</b></h2>
			

			<div class="tab" ref="tabActive" v-if="screenWidth > 1279 || shownActive">
				<h1>Active tasks:</h1>
				<h2>Sort by: <b ref="duedatesort" @click="sortByDueDate" v-bind:class="{ sortActive: duedatesorting }">Due date</b>
				/ <b ref="importancesort" @click="sortByImportance" v-bind:class="{ sortActive: importancesorting }">Importance</b>
				/ <b ref="createdsort" @click="sortByCreatedAt" v-bind:class="{ sortActive: createdsorting }">Date created</b></h2>

				<p v-if="tasksActive.length == 0">No active tasks!</p>

				<ul ref="fetched-tasks" v-if="duedatesorting">

				  	<h1 v-if="tasksToday.length">Today:</h1>
				  	<li v-for="(task, index) in tasksToday" v-bind:key="task.taskid">
				  		
				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="complete-task-wrapper">
									<img src="/assets/images/check.png" @click="completeTask(index, 'tasksToday')">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteActiveTask(index, 'tasksToday')">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>
				  	</li><div class="spacing" v-if="tasksToday.length"></div>

				  	<h1 v-if="tasksTomorrow.length">Tomorrow:</h1>
				  	<li v-for="(task, index) in tasksTomorrow" v-bind:key="task.taskid">
				  		
				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="complete-task-wrapper">
									<img src="/assets/images/check.png" @click="completeTask(index, 'tasksTomorrow')">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteActiveTask(index, 'tasksTomorrow')">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>
				  	</li><div class="spacing" v-if="tasksTomorrow.length"></div>

				  	<h1 v-if="tasksThisWeek.length">Later this week:</h1>
				  	<li v-for="(task, index) in tasksThisWeek" v-bind:key="task.taskid">
				  		
				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="complete-task-wrapper">
									<img src="/assets/images/check.png" @click="completeTask(index, 'tasksThisWeek')">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteActiveTask(index, 'tasksThisWeek')">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>
				  	</li><div class="spacing" v-if="tasksThisWeek.length"></div>

				  	<h1 v-if="tasksLater.length">After this week:</h1>
				  	<li v-for="(task, index) in tasksLater" v-bind:key="task.taskid">
				  		
				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="complete-task-wrapper">
									<img src="/assets/images/check.png" @click="completeTask(index, 'tasksLater')">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteActiveTask(index, 'tasksLater')">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>
				  	</li>


				</ul>

				<ul ref="fetched-tasks" v-else-if="importancesorting">

				  	<li v-for="(task, index) in tasksActive" v-bind:key="task.taskid">
				  		
				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="complete-task-wrapper">
									<img src="/assets/images/check.png" @click="completeTask(index, 'tasksActive')">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteActiveTask(index, 'tasksActive')">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>
				  	</li>
				</ul>

				<ul ref="fetched-tasks" v-else-if="createdsorting">

				  	<li v-for="(task, index) in tasksActive" v-bind:key="task.taskid">
				  		
				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="complete-task-wrapper">
									<img src="/assets/images/check.png" @click="completeTask(index, 'tasksActive')">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteActiveTask(index, 'tasksActive')">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>
				  	</li>
				</ul>

			</div>

			<div class="tab" ref="tabCompleted" v-if="screenWidth > 1279 || shownCompleted">

				<ul ref="fetched-tasks" style="margin-top: 0px;">

					<h1>Missed tasks:</h1>

					<p v-if="tasksMissed.length == 0">No missed tasks!</p>

				  	<li v-for="(task, index) in tasksMissed" v-bind:key="task.taskid">
				  		
				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="complete-task-wrapper">
									<img src="/assets/images/check.png" @click="completeTask(index, 'tasksMissed')">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteActiveTask(index, 'tasksMissed')">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>
				  	</li>

				<h1 class="completedh1">Completed tasks:</h1>

					<p v-if="tasksCompleted.length == 0">No completed tasks!</p>

				  	<li v-for="(task, index) in tasksCompleted" v-bind:key="task.taskid">

				  		<div class="task" :ref="'task'+task.taskid" :style="{ background: task.taskcolor }">
				  			<div class="task-basic-info">
								<div class="redo-task-wrapper">
									<img src="/assets/images/redo.png" @click="redoTask(index)">
								</div>
								<div class="task-name"><p>{{ task.taskname }}</p>
								</div>
								<div class="moreinfo-task-wrapper">
									<img src="/assets/images/moreinfo.png" :ref="'moreinfoIcon'+task.taskid" @click="showTaskInfo(index, task.taskid)">
								</div>
								<div class="delete-task-wrapper">
									<img src="/assets/images/delete.png" @click="deleteCompletedTask(index)">
								</div>
							</div>
							<div class="task-more-info">
								<div class="top-line"></div>
								<p><b>Due date: </b>{{ task.taskduedate }}<br><br>
								<b>Importance: </b>{{ task.taskimportance }}<br><br>
								<b>Description: </b>{{ task.taskdescription }}</p>
							</div>			
						</div>

				  	</li>
				</ul>
			</div>
		</div>


	</div>
</template>

<script>
export default {
	data: function () {
        return {
        	tasksActive: [],
        	tasksCompleted: [],
        	tasksToday: [],
        	tasksTomorrow: [],
        	tasksThisWeek: [],
        	tasksLater: [],
        	tasksMissed: [],
        	taskid: '',
        	taskname: '',
        	taskdescription: '',
        	taskduedate: '',
        	taskfetchedduedate: '',
        	taskfetchedimportance: '',
        	taskcreatedat: '',
        	taskimportance: 0,
        	taskstatus: '',
        	taskcolor: '',
        	taskmissed: '',
        	tasksameweek: false,
        	missed: false,
        	index: '',
        	csrfToken: null,
        	inputsVisible: false,
        	shownActive: true,
        	shownCompleted: false,
        	status: '',
        	timeoutstatuses: '',
        	colorInterval: '',
        	createdsorting: false,
        	duedatesorting: true,
        	importancesorting: false,
        	color: '#c97100',
        	sameWeek: false,
        	screenWidth: '',
        	desktop: '',
        	mobile: '',
        	opened: []
	    }
    },

	mounted: function() {
		var Tasks = this;        
        Tasks.csrfToken = document.querySelector('meta[name="csrf-token"]').content;

		Tasks.ScreenWidth();

		if (Tasks.screenWidth > 766) {
			Tasks.dekstop = true;
			Tasks.showInputs();
		}
		else {
			Tasks.mobile = true;
		}

		window.addEventListener('resize', function (event) {

    		Tasks.ScreenWidth();
    		if(Tasks.screenWidth > 766) {
    			Tasks.inputsVisible = false;
    			Tasks.showInputs();
    			Tasks.desktop = true;
    			Tasks.mobile = false;
    		}
    		else if(Tasks.screenWidth < 767 && Tasks.desktop) {
    			Tasks.inputsVisible = false;
    			Tasks.showInputs();
    			Tasks.desktop = false;
    			Tasks.mobile = true;
    		}
    	});


		axios.post('displayTasks').then(function (response) { //GET REQUEST TO DISPLAY STORED TASKS
			if (response.status == 200) { //CHECK RESPONSE
				
				/*var res = response.data.split("<html>"); //PARSE DATA
				var array = (JSON.parse(res[0]));
				var arrayOfTasks = array[0];*/
				var arrayOfTasks = response.data[0]; //GIT 

				var importance;

				for (var i = 0; i < arrayOfTasks.length; i++) { //ADD DATA TO VUE MODEL

					var duedate = Tasks.getTaskDueTime(new Date(Date.parse(arrayOfTasks[i].taskduedate)), new Date()); //GET DATE TILL TASK
					if(arrayOfTasks[i].importance == 0) {
						importance = '/';
					}

					else {
						importance = arrayOfTasks[i].importance;
					}

					var task = { "taskid": arrayOfTasks[i].id, "taskname": arrayOfTasks[i].taskname, "taskdescription": arrayOfTasks[i].taskdescription, "taskduedate": duedate, "taskfetchedduedate": arrayOfTasks[i].taskduedate, "taskimportance": importance, "taskfetchedimportance": arrayOfTasks[i].importance, "taskstatus": arrayOfTasks[i].status, "taskcreatedat": arrayOfTasks[i].created_at, "taskcolor": arrayOfTasks[i].taskcolor, "taskmissed": Tasks.missed, "tasksameweek": Tasks.sameWeek};

					Tasks.opened[i] = false;

					if (task.taskstatus == "Active" && !Tasks.missed) {
						Tasks.tasksActive.unshift(task); 
					}
					else if(task.taskstatus == "Active" && Tasks.missed) {
						Tasks.tasksMissed.unshift(task); 
					}
					else {
						Tasks.tasksCompleted.unshift(task);
					}		
				}
				Tasks.sortByDueDate();
			}
		});
	},

	methods: {

		addNewTask: function() {
			var Tasks = this;
			var importance;

			if (Tasks.taskname) {

				axios.post('addtask', { //POST REQUEST TO STORE TASK TO DB
				    'taskname': this.taskname,
				    'taskdescription': this.taskdescription,
				    'taskduedate': this.taskduedate,
				    'taskimportance': this.taskimportance,
				    'taskcolor': this.color
				}).then(function(response) { //GET RESPONSE	
					if (response.status == 200) { //CHECK RESPONSE

						/*var res = response.data.split("<html>"); //PARSE DATA
						var array = (JSON.parse(res[0]));
						var createdTask = array[0];*/
						var createdTask = response.data[0]; //GIT

						var duedate = Tasks.getTaskDueTime(new Date(Date.parse(createdTask.taskduedate)), new Date()); //GET DATE TILL TASK
						
						if(createdTask.importance == 0) {
							importance = '/';
						}

						else {
							importance = createdTask.importance;
						}

						var task = { "taskid": createdTask.id, "taskname": createdTask.taskname, "taskdescription": createdTask.taskdescription, "taskduedate": duedate, "taskfetchedduedate": createdTask.taskduedate, "taskimportance": importance, "taskfetchedimportance": createdTask.importance, "taskstatus": "Active", "taskcreatedat": createdTask.created_at, "taskcolor": Tasks.color, "taskmissed": Tasks.missed, "tasksameweek": Tasks.sameWeek }; //CREATE NEW TASK WITH DATA

						if(!Tasks.missed) {
							Tasks.tasksActive.unshift(task);
						}
						else {
							Tasks.tasksMissed.unshift(task);
						}

						Tasks.taskname = ''; //RESET VALUES IN INPUT
						Tasks.taskdescription = '';
						Tasks.taskduedate = null;
						Tasks.taskimportance = 0;

						if(Tasks.duedatesorting) {
							Tasks.sortByDueDate();
						}
						else if(Tasks.importancesorting) {
							Tasks.sortByImportance();
						}
						Tasks.displayStatus('addedstatus');

					}
				});			
			}
		},

		completeTask: function(index, array) {
			var Tasks = this;
			var completedtask = Tasks.[array][index];
			axios.post('completetask', { //POST REQUEST TO COMPLETE TASK TO DB
			    'id': Tasks.[array][index].taskid
			}).then(function(response) {		
				if (response.status == 200) { //CHECK RESPONSE

					if(array == "tasksMissed") {
						Tasks.[array].splice(index, 1);
					}

					else {
						for (var i = 0; i < Tasks.tasksActive.length; i++) {
							if (Tasks.tasksActive[i].taskid == Tasks.[array][index].taskid) {
								Tasks.tasksActive.splice(i, 1);
								break;
							}
						}
					}

					Tasks.tasksCompleted.unshift(completedtask); //ADD TASK TO COMPLETED
					Tasks.displayStatus('completedstatus');
					if(Tasks.duedatesorting) {
						Tasks.sortByDueDate();
					}
				}
			});					
		},

		deleteActiveTask: function(index, array) {
			var Tasks = this;
			axios.post('deletetask', { //POST REQUEST TO STORE TASK TO DB
			    'id': Tasks.[array][index].taskid
			}).then(function(response) {				
				if (response.status == 200) { //CHECK RESPONSE

					if(array == "tasksMissed") {
						Tasks.[array].splice(index, 1);
					}
					else {
						for (var i = 0; i < Tasks.tasksActive.length; i++) {
							if (Tasks.tasksActive[i].taskid == Tasks.[array][index].taskid) {
								Tasks.tasksActive.splice(i, 1);
							}
						}
						Tasks.displayStatus('deletedstatus');
						if(Tasks.duedatesorting) {
							Tasks.sortByDueDate();
						}
					}
				}
			});			
		},

		deleteCompletedTask: function(index) {
			var Tasks = this;
			axios.post('deletetask', { //POST REQUEST TO STORE TASK TO DB
			    'id': Tasks.tasksCompleted[index].taskid
			}).then(function(response) {				
				if (response.status == 200) { //CHECK RESPONSE
					Tasks.tasksCompleted.splice(index, 1); //REMOVE TASK WITH INDEX index
					Tasks.displayStatus('deletedstatus');
				}
			});				
		},


		deleteMissedTask: function(index) {
			var Tasks = this;
			axios.post('deletetask', { //POST REQUEST TO STORE TASK TO DB
			    'id': Tasks.tasksMissed[index].taskid
			}).then(function(response) {				
				if (response.status == 200) { //CHECK RESPONSE
					Tasks.tasksMissed.splice(index, 1); //REMOVE TASK WITH INDEX index
					Tasks.displayStatus('deletedstatus');
				}
			});				
		},

		redoTask: function(index) {
			var Tasks = this;
			axios.post('redotask', { //POST REQUEST TO STORE TASK TO DB
			    'id': Tasks.tasksCompleted[index].taskid
			}).then(function(response) {				
				if (response.status == 200) { //CHECK RESPONSE
					Tasks.tasksActive.unshift(Tasks.tasksCompleted[index]);
					Tasks.tasksCompleted.splice(index, 1); //REMOVE TASK WITH INDEX index					
					Tasks.displayStatus('taskmovedtoactive');
					if(Tasks.duedatesorting) {
						Tasks.sortByDueDate();
					}
				}
			});				
		},

		showTaskInfo: function(index, id) {
			var Tasks = this;
			var infoIcon = Tasks.$refs.["moreinfoIcon"+id][0];
			var taskInfo = Tasks.$refs.["task"+id][0];

			if (taskInfo.style.maxHeight == "500px") {
				taskInfo.style.maxHeight = "50px";
				infoIcon.style.transform = "rotate(0deg)";
			}
			else {
				taskInfo.style.maxHeight = "500px";
				infoIcon.style.transform = "rotate(180deg)";
			}
		},

		getTaskDueTime: function(dateFinish, dateToday) {
			var Tasks = this;
			var dateDifferenceInDays = (dateFinish.getDate() - dateToday.getDate());
			var dateDifferenceInMonths = (dateFinish.getMonth() - dateToday.getMonth());
			var dateDifferenceInYears = (dateFinish.getFullYear() - dateToday.getFullYear());
			var duedate;
			Tasks.missed = false;
			Tasks.sameWeek = false;

				if (dateDifferenceInDays < 0 && dateDifferenceInMonths <= 0 && dateDifferenceInYears <= 0 || dateDifferenceInMonths < 0 && dateDifferenceInYears <= 0 || dateDifferenceInYears < 0) {
					Tasks.missed = true;
				}
				if (dateFinish == 'Invalid Date') {
					duedate = '/';
				}
				else if (dateDifferenceInDays == 0) {
					duedate = "Today";
				}
				else if (dateDifferenceInDays == 1) {
					duedate = "Tomorrow";
				}
				else if (dateDifferenceInDays == -1) {
					duedate = "Yesterday";
				}
				else {
					duedate = dateFinish;
					duedate = String(duedate);
					duedate = duedate.split(" ", 3);
					duedate = duedate[0].concat(', ', duedate[1],' ', duedate[2]);
					
					if((dateFinish.getDay() - dateToday.getDay()) == dateDifferenceInDays || dateFinish.getDay() == 0 && (dateFinish.getTime() - dateToday.getTime()) < 604800000 ) {
						Tasks.sameWeek = true;
					}
				}
				return duedate;
		},

		sortByImportance: function() {
			var Tasks = this;
			
			Tasks.createdsorting = false;
			Tasks.duedatesorting = false;
			Tasks.importancesorting = true;

			Tasks.tasksActive.sort(function(a, b) {
			  return b.taskfetchedimportance - a.taskfetchedimportance;
			});
		},

		sortByDueDate: function() {
			var Tasks = this;

			Tasks.tasksToday.splice(0, Tasks.tasksToday.length);
			Tasks.tasksTomorrow.splice(0, Tasks.tasksTomorrow.length);
			Tasks.tasksThisWeek.splice(0, Tasks.tasksThisWeek.length);
			Tasks.tasksLater.splice(0, Tasks.tasksLater.length);

			Tasks.importancesorting = false;
			Tasks.createdsorting = false;
			Tasks.duedatesorting = true;			
			
			Tasks.tasksActive.sort(function(a, b) {	 
				var nameA = a.taskfetchedduedate.toUpperCase();
				var nameB = b.taskfetchedduedate.toUpperCase();
				if (nameA < nameB) {
				    return -1;
				}
				if (nameA > nameB) {
				    return 1;
				}
				return 0;
			});

			for (var i = 0; i < Tasks.tasksActive.length; i++) {

				if (Tasks.tasksActive[i].taskduedate == "Today") {
					Tasks.tasksToday.push(Tasks.tasksActive[i]);
				}
				else if (Tasks.tasksActive[i].taskduedate == "Tomorrow") {
					Tasks.tasksTomorrow.push(Tasks.tasksActive[i]);
				}
				else if (Tasks.tasksActive[i].tasksameweek) {
					Tasks.tasksThisWeek.push(Tasks.tasksActive[i]);
				}
				else {
					Tasks.tasksLater.push(Tasks.tasksActive[i]);
				}
			}
		},

		sortByCreatedAt: function() {
			var Tasks = this;

			Tasks.importancesorting = false;			
			Tasks.duedatesorting = false;
			Tasks.createdsorting = true;

			Tasks.tasksActive.sort(function(a, b) {	 
				var nameA = a.taskcreatedat.toUpperCase();
				var nameB = b.taskcreatedat.toUpperCase();
				if (nameA < nameB) {
				    return 1;
				}
				if (nameA > nameB) {
				    return -1;
				}
				return 0;
			});
		},

		displayStatus: function(status) {
			var Tasks = this;
			var statuses = Tasks.$refs.status.children;
			var displayStatus = Tasks.$refs.[status];

			for(var i = 0; i < statuses.length; i++) { //HIDE ALL STATUSES
				statuses[i].style.opacity = 0;
				statuses[i].style.zIndex = 1;
			}

			displayStatus.style.opacity = 1; //DISPLAY CURRENT STATUS
			displayStatus.style.zIndex = 2;

			clearTimeout(Tasks.timeoutstatuses); //PREVENT TIMING OUT
			Tasks.timeoutstatuses = setTimeout(function(){
				displayStatus.style.opacity = 0;
				displayStatus.style.zIndex = 1;
			},1500);

		},

		toggleTab: function(tab) {
			var Tasks=this;
			if(Tasks.shownActive && tab == "Completed") {
				Tasks.shownActive = false;
				Tasks.shownCompleted = true;
			}
			else if(Tasks.shownCompleted && tab == "Active") {
				Tasks.shownCompleted = false;
				Tasks.shownActive = true;
			}
		},

		selectedColor: function(color) {
			var Tasks = this;
			var colors = Tasks.$refs.colorsWrapper.children;
			for(var i = 0; i < colors.length; i++) {
				colors[i].classList.remove('selectedcolor');
			}
			Tasks.$refs.[color].classList.add('selectedcolor');
		},

		ScreenWidth: function() {
			var Tasks = this;
		    Tasks.screenWidth = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0)
		},

		showInputs: function() {
			var Tasks=this;
			if(!Tasks.inputsVisible) {
				Tasks.$refs.inputsToOpen.classList.add("shownInputs");
				Tasks.$refs.crossVert.style.opacity="0";
				Tasks.inputsVisible = true;
			}

			else {
				Tasks.$refs.inputsToOpen.classList.remove("shownInputs");
				Tasks.$refs.crossVert.style.opacity="1";
				Tasks.inputsVisible = false;
			}
		}
	}
};
</script>


<style scoped>

	.shownInputs {
		max-height: 700px !important;
		padding: 25px 25px 20px 25px !important;
	}

	li::marker {
		font-size: 0px;
	}

	ul {
		padding: 0px !important;
		overflow-y: scroll;
	}

	ul::-webkit-scrollbar {
	    display: none;
	}

	ul {
	  -ms-overflow-style: none;  
	  scrollbar-width: none; 
	  width: 100%;
	}

	ul h1 {
		font-size: 20px;
	}

	input {
		border-radius: 0px;
		background: #E3F1EC;
	}

	textarea {
		background: #E3F1EC;
	}

	.spacing {
		height: 1px;
	}

	.completedh1 {
		margin-top: 50px !important;
	}

	.plusminus {
		width: 26px;
	    height: 26px;
	    background: none;
	    border-radius: 13px;
	    border: 2px solid #212f3c;
	    display: flex;
	    margin: 0px 5px;
	    justify-content: center;
	    align-items: center;
	    position: relative;
	    cursor: pointer;
	    opacity: 0.7;
	    transition: 0.1s ease-in-out;
	    -webkit-transition: 0.1s ease-in-out;
	    -o-transition: 0.1s ease-in-out;
	    -moz-transition: 0.1s ease-in-out;
	}

	.plusminus:hover {
        opacity: 1;
	}

	.plus-vert {
		height: 10px;
		width: 2px;
		background: #212f3c;
		position: absolute;
		opacity: 1;
		border-radius: 1px;
	}

	.minus-horz {
		position: absolute;
		height: 2px;
		width: 10px;
		border-radius: 1px;
		background: #212f3c;
	}

	.status {
		width: 250px;
		height: 60px;
    	display: flex;
    	position: absolute;
    	bottom: 50px;
    	justify-content: center;
	}

	.status p {
		position: absolute;
		background: #53967e;
		color: white;
		font-size: 16px;
		padding: 10px 20px;
		border-radius: 10px;
		z-index: 1;
		opacity: 0;
		transition: 0.1s ease-in-out;
        -webkit-transition: 0.1s ease-in-out;
        -o-transition: 0.1s ease-in-out;
        -moz-transition: 0.1s ease-in-out;
        cursor: default;
	}

	.empty {
		z-index: 1 !important;
		opacity: 1 !important;
	}

	.tab {
		position: relative;
		display: inline-flex;
		width: 50%;
		border-left: 3px solid white;
		flex-direction: column;
		align-items: center;
		padding-top: 40px;
		box-sizing: border-box;
	}

	.tab h1 {
		font-size: 20px;
		font-weight: 500;
		text-align: center;
		color: white;
		margin-bottom: 20px;
	}

	.tab h2{
		font-family: Helvetica;
		font-size: 16px !important;
		font-weight: 500;
		color: white;
		cursor: default;
		margin: 0.5em 0px;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.tab b {
		color: white;
		font-weight: 500;
		opacity: 0.7;
		cursor: pointer;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.tab p{
		font-family: Helvetica;
		font-size: 16px;
		color: white;
		cursor: default;
		text-align: center;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.showButton {
		background: none;
		height: 30px;
	}

	.active {
		opacity: 1;
	}

	.task-basic-info {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 50px;
		box-sizing: border-box;
	}

	.task-more-info {
		width: 100%;
		position: relative;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	.task-more-info p {
		box-sizing: border-box;
		text-align: left;
		padding: 10px;
		margin: 5px 0px;
		width: 100%;
		font-size: 16px !important;
		opacity: 1 !important;
	}

	.task-more-info b {
		font-size: 16px !important;
		opacity: 1 !important;
		font-weight: 700;
	}

	.sortActive {
		opacity: 1 !important;
	}

	.top-line {
		width: calc(100% - 20px);
		height: 2px;
		background: white;
	}

	.task {
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		position: relative;
		width: 70%;
		max-height: 50px;
		border-radius: 15px;
		box-sizing: border-box;
		overflow: hidden;
		transition: max-height 0.25s ease-in-out;
        -webkit-transition: max-height 0.25s ease-in-out;
        -o-transition: max-height 0.25s ease-in-out;
        -moz-transition: max-height 0.25s ease-in-out;
        margin: 15px 15%;
        box-shadow: 0px 0px 4px 1px rgba(0, 0, 0, 0.2)
	}

	.opened {
		max-height: 500px;
	}

	.complete-task-wrapper {
		width: 10%;
		display: inline-flex;
		padding-left: 10px;
		box-sizing: border-box;
	}

	.complete-icon {
		width: 24px;
		height: 24px;
		border-radius: 12px;
		border: 3px solid white;
		box-sizing: border-box;
		opacity: 0.8;
		background: none;
	}

	.complete-task-wrapper img {
		height: 20px;
		opacity: 0.8;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.complete-task-wrapper img:hover{
		cursor: pointer;
		opacity: 1;
	}

	.delete-task-wrapper {
		width: 10%;
		display: inline-flex;
		padding-right: 10px;
		box-sizing: border-box;
	}

	.delete-task-wrapper img {
		height: 20px;
		opacity: 0.8;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.delete-task-wrapper img:hover {
		cursor: pointer;
		opacity: 1;
	}

	.moreinfo-task-wrapper {
		width: 10%;
		display: inline-flex;
		justify-content: flex-end;
		padding-right: 10px;
		box-sizing: border-box;
	}

	.moreinfo-task-wrapper img {
		width: 25px;
		opacity: 0.8;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.moreinfo-task-wrapper img:hover {
		cursor: pointer;
		opacity: 1;
	}

	.redo-task-wrapper {
		width: 10%;
		display: inline-flex;
		padding-left: 10px;
		box-sizing: border-box;
	}

	.redo-task-wrapper img {
		height: 25px;
		opacity: 0.8;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.redo-task-wrapper img:hover {
		cursor: pointer;
		opacity: 1;
	}

	.task-name {
		width: 70%;
		height: 50px;
		display: inline-flex;
		justify-content: center;
		align-items: center;
		padding-left: 15px;
		overflow: hidden;
	}

	.taskname {
		padding: 0px 15px;
		height: 40px;
		margin: 5px 0px;
		box-shadow: 0px 0px 4px 1px rgba(0, 0, 0, 0.2);
	}

	.task-name p {
		font-family: Helvetica;
		font-size: 16px;
		width: 100%;
		padding-right: 10px;
		color: white !important;
		cursor: default !important;
		text-align: left;
	}

	.taskdescription {
		width: 100%;
		height: 80px;
		padding: 15px;
		margin: 5px 0px;
		box-shadow: 0px 0px 4px 1px rgba(0, 0, 0, 0.2);
	}

	.charcounter {
		color: white;
		font-size: 14px;
		position: relative;
		margin-top: 5px;
		margin-left: 5px;
	}

	.required {
		color: white;
	    font-size: 14px;
	    position: relative;
	    margin-top: 5px;
	    margin-left: 5px;
	}

	.colors-wrapper {
		display: flex;
		justify-content: center;
		box-sizing: border-box;
		margin: 5px 0px 30px 0px;
	}

	.color {
		display: inline-flex;
		width: 60px;
		height: 50px;
		margin: 0px 5px;		
		border: 3px solid white;
		border-radius: 3px;
		transition: 0.1s ease-in-out;
        -webkit-transition: 0.1s ease-in-out;
        -o-transition: 0.1s ease-in-out;
        -moz-transition: 0.1s ease-in-out;
	}

	.color:hover {
		border: 3px solid #666666;
		cursor: pointer;
	}

	.selectedcolor {
		border: 3px solid #666666;
	}

	.orange {
		background: #c97100;
	}

	.blue {
		background: #214055;
	}

	.green {
		background: #1c7d3c;
	}

	.tasks-screen-wrapper {
		display: flex;
		width: 100%;
		height: 100vh;
		min-height: 700px;
		background: linear-gradient(60deg, #143f60, #53967e);
		justify-content: center;
		align-items: flex-start;
		position: relative;
	}

	.task-input-wrapper {
		display: flex;
		justify-content: center;
		flex-direction: column;
		overflow: hidden;
		padding: 0px 25px;
		max-height: 0px;
		width: 70%;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.task-input-wrapper p {
		color: #262626;
		text-align: center;
		font-size: 16px;
	}

	.task-add-wrapper h1 {
		font-size: 36px;
		font-family: Helvetica;
		color: #53967e;
		text-align: center;
		margin-bottom: 10px;
	}

	.task-optional-inputs-wrapper {
		max-height: 0px;
		position: relative;
		overflow: hidden;
		transition: 0.2s ease-in-out;
        -webkit-transition: 0.2s ease-in-out;
        -o-transition: 0.2s ease-in-out;
        -moz-transition: 0.2s ease-in-out;
	}

	.task-input-buttons-wrapper {
		display: flex;
		width: 100%;
		position: relative;
		padding-top: 7px;
	}

	.task-more-button-wrapper {
		margin-left: 25%;
		width: 50%;
		display: inline-flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}

	.task-more-button-wrapper p {
		font-family: Helvetica;
		font-size: 14px;
		color: white;
		margin: 8px 0px;
		transition: 0.1s ease-in-out;
        -webkit-transition: 0.1s ease-in-out;
        -o-transition: 0.1s ease-in-out;
        -moz-transition: 0.1s ease-in-out;
	}

	.task-add-button-wrapper {
		width: 100%;
		display: inline-flex;
		justify-content: center;
		align-items: center;
	}

	.addTask {
		display: flex;
		justify-content: center;
		align-items: center;
		background: #53967e;
		height: 55px;
		padding: 0px 30px;
		opacity: 0.8;
		border-radius: 15px;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
        cursor: default;
	}

	.submitable {
		opacity: 1;
		cursor: pointer;
	}

	.submitable:hover {
		background: #356152;
	}

	.addTask p {
		font-family: Helvetica;
		font-size: 22px;
		color: white;
		margin: 0px;
		white-space: nowrap;
		transition: 0.15s ease-in-out;
        -webkit-transition: 0.15s ease-in-out;
        -o-transition: 0.15s ease-in-out;
        -moz-transition: 0.15s ease-in-out;
	}

	.showMore {
		width: 51px;
		height: 51px;
		background: #356152;
		border-radius: 25.5px;
		border: 3px solid white;
		display: flex;
		justify-content: center;
		align-items: center;
		position: relative;
		cursor: pointer;
		opacity: 0.7;
		transition: 0.1s ease-in-out;
        -webkit-transition: 0.1s ease-in-out;
        -o-transition: 0.1s ease-in-out;
        -moz-transition: 0.1s ease-in-out;
	}

	.showMore:hover {
		opacity: 1;
	}

	.cross-vert {
		height: 26px;
		width: 3px;
		background: white;
		position: absolute;
		opacity: 1;
		border-radius: 1.5px;
		transition: 0.2s ease-in-out;
        -webkit-transition: 0.2s ease-in-out;
        -o-transition: 0.2s ease-in-out;
        -moz-transition: 0.2s ease-in-out;
	}

	.cross-horz {
		position: absolute;
		height: 3px;
		width: 26px;
		border-radius: 1.5px;
		background: white;
	}

	.task-add-wrapper {
		width: calc(99.99% / 3);
		display: inline-flex;
		flex-direction: column;
		height: 100%;
		justify-content: center;
		align-items: center;
		position: relative;
		background: white;
		padding-bottom: 100px;
		box-sizing: border-box;
	}

	.tasks-wrapper {
		width: calc(99.99% / 1.5);
		display: inline-flex;
		height: 100vh;
		min-height: 700px;
		box-sizing: border-box;
		position: relative;
	}

	.tasks-wrapper h2 {
		color: white;
		font-size: 22px;
		text-align: center;
		cursor: pointer;
	}

	.op07 {
		opacity: 0.7;
	}

	.tasks-wrapper b{
		font-weight: 500 !important;
	}

	.importance-wrapper {
		display: inline-flex;
		align-items: center;
		height: 40px;
		background: #E3F1EC;
		padding: 0px 15px;
		box-sizing: border-box;
		margin: 5px 0px;
		box-shadow: 0px 0px 4px 1px rgba(0, 0, 0, 0.2);
	}

	.importance-wrapper p {
		font-family: Helvetica;
		font-size: 14px;
		color: #4d4d4d;
		margin-right: 5px;
	}

	.taskduedate-wrapper {
		display: inline-flex;
		align-items: center;
		height: 40px;
		background: #E3F1EC;
 		padding: 0px 15px;
		box-sizing: border-box;
		margin: 5px 0px;
		box-shadow: 0px 0px 4px 1px rgba(0, 0, 0, 0.2);
	}

	.taskduedate {

	}

	.taskduedate-wrapper p {
		font-family: Helvetica;
		font-size: 14px;
		color: #4d4d4d;
		margin-right: 5px;
		white-space: nowrap;
	}

	@media (max-width: 1500px) {

		.task {
			width: 80%;
			margin: 15px 10%;
		}
	}

	@media (max-width: 1280px) {

		.task-add-wrapper {
			width: 50%;
		}

		.tasks-wrapper {
			width: 50%;
			flex-direction: column;
		}

		.tab {
			width: 100%;
			border-left: none;
			height: calc(100% - 66px);
		}

		.task {
			width: 70%;
			margin: 15px 15%;
		}
	}

	@media (max-width: 1023px) {

		.task {
			width: 90%; 
			margin: 15px 5%;
		}
		
	}

	@media (max-width: 766px) {

		.tasks-screen-wrapper {
			flex-direction: column;
			height: auto;
		}

		.task-add-wrapper {
			width: 100%;
			padding-bottom: 70px;
		}

		.tasks-wrapper {
			padding-top: 40px;
			width: 100%;
			height: auto;
		}

		.task {
			width: 400px;
			margin: 15px auto;
		}

		.task-input-wrapper {
			box-sizing: border-box;
			padding: 0px 25px;
			width: 90%;
			max-height: 0px;
		}

		.status {
			bottom: 10px;
		}

		.tab {
			width: 100%;
		}

		.tasks-wrapper h2{
			margin: 0.5em 5%;
		}

		.taskduedate-wrapper {
			width: 100%;
			margin: 10px 0px 5px 0px;
		}

		.importance-wrapper {
			width: 100%;
			position: relative;
			margin: 5px 0px 10px 0px;
		}

		.tab {
			height: auto;
		}
	}

	@media (max-width: 500px) {
		.task-input-wrapper {
			box-sizing: border-box;
			width: 92%;
		}

		.task {
			width: 80%;
			margin: 12px 10%;
		}

		ul {
			width: 100%;
		}

		ul h1 {
			margin: 20px 0px 15px 4%;
		}

		.tasks-wrapper h2{
			margin: 0.5em 5%;
		}

		.moreinfo-task-wrapper img {
			
		}

		.delete-task-wrapper img {
			
		}

		.complete-task-wrapper img {
			
		}

		.redo-task-wrapper img {
			
		}

		.task-input-buttons-wrapper {
			flex-direction: column;
		}

		.task-add-button-wrapper {
			width: 100%;
			justify-content: center;
			margin-top: 15px;
		}
	}

	@media(max-width: 400px) {

		.task {
			width: 90%;
			margin: 12px 5%;
		}
	}

</style>