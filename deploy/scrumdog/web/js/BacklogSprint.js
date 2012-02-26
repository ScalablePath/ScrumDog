var sprint_backlog;
var lastId;
var SBL_assignDialogOpened = false;
var SBL_estimateDialogOpened = false;
var taskSaveSuccessFunc;

function SprintTaskRow(id) {
	this.id = id;
	this.formRow = $('#form_row-'+id).get(0);
	this.regRow = $(this.formRow).prev().get(0);
	//name
	this.nameInput = $('#name-'+id).get(0);
	this.name = this.nameInput.value;
	//user
	this.userSelect = $('#user-'+id).get(0);
	this.userIndex = this.userSelect.selectedIndex;
	this.userValue = this.userSelect.options[this.userIndex].value;
	this.userString = this.userSelect.options[this.userIndex].text;
	//priority
	this.prioritySelect = $('#pri-'+id).get(0);
	this.priorityIndex = this.prioritySelect.selectedIndex;
	this.priorityValue = this.prioritySelect.options[this.priorityIndex].value;
	this.priorityString = this.prioritySelect.options[this.priorityIndex].text;
	//status
	this.statusSelect = $('#status-'+id).get(0);
	this.statusIndex = this.statusSelect.selectedIndex;
	this.statusValue = this.statusSelect.options[this.statusIndex].value;
	this.statusString = this.statusSelect.options[this.statusIndex].text;
	//Estimated Hours
	this.ehInput = $('#eh-'+id).get(0);
	this.estimatedHours = this.ehInput.value;
	this.save = function(action) {
		if(action=='row')
		{
			//get the new data
			this.name = this.nameInput.value;
			this.userIndex = this.userSelect.selectedIndex;
			this.userValue = this.userSelect.options[this.userIndex].value;
			this.userString = this.userSelect.options[this.userIndex].text;
			this.priorityIndex = this.prioritySelect.selectedIndex;
			this.priorityValue = this.prioritySelect.options[this.priorityIndex].value;
			this.priorityString = this.prioritySelect.options[this.priorityIndex].text;
			this.statusIndex = this.statusSelect.selectedIndex;
			this.statusValue = this.statusSelect.options[this.statusIndex].value;
			this.statusString = this.statusSelect.options[this.statusIndex].text;
			this.estimatedHours = jQuery.trim(this.ehInput.value);
			//update the table
			var tdArray = $(this.regRow).children();
			$(tdArray[0]).children('a').get(0).innerHTML = this.name;
			tdArray[1].innerHTML = this.userString;
			tdArray[2].innerHTML = this.priorityString;
			tdArray[3].innerHTML = this.statusString;
			tdArray[4].innerHTML = this.estimatedHours;
			//make the ajax call
			var myData = 'task[id]='+this.id+'&task[name]='+encodeURIComponent(this.name)+'&task[user_id]='+this.userValue+'&task[priority]='+this.priorityValue+'&task[status]='+this.statusValue+'&task[estimated_hours]='+this.estimatedHours;
		}
		else if(action=='assign')
		{
			var myData = 'task[id]='+this.id+'&task[sprint_id]='+this.sprintId;
			if(myObj && myObj.is_archived)
			{
				myData = myData + '&task[is_archived]=0';
			}
		}
		else if(action=='remove')
		{
			var myData = 'task[id]='+this.id+'&task[sprint_id]=';
		}
		else if(action=='archive')
		{
			var myData = 'task[id]='+this.id+'&task[sprint_id]=&task[is_archived]=1';
		}
//alert(myData);
		$.ajax({
		   type: "POST",
		   url: "/task-ajax-save",
		   data: myData,
			dataType: 'json',
			error: function() {alert('Failed to save task. It is likely that your session has expired.');},
		   success: function(res){
				myString = taskSaveSuccessFunc+'(action, res)';
				eval(myString);
		   }
		 });
	};
}

function SBL_taskSaveSuccess(action, res) {
	if(res.status=='error')
	{
		alert('Server error: Failed to save task.');
	}
	if(action=='archive' || action=='assign')
	{
		SBL_refresh();
	}
}

