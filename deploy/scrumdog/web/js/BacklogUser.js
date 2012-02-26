var user_backlog;
var lastId;
var UBL_removeDialogOpened = false;
var UBL_assignDialogOpened = false;

function UBL_TaskRow(id) {
	this.id = id;
	this.formRow = $('#form_row-'+id).get(0);
	this.regRow = $(this.formRow).prev().get(0);
	//name
	this.nameInput = $('#name-'+id).get(0);
	this.name = this.nameInput.value;
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
			tdArray[2].innerHTML = this.priorityString;
			tdArray[3].innerHTML = this.statusString;
			tdArray[4].innerHTML = this.estimatedHours;
			//make the ajax call
			var myData = 'task[id]='+this.id+'&task[name]='+encodeURIComponent(this.name)+'&task[priority]='+this.priorityValue+'&task[status]='+this.statusValue+'&task[estimated_hours]='+this.estimatedHours;
		}
		else if(action=='assign')
		{
			var myData = 'task[id]='+this.id+'&task[sprint_id]='+this.sprintId;
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
				UBL_taskSaveSuccess(res);
		   }
		 });
	};
}

function UBL_taskSaveSuccess(res) {
	//alert(response.id);
	if(res.status=='error')
	{
		alert('Server error: Failed to save task.');
	}
}

function UserBacklog(filters, sort) {
	this.taskHash = [];
	this.filters = filters;
	this.sort = sort;
	//attach table header events
	$('span.refresh_button').click(UBL_refresh);
	$('span.clear_button').click(UBL_clear);
	$('#sort-name').click(UBL_sortName);
	$('#sort-proj').click(UBL_sortProj);
	$('#sort-pri').click(UBL_sortPri);
	$('#sort-status').click(UBL_sortStatus);
	$('#sort-eh').click(UBL_sortEH);
	$('#filter-name').keypress(function (e) {if (e.which == 13) UBL_refresh();});
	$('#filter-proj').change(UBL_refresh);
	$('#filter-pri').change(UBL_refresh);
	$('#filter-status').change(UBL_refresh);
	$('#filter-eh').change(UBL_refresh);
	UBL_attachTableBodyEvents();
}

function UBL_attachTableBodyEvents() {
	$('span.backlog_edit').click(UBL_edit);
	$('span.backlog_cancel').click(UBL_cancel);
	$('span.backlog_save').click(UBL_save);
	$('span.backlog_remove').click(UBL_removePop);
	$('span.backlog_assign').click(UBL_assignPop);
	$('span.backlog_archive').click(UBL_archive);
	$("input.numeric").numeric(null);
}

function UBL_getTask(taskId) {
	if(user_backlog.taskHash[taskId])
	{
		var myTask = user_backlog.taskHash[taskId];
	}
	else
	{
		var myTask = new UBL_TaskRow(taskId);
		user_backlog.taskHash[taskId] = myTask;
	}
	return myTask;
}

