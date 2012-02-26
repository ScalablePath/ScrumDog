var backlog;
var lastId;
var BL_assignDialogOpened = false;
var BL_estimateDialogOpened = false;

function TaskRow(id) {
	this.id = id;
	this.formRow = $('#form_row-'+id).get(0);
	this.regRow = $(this.formRow).prev().get(0);
	//name
	this.nameInput = $('#name-'+id).get(0);
	this.name = this.nameInput.value;
	//businessValue
	this.bvSelect = $('#bv-'+id).get(0);
	this.bvIndex = this.bvSelect.selectedIndex;
	this.bvValue = this.bvSelect.options[this.bvIndex].value;
	this.bvString = this.bvSelect.options[this.bvIndex].text;
	//Estimated Hours
	this.ehInput = $('#eh-'+id).get(0);
	this.estimatedHours = this.ehInput.value;
		
	this.save = function(action) {
		if(action=='row')
		{
			//get the new data
			this.name = this.nameInput.value;
			this.bvIndex = this.bvSelect.selectedIndex;
			this.bvValue = this.bvSelect.options[this.bvIndex].value;
			this.bvString = this.bvSelect.options[this.bvIndex].text;
			this.estimatedHours = jQuery.trim(this.ehInput.value);
			//update the table
			var tdArray = $(this.regRow).children();
			$(tdArray[0]).children('a').get(0).innerHTML = this.name;
			tdArray[1].innerHTML = this.bvString;
			tdArray[2].innerHTML = this.estimatedHours;
			//make the ajax call
			var myData = 'task[id]='+this.id+'&task[name]='+encodeURIComponent(this.name)+'&task[business_value]='+this.bvValue+'&task[estimated_hours]='+this.estimatedHours;
		}
		else if(action=='assign')
		{
			var myData = 'task[id]='+this.id+'&task[sprint_id]='+this.sprintId;
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
	   			BL_taskSaveSuccess(res);
	   			if(action=='archive' || action=='assign')
	   			{
	   				BL_refresh();
	   			}
	 	  	}
		});
	};
}


function BL_taskSaveSuccess(res) {
	if(res.status=='error')
	{
		alert('Server error: Failed to save task.');
	}
}


function Backlog(filters, sort) {
	this.projectId =  $('table.backlog_table').get(0).getAttribute('id').split('-')[1];
	this.taskHash = [];
	this.filters = filters;
	this.sort = sort;
	//attach table header events
	$('#create-task-button').click(BL_createTaskPop);
	$('span.refresh_button').click(BL_refresh);
	$('span.clear_button').click(BL_clear);
	$('#sort-name').click(BL_sortName);
	$('#sort-bv').click(BL_sortBV);
	$('#filter-bv').change(BL_refresh);
	$('#sort-eh').click(BL_sortEH);
	$('#filter-eh').change(BL_refresh);
	$('#filter-name').keypress(function (e) {if (e.which == 13) BL_refresh();});
	
	BL_attachTableBodyEvents();
	this.create = function() {
		var name = encodeURIComponent($('#pop-name').get(0).value);
		var estimated_hours = $('#pop-estimated_hours').get(0).value;
		var parent_id = $('#pop-parent_id').get(0).value;
		var business_value = $('#pop-business_value').get(0).value;
		
		var myData = 'task[project_id]='+this.projectId+'&task[name]='+name+'&task[estimated_hours]='+estimated_hours+'&task[parent_id]='+parent_id+'&task[business_value]='+business_value;
//alert(myData);
		$.ajax({
	   		type: "POST",
	   		url: "/task-ajax-create",
	   		data: myData,
			dataType: 'json',
			error: function() {alert('Failed to save task. It is likely that your session has expired.');},
	   		success: function(res){
	   			BL_taskCreateSuccess(res);
	 		}
	 	});
	 }
}


function BL_attachTableBodyEvents() {
	$('span.backlog_edit').click(BL_edit);
	$('span.backlog_cancel').click(BL_cancel);
	$('span.backlog_save').click(BL_save);
	$('span.backlog_assign').click(BL_assignPop);
	$('span.backlog_archive').click(BL_archive);
	$("input.numeric").numeric(null);
}

function BL_getTask(taskId) {
	if(backlog.taskHash[taskId])
	{
		var myTask = backlog.taskHash[taskId];
	}
	else
	{
		var myTask = new TaskRow(taskId);
		backlog.taskHash[taskId] = myTask;
	}
	return myTask;
}