function SprintBacklog(filters, sort) {
	this.sprintId =  $('table.backlog_table').get(0).getAttribute('id').split('-')[1];
	this.taskHash = [];
	this.filters = filters;
	this.sort = sort;
	//attach table header events
	$('#create-task-button').click(SBL_createTaskPop);
	$('span.refresh_button').click(SBL_refresh);
	$('span.clear_button').click(SBL_clear);
	$('#sort-name').click(SBL_sortName);
	$('#sort-user').click(SBL_sortUser);
	$('#sort-pri').click(SBL_sortPri);
	$('#sort-status').click(SBL_sortStatus);
	$('#sort-eh').click(SBL_sortEH);
	$('#filter-name').keypress(function (e) {if (e.which == 13) SBL_refresh();});
	$('#filter-user').change(SBL_refresh);
	$('#filter-pri').change(SBL_refresh);
	$('#filter-status').change(SBL_refresh);
	$('#filter-eh').change(SBL_refresh);
	SBL_attachTableBodyEvents();
	this.create = function() {
		var name = encodeURIComponent($('#pop-name').get(0).value);
		var estimated_hours = $('#pop-estimated_hours').get(0).value;
		var parent_id = $('#pop-parent_id').get(0).value;
		var user_id = $('#pop-user_id').get(0).value;
		var status = $('#pop-status').get(0).value;
		var priority = $('#pop-priority').get(0).value;
		
		var myData = 'task[sprint_id]='+this.sprintId+'&task[name]='+name+'&task[estimated_hours]='+estimated_hours+'&task[parent_id]='+parent_id+'&task[user_id]='+user_id+'&task[status]='+status+'&task[priority]='+priority;
//alert(myData);
		$.ajax({
	   		type: "POST",
	   		url: "/task-ajax-create",
	   		data: myData,
			dataType: 'json',
			error: function() {alert('Failed to save task. It is likely that your session has expired.');},
	   		success: function(res){
	   			SBL_taskCreateSuccess(res);
	 		}
	 	});
	 }
}

function SBL_attachTableBodyEvents() {
	$('span.backlog_edit').click(SBL_edit);
	$('span.backlog_cancel').click(SBL_cancel);
	$('span.backlog_save').click(SBL_save);
	//$('span.backlog_remove').click(SBL_removePop);
	$('span.backlog_assign').click(SBL_assignPop);
	$('span.backlog_archive').click(SBL_archive);
	$("input.numeric").numeric(null);
}

function SBL_getTask(taskId) {
	if(sprint_backlog.taskHash[taskId])
	{
		var myTask = sprint_backlog.taskHash[taskId];
	}
	else
	{
		var myTask = new SprintTaskRow(taskId);
		sprint_backlog.taskHash[taskId] = myTask;
	}
	return myTask;
}

function SBL_edit(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = SBL_getTask(taskId);
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function SBL_cancel(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = SBL_getTask(taskId);
	//reset the form values
	myTask.nameInput.value = myTask.name;
	myTask.userSelect.selectedIndex = myTask.userIndex;
	myTask.prioritySelect.selectedIndex = myTask.priorityIndex;
	myTask.ehInput.value = myTask.estimatedHours;
	$(myTask.formRow).toggleClass('hide');
	$(myTask.regRow).toggleClass('hide');
}

function SBL_save(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = SBL_getTask(taskId);
	myTask.save('row');
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function SBL_assignPop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = SBL_getTask(lastId);
	if(myTask.estimatedHours!='')
	{
		if(!SBL_assignDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": SBL_assign, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Select Sprint', modal: true};
			$("#assign_dialog").dialog(dialogOptions);
			SBL_assignDialogOpened = true;
		}
		else
		{
			$("#assign_dialog").dialog('open');
		}
	}
	else
	{
		if(!SBL_estimateDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": function() {$(this).dialog('close')}}, resizable: false, title: 'Not so fast!', modal: true};
			$("#estimate_dialog").dialog(dialogOptions);
			SBL_estimateDialogOpened = true;
		}
		else
		{
			$("#estimate_dialog").dialog('open');
		}
	}	
}

function SBL_assign() {
	//close the dialog
	$("#assign_dialog").dialog('close');
	var myTask = SBL_getTask(lastId);
	var mySelect = $('#assign_select').get(0);	
	myTask.sprintId = mySelect.options[mySelect.selectedIndex].value;
	$(myTask.regRow).toggle();
	myTask.save('assign');
}

function SBL_archive(e) {
	if(confirm("Are you sure you want to send this task (and all of its subtasks) to the archive?"))
	{
		if (!e) var e = window.event;
		var tg = (window.event) ? e.srcElement : e.target;
		lastId = tg.getAttribute('id').split('-')[1];
		var myTask = SBL_getTask(lastId);
		myTask.save('archive');
		$(myTask.regRow).toggle();
	}
}

function SBL_sortName(e) {
	if (!e) var e = window.event;
	SBL_clearSortClasses();
	if(sprint_backlog.sort['name']=='asc')
	{
		sprint_backlog.sort = {name: 'desc'};
		$('#sort-name').parent().addClass("desc");
	}
	else
	{
		sprint_backlog.sort = {name: 'asc'};
		$('#sort-name').parent().addClass("asc");
	}
	SBL_refresh();
}

function SBL_sortUser(e) {
	if (!e) var e = window.event;
	SBL_clearSortClasses();
	if(sprint_backlog.sort['user_id']=='asc')
	{
		sprint_backlog.sort = {user_id: 'desc'};
		$('#sort-user').parent().addClass("desc");
	}
	else
	{
		sprint_backlog.sort = {user_id: 'asc'};
		$('#sort-user').parent().addClass("asc");
	}
	SBL_refresh();
}

function SBL_sortPri(e) {
	if (!e) var e = window.event;
	SBL_clearSortClasses();
	if(sprint_backlog.sort['priority']=='desc')
	{
		sprint_backlog.sort = {priority: 'asc'};
		$('#sort-pri').parent().addClass("asc");
	}
	else
	{
		sprint_backlog.sort = {priority: 'desc'};
		$('#sort-pri').parent().addClass("desc");
	}
	SBL_refresh();
}

function SBL_sortStatus(e) {
	if (!e) var e = window.event;
	SBL_clearSortClasses();
	if(sprint_backlog.sort['status']=='asc')
	{
		sprint_backlog.sort = {status: 'desc'};
		$('#sort-status').parent().addClass("desc");
	}
	else
	{
		sprint_backlog.sort = {status: 'asc'};
		$('#sort-status').parent().addClass("asc");
	}
	SBL_refresh();
}

function SBL_sortEH() {
	SBL_clearSortClasses();
	if(sprint_backlog.sort['estimated_hours']=='asc')
	{
		sprint_backlog.sort = {estimated_hours: 'desc'};
		$('#sort-eh').parent().addClass("desc");
	}
	else
	{
		sprint_backlog.sort = {estimated_hours: 'asc'};
		$('#sort-eh').parent().addClass("asc");
	}
	SBL_refresh();
}

function SBL_clearSortClasses() {
	$('.sortable').removeClass("desc");
	$('.sortable').removeClass("asc");
}

function SBL_clear() {
	SBL_clearSortClasses();
	//set the default sort
	sprint_backlog.sort = {priority: 'desc'};
	$('#sort-pri').parent().addClass("desc");
	$('#filter-name').get(0).value = '';
	$('#filter-user').get(0).selectedIndex = 0;
	$('#filter-pri').get(0).selectedIndex = 0;
	$('#filter-status').get(0).selectedIndex = 2;
	$('#filter-eh').get(0).selectedIndex = 0;
	SBL_refresh();
}

function SBL_refresh() {
	$('.backlog_table').loading();
	sprint_backlog.filters = {"name": $('#filter-name').get(0).value, "user_id": $('#filter-user').get(0).value, "priority": $('#filter-pri').get(0).value, "status": $('#filter-status').get(0).value, "estimated_hours": $('#filter-eh').get(0).value};
	var qs = '?filters='+encodeURIComponent($.toJSON(sprint_backlog.filters));
	qs += '&sort='+encodeURIComponent($.toJSON(sprint_backlog.sort));
	var myURL = "/sprint/"+sprint_backlog.sprintId+"/backlog-body"+qs;
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function() {alert('Failed to refresh. It is likely that your session has expired.');},
		   success: function(res){
				$('#backlog_body').replaceWith(res);
				sprint_backlog.taskHash = [];
				SBL_attachTableBodyEvents();
				$('.backlog_table').killLoading();
		   }
		 });
}

