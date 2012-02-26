var qi, question_backlog, qc;

function Questions(hasYesterQuestion) {
	this.searchDialogOpened = false;
	this.lastWorkId;
	this.lastTaskId; 
	this.totalTasks = 0;
	this.hasYesterQuestion = hasYesterQuestion;
	this.mode = 'today';
	this.taskMode = 'add';
	this.yesterTasks = [];
	this.todayTasks = [];
	this.yesterTasksCount = 0;
	this.todayTasksCount = 0;
	this.hasHoursErrors;
	$("span.add").click(Q_addTask);
	$("input.add").click(Q_addTask);
	$("#question_save").click(Q_save);
	//$('#create-task-button').click(SBL_createTaskPop);
	Q_attachEvents();
}

function Q_attachEvents() {
	$("input.numeric").numeric(null);
	$("span.edit").click(Q_searchPop);
	$("span.delete").click(Q_deleteTask);
	$("input.hours").blur(Q_validateHours);
}

function Q_save() {
	qi.hasHoursErrors = false;
	$("input.hours").each(function() {
		if(this.value=='')
		{
			alert('You need to fill in all of your task hours.');
			qi.hasHoursErrors = true;
		}
	});
	$("input.total").each(function() {
		if(this.value=='')
		{
			if(this.id=="yester-total")
			{
				if(qi.yesterTasksCount>0 || $.trim($('#yester-work').get(0).value)!='')
				{
					alert("Since you've indicated that you did work yesterday, you need to fill in your total hours.");
					qi.hasHoursErrors = true;
				}
			}
			else if(this.id=="today-total")
			{
				if(qi.todayTasksCount>0 || $.trim($('#today-work').get(0).value)!='')
				{
					alert("Since you've indicated that you did work on this day, you need to fill in your total hours.");
					qi.hasHoursErrors = true;
				}
			}
		}
	});
	if(qi.hasHoursErrors)
		return;
	$("#question_form").get(0).submit();
}

function Q_searchPop(e) {
	if(e!='new')
	{
		qi.taskMode = 'edit';
		if (!e) var e = window.event;
		var tg = (window.event) ? e.srcElement : e.target;
		qi.lastWorkId = Q_getElementId(tg);
	}
	else
	{
		qi.taskMode = 'add';
	}
	if(!qi.searchDialogOpened)
	{
		var dialogOptions = {resizable: false, title: 'Select Task', modal: true, width: 700};
		$("#search-dialog").dialog(dialogOptions);
		QBL_resize();
		qi.searchDialogOpened = true;
	}
	else
	{
		$("#search-dialog").dialog('open');
	}
}

function Q_addTask(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	if(tg.getAttribute('id')=='yester_add')
	{
		qi.mode = 'yester';
	}
	else
	{
		qi.mode = 'today';
	}
	Q_searchPop('new');
}

function Q_addTaskRow() {
	qi.totalTasks++;
	var rowHTML = '<tr id="task-'+qi.totalTasks+'">';
	rowHTML += '<td><input style="width:40px;" id="work_task_id-'+qi.totalTasks+'" type="text" readonly name="questions['+qi.mode+'][tasks][]" value="" autocomplete="off"/></td>';
	rowHTML += '<td><input style="width:300px;" id="work_task_name-'+qi.totalTasks+'" type="text" readonly value="" autocomplete="off"/></td>';
	rowHTML += '<td><input style="width:30px;" id="work_task_hours-'+qi.totalTasks+'" type="text" class="numeric hours" name="questions['+qi.mode+'][hours][]" value="" autocomplete="off"/></td>';
	rowHTML += '<td class="actions"><span class="icon edit" title="Edit">Edit</span>&nbsp;<span class="icon delete" title="Delete">Delete</span></td>';
	rowHTML += '</tr>';
	if(qi.mode == 'yester')
	{
		qi.yesterTasks[qi.lastTaskId] = true;
		qi.yesterTasksCount++;
		$("tbody#yester_body").append(rowHTML);
	}
	else
	{
		//qi.todayTasks[qi.lastWorkId] = true;
		qi.todayTasks[qi.lastTaskId] = true;
		qi.todayTasksCount++;
		$("tbody#today_body").append(rowHTML);
		
	}
	qi.mode = 'today';
	qi.lastWorkId = qi.totalTasks;
	Q_attachEvents();
}