function BL_edit(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = BL_getTask(taskId);
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function BL_cancel(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = BL_getTask(taskId);
	//reset the form values
	myTask.nameInput.value = myTask.name;
	myTask.bvSelect.selectedIndex = myTask.bvIndex;
	myTask.ehInput.value = myTask.estimatedHours;
	$(myTask.formRow).toggleClass('hide');
	$(myTask.regRow).toggleClass('hide');
}

function BL_save(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = BL_getTask(taskId);
	myTask.save('row');
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function BL_archive(e) {
	if(confirm("Are you sure you want to send this task (and all of its subtasks) to the archive?"))
	{
		$('.backlog_table').loading();
		if (!e) var e = window.event;
		var tg = (window.event) ? e.srcElement : e.target;
		lastId = tg.getAttribute('id').split('-')[1];
		var myTask = BL_getTask(lastId);
		myTask.save('archive');
		$(myTask.regRow).toggle();
	}
}

function BL_assignPop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = BL_getTask(lastId);
	if(myTask.estimatedHours!='')
	{
		if(!BL_assignDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": BL_assign, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Select Sprint', modal: true};
			$("#assign_dialog").dialog(dialogOptions);
			BL_assignDialogOpened = true;
		}
		else
		{
			$("#assign_dialog").dialog('open');
		}
	}
	else
	{
		if(!BL_estimateDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": function() {$(this).dialog('close')}}, resizable: false, title: 'Not so fast!', modal: true};
			$("#estimate_dialog").dialog(dialogOptions);
			BL_estimateDialogOpened = true;
		}
		else
		{
			$("#estimate_dialog").dialog('open');
		}
	}	
}

function BL_assign() {
	//close the dialog
	$("#assign_dialog").dialog('close');
	$('.backlog_table').loading();
	var myTask = BL_getTask(lastId);
	var mySelect = $('#assign_select').get(0);	
	myTask.sprintId = mySelect.options[mySelect.selectedIndex].value;
	$(myTask.regRow).toggle();
	myTask.save('assign');
}

function BL_sortName(e) {
	if (!e) var e = window.event;
	BL_clearSortClasses();
	if(backlog.sort['name']=='asc')
	{
		backlog.sort = {name: 'desc'};
		$('#sort-name').parent().addClass("desc");
	}
	else
	{
		backlog.sort = {name: 'asc'};
		$('#sort-name').parent().addClass("asc");
	}
	BL_refresh();
}

function BL_sortBV() {
	BL_clearSortClasses();
	if(backlog.sort['business_value']=='desc')
	{
		backlog.sort = {business_value: 'asc'};
		$('#sort-bv').parent().addClass("asc");
	}
	else
	{
		backlog.sort = {business_value: 'desc'};
		$('#sort-bv').parent().addClass("desc");
	}
	BL_refresh();
}

function BL_sortEH() {
	BL_clearSortClasses();
	if(backlog.sort['estimated_hours']=='asc')
	{
		backlog.sort = {estimated_hours: 'desc'};
		$('#sort-eh').parent().addClass("desc");
	}
	else
	{
		backlog.sort = {estimated_hours: 'asc'};
		$('#sort-eh').parent().addClass("asc");
	}
	BL_refresh();
}

function BL_clearSortClasses() {
	$('.sortable').removeClass("desc");
	$('.sortable').removeClass("asc");
}

function BL_clear() {
	BL_clearSortClasses();
	//set the default sort
	backlog.sort = {business_value: 'desc'};
	$('#sort-bv').parent().addClass("desc");
	$('#filter-name').get(0).value = '';
	$('#filter-bv').get(0).selectedIndex = 0;
	$('#filter-eh').get(0).selectedIndex = 0;
	BL_refresh();
}

function BL_refresh() {
	$('.backlog_table').loading();
	backlog.filters = {"name": $('#filter-name').get(0).value, "business_value": $('#filter-bv').get(0).value, "estimated_hours": $('#filter-eh').get(0).value};
	var qs = '?filters='+encodeURIComponent($.toJSON(backlog.filters));
	qs += '&sort='+encodeURIComponent($.toJSON(backlog.sort));
	var myURL = "/project/"+backlog.projectId+"/backlog-body"+qs;
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function(res) {
				alert('Failed to refresh. It is likely that your session has expired.');
			},
		   success: function(res){
				$('#backlog_body').replaceWith(res);
				backlog.taskHash = [];
				BL_attachTableBodyEvents();
				$('.backlog_table').killLoading();

		   }
		 });
}

function BL_createTaskPop(e) {
	if(!createTaskDialogOpened)
	{
		var dialogOptions = {height: 270, width: 500, buttons: {"Create": BL_createTask, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Create Task', modal: true};
		var res = $("#create-task-dialog").dialog(dialogOptions);
		createTaskDialogOpened = true;
	}
	else
	{
		$("#create-task-dialog").dialog('open');
	}
}

function BL_createTask(e) {
	var tg = $('#pop-name').get(0);
	if(tg.value=='')
	{
		if($(tg.parentNode).find(".error").length==0)
		{
			var errorHTML = '<span class="error">Required</span>';
			$(tg.parentNode).append(errorHTML);
		}
	}
	else
	{
		$(tg.parentNode).find(".error").remove();
		$("#create-task-dialog").dialog('close');
		backlog.create();
		$('#pop-name').get(0).value = '';
		$('#pop-estimated_hours').get(0).value = '';
		$('#pop-parent_id').get(0).value = '';
		$('#pop-business_value').get(0).value = 1;
	}
}

function BL_taskCreateSuccess(res) {
	if(res.status=='error')
	{
		alert('Server error: Failed to save task.');
	}
	else
	{
		BL_refresh();
		var myUrl = '/create-dialog/project-backlog/'+backlog.projectId;
	}
	refreshCreateDialog(myUrl);
}