function SBL_createTaskPop(e) {
	if(!createTaskDialogOpened)
	{
		var dialogOptions = {height: 305, width: 500, buttons: {"Create": SBL_createTask, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Create Task', modal: true};
		$("#create-task-dialog").dialog(dialogOptions);
		createTaskDialogOpened = true;
	}
	else
	{
		$("#create-task-dialog").dialog('open');
	}
}

function SBL_createTask(e) {
	var tg = $('#pop-name').get(0);
	var tg2 = $('#pop-estimated_hours').get(0);
	if(tg.value=='' || tg2.value=='')
	{
		var errorHTML = '<span class="error">Required</span>';
		if(tg.value=='')
		{
			if($(tg.parentNode).find(".error").length==0)
			{
				$(tg.parentNode).append(errorHTML);
			}
		}
		else
		{
			$(tg.parentNode).find(".error").remove();
		}
		
		if(tg2.value=='')
		{
			if($(tg2.parentNode).find(".error").length==0)
			{	
				$(tg2.parentNode).append(errorHTML);
			}
		}
		else
		{
			$(tg2.parentNode).find(".error").remove();
		}
	}
	else
	{
		$(tg.parentNode).find(".error").remove();
		$(tg2.parentNode).find(".error").remove();
		$("#create-task-dialog").dialog('close');
		sprint_backlog.create();
		$('#pop-name').get(0).value = '';
		$('#pop-estimated_hours').get(0).value = '';
		$('#pop-parent_id').get(0).value = '';
		$('#pop-user_id').get(0).value = '';
		$('#pop-status').get(0).value = 0;
		$('#pop-priority').get(0).value = 1;
	}
}

function SBL_taskCreateSuccess(res) {
	if(res.status=='error')
	{
		alert(res.message);
	}
	else
	{
		var myUrl = '/create-dialog/sprint-backlog/'+sprint_backlog.sprintId;
		refreshCreateDialog(myUrl);
	}
	SBL_refresh();
}

function SBL_taskSaveSuccess(action, res) {
	if(res.status=='error')
	{
		alert(res.message);
		SBL_refresh();
	}
	else
	{
		var myUrl = '/create-dialog/sprint-backlog/'+sprint_backlog.sprintId;
		refreshCreateDialog(myUrl);
	}
}