function Q_selectTask(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	qi.lastTaskId = Q_getElementId(tg);
	
	//check if the task already exists
	if(qi.mode=='yester')
	{
		if(qi.yesterTasks[qi.lastTaskId])
		{
			alert("You can't select the same task twice on the same day.");
			return;
		}
	}
	else
	{
		if(qi.todayTasks[qi.lastTaskId])
		{
			alert("You can't select the same task twice on the same day.");
			return;
		}
	}
	
	if(qi.taskMode=='add')
	{
		Q_addTaskRow();
	}
	
	var taskName = $("#search_row_name-" + qi.lastTaskId).get(0).innerHTML;
	$("#work_task_id-" + qi.lastWorkId).get(0).value = qi.lastTaskId;
	$("#work_task_name-" + qi.lastWorkId).get(0).value = taskName;
	$("#work_task_hours-" + qi.lastWorkId).get(0).focus();
	$("#task-" + qi.lastWorkId).find(".delete").get(0).setAttribute('id', 'delete_task-'+qi.lastTaskId);
	$("#search-dialog").dialog('close');
}

function Q_deleteTask(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = Q_getElementId(tg);
	var workId = Q_getElementId(tg.parentNode);
	var taskRow = $("#task-"+workId);
	if(taskRow.get(0).parentNode.getAttribute('id')=='yester_body')
	{
		qi.yesterTasks[taskId] = false;
		qi.yesterTasksCount--;
	}
	else
	{
		qi.todayTasks[taskId] = false;
		qi.yesterTasksCount--;
	}
	taskRow.remove();
}

//recursive
function Q_getElementId(tg)
{
	if(tg.getAttribute('id'))
	{
		return tg.getAttribute('id').split('-')[1];
	}
	else
	{
		return Q_getElementId(tg.parentNode);
	}	
}

function Q_validateHours(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
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
	}	
}

function QuestionBacklog(filters, sort) {
	this.projectId =  $('table.search-table').get(0).getAttribute('id').split('-')[1];
	this.filters = filters;
	this.sort = sort;
	this.questionUserId = null;
	//attach table header events
	$('span.refresh_button').click(QBL_refresh);
	$('span.clear_button').click(QBL_clear);
	$('#sort-name').click(QBL_sortName);
	$('#sort-user').click(QBL_sortUser);
	$('#sort-pri').click(QBL_sortPri);
	$('#sort-status').click(QBL_sortStatus);
	$('#sort-eh').click(QBL_sortEH);
	$('#filter-name').keypress(function (e) {if (e.which == 13) QBL_refresh();});
	$('#filter-user').change(QBL_refresh);
	$('#filter-pri').change(QBL_refresh);
	$('#filter-status').change(QBL_refresh);
	$('#filter-eh').change(QBL_refresh);
	QBL_attachTableBodyEvents();
}

function QBL_attachTableBodyEvents() {
	$('tr.task-search-row').click(Q_selectTask);
	$("input.numeric").numeric(null);
	$("tr.task-search-row").mouseover(function() {
		$(this).addClass('hilite');
	});
	$("tr.task-search-row").mouseout(function() {$(this).removeClass('hilite');});
}

function QBL_sortName(e) {
	if (!e) var e = window.event;
	QBL_clearSortClasses();
	if(question_backlog.sort['name']=='asc')
	{
		question_backlog.sort = {name: 'desc'};
		$('#sort-name').parent().addClass("desc");
	}
	else
	{
		question_backlog.sort = {name: 'asc'};
		$('#sort-name').parent().addClass("asc");
	}
	QBL_refresh();
}

function QBL_sortUser(e) {
	if (!e) var e = window.event;
	QBL_clearSortClasses();
	if(question_backlog.sort['user_id']=='asc')
	{
		question_backlog.sort = {user_id: 'desc'};
		$('#sort-user').parent().addClass("desc");
	}
	else
	{
		question_backlog.sort = {user_id: 'asc'};
		$('#sort-user').parent().addClass("asc");
	}
	QBL_refresh();
}

