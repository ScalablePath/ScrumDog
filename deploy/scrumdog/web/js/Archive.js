var archive_backlog;
var ABL_lastId;
var ABL_assignDialogOpened = false;
var ABL_estimateDialogOpened = false;

function ArchiveTaskRow(id) {
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
			var myData = 'task[id]='+this.id+'&task[sprint_id]='+this.sprintId+'&task[is_archived]=0';
		}
//alert(myData);
		$.ajax({
		   type: "POST",
		   url: "/task-ajax-save",
		   data: myData,
			dataType: 'json',
			error: function() {alert('Failed to save task. It is likely that your session has expired.');},
		   success: function(res){
				ABL_taskSaveSuccess(res);
				if(action=='assign')
				{
					ABL_refresh();
				}
		   }
		 });
	};
}

function ABL_taskSaveSuccess(res) {
	if(res.status=='error')
	{
		alert('Server error: Failed to save task.');
	}
}

function ArchiveBacklog(filters, sort) {
	this.projectId =  $('table.backlog_table').get(0).getAttribute('id').split('-')[1];
	this.taskHash = [];
	this.filters = filters;
	this.sort = sort;
	//attach table header events
	$('span.refresh_button').click(ABL_refresh);
	$('span.clear_button').click(ABL_clear);
	$('#sort-name').click(ABL_sortName);
	$('#sort-bv').click(ABL_sortBV);
	$('#filter-bv').change(ABL_refresh);
	$('#sort-eh').click(ABL_sortEH);
	$('#filter-eh').change(ABL_refresh);
	$('#filter-name').keypress(function (e) {if (e.which == 13) ABL_refresh();});
	ABL_attachTableBodyEvents();
}

function ABL_attachTableBodyEvents() {
	$('span.backlog_edit').click(ABL_edit);
	$('span.backlog_cancel').click(ABL_cancel);
	$('span.backlog_save').click(ABL_save);
	$('span.backlog_assign').click(ABL_assignPop);
	$("input.numeric").numeric(null);
}

function ABL_getTask(taskId) {
	if(archive_backlog.taskHash[taskId])
	{
		var myTask = archive_backlog.taskHash[taskId];
	}
	else
	{
		var myTask = new ArchiveTaskRow(taskId);
		archive_backlog.taskHash[taskId] = myTask;
	}
	return myTask;
}

function ABL_edit(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = ABL_getTask(taskId);
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function ABL_cancel(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = ABL_getTask(taskId);
	//reset the form values
	myTask.nameInput.value = myTask.name;
	myTask.bvSelect.selectedIndex = myTask.bvIndex;
	myTask.ehInput.value = myTask.estimatedHours;
	$(myTask.formRow).toggleClass('hide');
	$(myTask.regRow).toggleClass('hide');
}

function ABL_save(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = ABL_getTask(taskId);
	myTask.save('row');
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function ABL_assignPop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	ABL_lastId = tg.getAttribute('id').split('-')[1];
	var myTask = ABL_getTask(ABL_lastId);
	if(myTask.estimatedHours!='')
	{
		if(!ABL_assignDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": ABL_assign, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Select Sprint', modal: true};
			$("#assign_dialog").dialog(dialogOptions);
			ABL_assignDialogOpened = true;
		}
		else
		{
			$("#assign_dialog").dialog('open');
		}
	}
	else
	{
		if(!ABL_estimateDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": function() {$(this).dialog('close')}}, resizable: false, title: 'Not so fast!', modal: true};
			$("#estimate_dialog").dialog(dialogOptions);
			ABL_estimateDialogOpened = true;
		}
		else
		{
			$("#estimate_dialog").dialog('open');
		}
	}	
}

function ABL_assign() {
	//close the dialog
	$("#assign_dialog").dialog('close');
	var myTask = ABL_getTask(ABL_lastId);
	var mySelect = $('#assign_select').get(0);	
	myTask.sprintId = mySelect.options[mySelect.selectedIndex].value;
	$(myTask.regRow).toggle();
	myTask.save('assign');
}

function ABL_sortName(e) {
	if (!e) var e = window.event;
	ABL_clearSortClasses();
	if(archive_backlog.sort['name']=='asc')
	{
		archive_backlog.sort = {name: 'desc'};
		$('#sort-name').parent().addClass("desc");
	}
	else
	{
		archive_backlog.sort = {name: 'asc'};
		$('#sort-name').parent().addClass("asc");
	}
	ABL_refresh();
	e.preventDefault();
}

function ABL_sortBV() {
	ABL_clearSortClasses();
	if(archive_backlog.sort['business_value']=='desc')
	{
		archive_backlog.sort = {business_value: 'asc'};
		$('#sort-bv').parent().addClass("asc");
	}
	else
	{
		archive_backlog.sort = {business_value: 'desc'};
		$('#sort-bv').parent().addClass("desc");
	}
	ABL_refresh();
}

function ABL_sortEH() {
	ABL_clearSortClasses();
	if(archive_backlog.sort['estimated_hours']=='asc')
	{
		archive_backlog.sort = {estimated_hours: 'desc'};
		$('#sort-eh').parent().addClass("desc");
	}
	else
	{
		archive_backlog.sort = {estimated_hours: 'asc'};
		$('#sort-eh').parent().addClass("asc");
	}
	ABL_refresh();
}

function ABL_clearSortClasses() {
	$('.sortable').removeClass("desc");
	$('.sortable').removeClass("asc");
}

function ABL_clear() {
	ABL_clearSortClasses();
	//set the default sort
	archive_backlog.sort = {id: 'desc'};
	//$('#sort-bv').addClass("desc");
	$('#filter-name').get(0).value = '';
	$('#filter-bv').get(0).selectedIndex = 0;
	$('#filter-eh').get(0).selectedIndex = 0;
	ABL_refresh();
}

function ABL_refresh() {
	$('.backlog_table').loading();
	archive_backlog.filters = {"name": $('#filter-name').get(0).value, "business_value": $('#filter-bv').get(0).value, "estimated_hours": $('#filter-eh').get(0).value};
	var qs = '?filters='+encodeURIComponent($.toJSON(archive_backlog.filters));
	qs += '&sort='+encodeURIComponent($.toJSON(archive_backlog.sort));
	var myURL = "/project/"+archive_backlog.projectId+"/archive-body"+qs;
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function() {alert('Failed to refresh. It is likely that your session has expired.');},
		   success: function(res){
				$('#backlog_body').replaceWith(res);
				archive_backlog.taskHash = [];
				ABL_attachTableBodyEvents();
				$('.backlog_table').killLoading();
		   }
		 });
}