function UBL_edit(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = UBL_getTask(taskId);
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function UBL_cancel(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = UBL_getTask(taskId);
	//reset the form values
	myTask.nameInput.value = myTask.name;
	myTask.prioritySelect.selectedIndex = myTask.priorityIndex;
	myTask.ehInput.value = myTask.estimatedHours;
	$(myTask.formRow).toggleClass('hide');
	$(myTask.regRow).toggleClass('hide');
}

function UBL_save(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = UBL_getTask(taskId);
	myTask.save('row');
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function UBL_removePop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = UBL_getTask(lastId);
	if(!UBL_removeDialogOpened)
	{
		var dialogOptions = {buttons: {"Ok": UBL_remove, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Remove', modal: true};
		$("#remove_dialog").dialog(dialogOptions);
		UBL_removeDialogOpened = true;
	}
	else
	{
		$("#remove_dialog").dialog('open');
	}
}

function UBL_assignPop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = UBL_getTask(lastId);
	if(myTask.estimatedHours!='')
	{
		if(!UBL_assignDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": UBL_assign, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Select Sprint', modal: true};
			$("#assign_dialog").dialog(dialogOptions);
			UBL_assignDialogOpened = true;
		}
		else
		{
			$("#assign_dialog").dialog('open');
		}
	}
	else
	{
		if(!UBL_estimateDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": function() {$(this).dialog('close')}}, resizable: false, title: 'Not so fast!', modal: true};
			$("#estimate_dialog").dialog(dialogOptions);
			UBL_estimateDialogOpened = true;
		}
		else
		{
			$("#estimate_dialog").dialog('open');
		}
	}	
}

function UBL_assign() {
	//close the dialog
	$("#assign_dialog").dialog('close');
	var myTask = UBL_getTask(lastId);
	var mySelect = $('#assign_select').get(0);	
	myTask.sprintId = mySelect.options[mySelect.selectedIndex].value;
	$(myTask.regRow).toggle();
	myTask.save('assign');
}

function UBL_remove() {
	//close the dialog
	$("#remove_dialog").dialog('close');
	var myTask = UBL_getTask(lastId);
	$(myTask.regRow).toggle();
	myTask.save('remove');
}

function UBL_archive(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = UBL_getTask(lastId);
	myTask.save('archive');
	$(myTask.regRow).toggle();
}

function UBL_sortName(e) {
	if (!e) var e = window.event;
	UBL_clearSortClasses();
	if(user_backlog.sort['name']=='asc')
	{
		user_backlog.sort = {name: 'desc'};
		$('#sort-name').parent().addClass("desc");
	}
	else
	{
		user_backlog.sort = {name: 'asc'};
		$('#sort-name').parent().addClass("asc");
	}
	UBL_refresh();
}

function UBL_sortPri(e) {
	if (!e) var e = window.event;
	UBL_clearSortClasses();
	if(user_backlog.sort['priority']=='desc')
	{
		user_backlog.sort = {priority: 'asc'};
		$('#sort-pri').parent().addClass("asc");
	}
	else
	{
		user_backlog.sort = {priority: 'desc'};
		$('#sort-pri').parent().addClass("desc");
	}
	UBL_refresh();
}

function UBL_sortProj(e) {
	if (!e) var e = window.event;
	UBL_clearSortClasses();
	if(user_backlog.sort['project_id']=='asc')
	{
		user_backlog.sort = {project_id: 'desc'};
		$('#sort-proj').parent().addClass("desc");
	}
	else
	{
		user_backlog.sort = {project_id: 'asc'};
		$('#sort-proj').parent().addClass("asc");
	}
	UBL_refresh();
}

function UBL_sortStatus(e) {
	if (!e) var e = window.event;
	UBL_clearSortClasses();
	if(user_backlog.sort['status']=='asc')
	{
		user_backlog.sort = {status: 'desc'};
		$('#sort-status').parent().addClass("desc");
	}
	else
	{
		user_backlog.sort = {status: 'asc'};
		$('#sort-status').parent().addClass("asc");
	}
	UBL_refresh();
}

function UBL_sortEH() {
	UBL_clearSortClasses();
	if(user_backlog.sort['estimated_hours']=='asc')
	{
		user_backlog.sort = {estimated_hours: 'desc'};
		$('#sort-eh').parent().addClass("desc");
	}
	else
	{
		user_backlog.sort = {estimated_hours: 'asc'};
		$('#sort-eh').parent().addClass("asc");
	}
	UBL_refresh();
}

function UBL_clearSortClasses() {
	$('.sortable').removeClass("desc");
	$('.sortable').removeClass("asc");
}

function UBL_clear() {
	UBL_clearSortClasses();
	//set the default sort
	user_backlog.sort = {priority: 'desc'};
	$('#sort-pri').parent().addClass("desc");
	$('#filter-name').get(0).value = '';
	$('#filter-pri').get(0).selectedIndex = 0;
	$('#filter-status').get(0).selectedIndex = 1;
	$('#filter-eh').get(0).selectedIndex = 0;
	UBL_refresh();
}

function UBL_refresh() {
	$('.backlog_table').loading();
	user_backlog.filters = {"name": $('#filter-name').get(0).value, "priority": $('#filter-pri').get(0).value, "status": $('#filter-status').get(0).value, "estimated_hours": $('#filter-eh').get(0).value, "project_id": $('#filter-proj').get(0).value};
	var qs = '?filters='+encodeURIComponent($.toJSON(user_backlog.filters));
	qs += '&sort='+encodeURIComponent($.toJSON(user_backlog.sort));
	var myURL = "/member/backlog-body"+qs;
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function() {alert('Failed to refresh. It is likely that your session has expired.');},
		   success: function(res){
				$('#backlog_body').replaceWith(res);
				user_backlog.taskHash = [];
				UBL_attachTableBodyEvents();
				$('.backlog_table').killLoading();

		   }
		 });
}

function UBL_taskSaveSuccess(res) {
	if(res.status=='error')
	{
		alert(res.message);
		UBL_refresh();
	}
}