function QBL_sortPri(e) {
	if (!e) var e = window.event;
	QBL_clearSortClasses();
	if(question_backlog.sort['priority']=='desc')
	{
		question_backlog.sort = {priority: 'asc'};
		$('#sort-pri').parent().addClass("asc");
	}
	else
	{
		question_backlog.sort = {priority: 'desc'};
		$('#sort-pri').parent().addClass("desc");
	}
	QBL_refresh();
}

function QBL_sortStatus(e) {
	if (!e) var e = window.event;
	QBL_clearSortClasses();
	if(question_backlog.sort['status']=='asc')
	{
		question_backlog.sort = {status: 'desc'};
		$('#sort-status').parent().addClass("desc");
	}
	else
	{
		question_backlog.sort = {status: 'asc'};
		$('#sort-status').parent().addClass("asc");
	}
	QBL_refresh();
}

function QBL_sortEH() {
	QBL_clearSortClasses();
	if(question_backlog.sort['estimated_hours']=='asc')
	{
		question_backlog.sort = {estimated_hours: 'desc'};
		$('#sort-eh').parent().addClass("desc");
	}
	else
	{
		question_backlog.sort = {estimated_hours: 'asc'};
		$('#sort-eh').parent().addClass("asc");
	}
	QBL_refresh();
}

function QBL_clearSortClasses() {
	$('.sortable').removeClass("desc");
	$('.sortable').removeClass("asc");
}

function QBL_clear() {
	QBL_clearSortClasses();
	//set the default sort
	question_backlog.sort = {priority: 'desc'};
	$('#sort-pri').parent().addClass("desc");
	$('#filter-name').get(0).value = '';
	$('#filter-user').get(0).selectedIndex = QBL_getQuestionUserIndex();
	$('#filter-pri').get(0).selectedIndex = 0;
	$('#filter-status').get(0).selectedIndex = 1;
	//$('#filter-eh').get(0).selectedIndex = 0;
	QBL_refresh();
}

function QBL_getQuestionUserIndex() {
	var userSelect = $('#filter-user').get(0);
	for(var i=0; i<userSelect.options.length; i++)
	{
		if(userSelect.options[i].value == question_backlog.questionUserId)
			return i;
	}
}

function QBL_refresh() {
	$('.search-table').loading(); //Doesn't seem to work for some reason
	question_backlog.filters = {"priority": $('#filter-pri').get(0).value, "name": $('#filter-name').get(0).value, "user_id": $('#filter-user').get(0).value, "status": $('#filter-status').get(0).value};
	var qs = '?filters='+encodeURIComponent($.toJSON(question_backlog.filters));
	qs += '&sort='+encodeURIComponent($.toJSON(question_backlog.sort));
	qs += '&question-user-id='+question_backlog.questionUserId;
	var myURL = "/project/"+question_backlog.projectId+"/search-table-body"+qs;
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function() {alert('Failed to refresh. It is likely that your session has expired.');},
		   success: function(res){
				$('#backlog_body').replaceWith(res);
				question_backlog.taskHash = [];
				QBL_attachTableBodyEvents();
				$('.search-table').killLoading();
				QBL_resize();
		   }
		 });
}

function QBL_resize() {
	var thCollection = $('#backlog_table-'+question_backlog.projectId+' th');
	var bodyCollection = $('#backlog_body td');
	for(var i=0; i<thCollection.length-1; i++)
	{
		var myWidth = $(thCollection[i]).width();
		$(bodyCollection[i]).width(myWidth);
	}
}

function QuestionCalendar() {
	var username, selectedDay, month;
	QC_attachEvents();
}

function QC_attachEvents() {
	$('th#cal_prev').click(QC_scrollMonth);
	$('th#cal_next').click(QC_scrollMonth);
	$('.data').tooltip();
}

function QC_scrollMonth(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	qc.month = tg.getAttribute('class');
	QC_refresh();
}

function QC_refresh() {
	$('#question-calendar').loading();
	var myURL = "/project/"+question_backlog.projectId+"/calendar/"+qc.username+"/month/"+qc.month+"/selected-day/"+qc.selectedDay;
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function() {alert('Failed to refresh. It is likely that your session has expired.');},
		   success: function(res){
				$('#question-calendar').replaceWith(res);
				QC_attachEvents();
				$('.calendar').killLoading();
		   }
		 });
}