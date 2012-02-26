var myObj;
var st_backlog;
var lastId;
var ST_removeDialogOpened = false;
var ST_assignDialogOpened = false;
var ST_estimateDialogOpened = false;
var createTaskDialogOpened = false;
var taskSaveSuccessFunc;
var num_files_to_upload = 1;

function Task() {
	$("input.numeric").numeric(null);
	this.id = $('#id_input').get(0).value;
	$('.task-edit').click(Task_Edit);
	$('.task-cancel').click(Task_Cancel);
	$('.task-save').click(Task_Save);
	//subtask stuff
	$('#create-task-button').click(ST_createTaskPop);
	$('span.refresh_button').click(ST_refresh);
	this.type = 'task';
	//name
	this.nameHeader = $('#name-header').get(0);
	this.nameInput = $('#name_input').get(0);
	this.name = this.nameInput.value;
	//description
	this.descriptionParagraph = $('#description_paragraph').get(0);
	this.descriptionTextarea = $('#description_textarea').get(0);
	this.description = this.descriptionTextarea.innerHTML;
	this.descriptionSize = $('#description_paragraph').height();
	this.setDescriptionTextareaSize = function() { };
	$(this.descriptionTextarea).autoResize();
	this.descriptionSize = this.descriptionSize==0 ? 35 : this.descriptionSize;
	$(this.descriptionTextarea).css({height: this.descriptionSize+'px'});
	//business_value
	this.bvParagraph = $('#bv_paragraph').get(0);
	this.bvSelect = $('#bv_select').get(0);
	this.bvIndex = this.bvSelect.selectedIndex;
	this.bvValue = this.bvSelect.options[this.bvIndex].value;
	this.bvString = this.bvSelect.options[this.bvIndex].text;	
	//priority
	this.priParagraph = $('#pri_paragraph').get(0);
	this.priSelect = $('#pri_select').get(0);
	this.priIndex = this.priSelect.selectedIndex;
	this.priValue = this.priSelect.options[this.priIndex].value;
	this.priString = this.priSelect.options[this.priIndex].text;	
	//estimated_hours
	this.ehParagraph = $('#eh_paragraph').get(0);
	this.ehInput = $('#eh_input').get(0);
	this.eh = this.ehInput.value;
	//user
	this.userParagraph = $('#user_paragraph').get(0);
	this.userSelect = $('#user_select').get(0);
	this.userIndex = this.userSelect.selectedIndex;
	this.userValue = this.userSelect.options[this.userIndex].value;
	this.userString = this.userSelect.options[this.userIndex].text;	
	//status
	this.statusParagraph = $('#status_paragraph').get(0);
	this.statusSelect = $('#status_select').get(0);
	this.statusIndex = this.statusSelect.selectedIndex;
	this.statusValue = this.statusSelect.options[this.statusIndex].value;
	this.statusString = this.statusSelect.options[this.statusIndex].text;	
	//parent task
	this.ptParagraph = $('#pt_paragraph').get(0);
	this.ptSelect = $('#pt_select').get(0);
	this.ptIndex = this.statusSelect.selectedIndex;
	this.ptValue = this.statusSelect.options[this.statusIndex].value;
	this.ptString = this.statusSelect.options[this.statusIndex].text;

	//comment
	$('#comment_save').click(Task_commentSave);
	this.commentParagraph = $('#comment_paragraph').get(0);
	this.commentTextarea = $('#comment_textarea').get(0);
	this.comment = this.commentTextarea.innerHTML;
	//functions
	this.save = function() {
		//make the ajax call
		myData = 'task[id]=' + this.id;
		myData += '&task[name]=' + encodeURIComponent(this.name);
		myData += '&task[description]=' + encodeURIComponent(this.description);
		myData += '&task[business_value]=' + this.bvValue;
		myData += '&task[priority]=' + this.priValue;
		myData += '&task[estimated_hours]=' + this.eh;
		myData += '&task[user_id]=' + this.userValue;
		myData += '&task[status]=' + this.statusValue;
		myData += '&task[parent_id]=' + this.ptValue;
		$.ajax({
		   type: "POST",
		   url: "/task-ajax-save",
		   data: myData,
			dataType: 'json',
			error: function() {alert('Failed to save task.');},
		   success: function(res){
				Task_taskSaveSuccess(res);
		   }
		 });
	}
	
	this.saveComment = function(myData) {
		//make the ajax call
		var saveUrl = "/task-ajax-comment-save/"+ this.id;
		$.ajax({
		   type: "POST",
		   url: saveUrl,
		   data: myData,
			dataType: 'html',
			error: function() {alert('Failed to save comment.'); },
			success: function (res) {
				var search_term = '<form id="login-form"';
				if (res.indexOf(search_term) != -1)
				{ 
					alert('Failed to save comment. Your session may have expired.  Next time you log in, try using the "remember" feature.');
				}
				else
				{
					$('#comment_textarea').get(0).value = "";	
					$('#outer_comment_paragraph').fadeTo("200", 0, function() {
						$('#outer_comment_paragraph').replaceWith(res);
						$('#outer_comment_paragraph').fadeTo("200", 1);
						});					
				}
			}
		 });
	}	
}

function Task_Edit(e) {
	$('.display').addClass('hide');	
	$('.field.pri-task').removeClass('hide');
}

function Task_Cancel(e) {
	//undo any changes (ToDo)
	myObj.nameInput.value = myObj.name;
	myObj.descriptionTextarea.value = myObj.description;
	myObj.bvSelect.selectedIndex = myObj.bvIndex;
	myObj.priSelect.selectedIndex = myObj.priIndex;
	myObj.ehInput.value = myObj.eh;
	myObj.userSelect.selectedIndex = myObj.userIndex;
	myObj.statusSelect.selectedIndex = myObj.statusIndex;

	$('.display').removeClass('hide');
	$('.field').addClass('hide');
}

function Task_Save(e) {
	//set any changes
	//name
	myObj.name = myObj.nameInput.value;
	myObj.nameHeader.innerHTML = myObj.name;
	//description
	myObj.description = myObj.descriptionTextarea.value;
	myObj.descriptionParagraph.innerHTML = nl2br(myObj.description);
	myObj.setDescriptionTextareaSize();
	//business value
	myObj.bvIndex = myObj.bvSelect.selectedIndex;
	myObj.bvValue = myObj.bvSelect.options[myObj.bvIndex].value;
	myObj.bvString = myObj.bvSelect.options[myObj.bvIndex].text;	
	myObj.bvParagraph.innerHTML = myObj.bvString;
	//priority
	myObj.priIndex = myObj.priSelect.selectedIndex;
	myObj.priValue = myObj.priSelect.options[myObj.priIndex].value;
	myObj.priString = myObj.priSelect.options[myObj.priIndex].text;	
	myObj.priParagraph.innerHTML = myObj.priString;
	//estimated hours
	myObj.eh = myObj.ehInput.value;
	myObj.ehParagraph.innerHTML = myObj.eh;
	//user
	myObj.userIndex = myObj.userSelect.selectedIndex;
	myObj.userValue = myObj.userSelect.options[myObj.userIndex].value;
	myObj.userString = myObj.userSelect.options[myObj.userIndex].text;	
	myObj.userParagraph.innerHTML = myObj.userString;
	//status
	myObj.statusIndex = myObj.statusSelect.selectedIndex;
	myObj.statusValue = myObj.statusSelect.options[myObj.statusIndex].value;
	myObj.statusString = myObj.statusSelect.options[myObj.statusIndex].text;	
	myObj.statusParagraph.innerHTML = myObj.statusString;
	//parent task
	myObj.ptIndex = myObj.ptSelect.selectedIndex;
	myObj.ptValue = myObj.ptSelect.options[myObj.ptIndex].value;
	myObj.ptString = myObj.ptSelect.options[myObj.ptIndex].text;	
	myObj.ptParagraph.innerHTML = myObj.ptString;

	$('.display').removeClass('hide');
	$('.field.pri-task').addClass('hide');
	myObj.save();
}

function Task_commentSave(e) {
	myObj.comment = encodeURIComponent(myObj.commentTextarea.value);
	var myData = 'task[comment]=' + myObj.comment;
	if(myObj.comment == "")
	{
		$('.error_field').removeClass('hide');
		myObj.commentTextarea.focus();
	}
	else
	{
		$('.error_field').addClass('hide');
		myObj.saveComment(myData);
	}
}


//this could be global
function nl2br(str, is_xhtml) {
    breakTag = '<br />';
    if (typeof is_xhtml != 'undefined' && !is_xhtml) {
        breakTag = '<br>';
    }
    return (str + '').replace(/([^>]?)\n/g, '$1'+ breakTag +'\n');
}

//Files Refresh
function Files_Refresh() {
	//make the ajax call
	var filesUrl = "/"+ myObj.type + "/"+ myObj.id + "/file-list";
	$.ajax({
		type: "GET",
		url: filesUrl,
		dataType: 'html',
		error: function() {alert('Failed to retrieve task list.'); },
		success: function (res) {
			var search_term = '<form id="login-form"';
			if (res.indexOf(search_term) != -1)
			{ 
				alert('Failed to retrieve file list. Your session may have expired.  Next time you log in, try using the "remember" feature.');
			}
			else
			{
				$('#file-list').fadeTo("200", 0, function() {
					$('#file-list').replaceWith(res);
					prepareFileList();
					$('#file-list').fadeTo("200", 1);
				});					
			}
		}
	 });
}

function ST_archive(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = ST_getTask(lastId);
	myTask.save('archive');
	$(myTask.regRow).toggle();
}

function ST_createTaskPop(e) {
	if(myObj.statusValue>1)
	{
		alert('You cannot add a subtask to a task that is already completed.');
		return;
	}
	if(!createTaskDialogOpened)
	{
		var dialogOptions = {height: 305, width: 500, buttons: {"Create": ST_createTask, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Create Task', modal: true};
		$("#create-task-dialog").dialog(dialogOptions);
		createTaskDialogOpened = true;
	}
	else
	{
		$("#create-task-dialog").dialog('open');
	}
}

function ST_createTask(e) {
	var tg = $('#pop-name').get(0);
	var tg2 = $('#pop-estimated_hours').get(0);
	if(tg.value=='' || (myObj.sprint_id && tg2.value==''))
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
		
		if(myObj.sprint_id && tg2.value=='')
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
		CD_create();
		$('#pop-name').get(0).value = '';
		$('#pop-estimated_hours').get(0).value = '';
		$('#pop-parent_id').get(0).value = myObj.id;
		$('#pop-user_id').get(0).value = '';
		$('#pop-status').get(0).value = 0;
		$('#pop-priority').get(0).value = 1;
	}
}

function ST_attachTableBodyEvents() {
	$('span.backlog_edit').click(ST_edit);
	$('span.backlog_cancel').click(ST_cancel);
	$('span.backlog_save').click(ST_save);
	$('span.backlog_remove').click(ST_removePop);
	$('span.backlog_assign').click(ST_assignPop);
	$('span.backlog_archive').click(ST_archive);
	$("input.numeric").numeric(null);
}

function ST_getTask(taskId) {
	if(st_backlog.taskHash[taskId])
	{
		var myTask = st_backlog.taskHash[taskId];
	}
	else
	{
		var myTask = new SprintTaskRow(taskId);
		st_backlog.taskHash[taskId] = myTask;
	}
	return myTask;
}

function ST_edit(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = ST_getTask(taskId);
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function ST_cancel(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = ST_getTask(taskId);
	//reset the form values
	myTask.nameInput.value = myTask.name;
	myTask.userSelect.selectedIndex = myTask.userIndex;
	myTask.prioritySelect.selectedIndex = myTask.priorityIndex;
	myTask.ehInput.value = myTask.estimatedHours;
	$(myTask.formRow).toggleClass('hide');
	$(myTask.regRow).toggleClass('hide');
}

function ST_save(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskId = tg.getAttribute('id').split('-')[1];
	var myTask = ST_getTask(taskId);
	myTask.save('row');
	$(myTask.regRow).toggleClass('hide');
	$(myTask.formRow).toggleClass('hide');
}

function ST_removePop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = ST_getTask(lastId);
	if(!ST_removeDialogOpened)
	{
		var dialogOptions = {buttons: {"Ok": ST_remove, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Remove', modal: true};
		$("#remove_dialog").dialog(dialogOptions);
		ST_removeDialogOpened = true;
	}
	else
	{
		$("#remove_dialog").dialog('open');
	}
}

function ST_assignPop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = ST_getTask(lastId);
	if(myTask.estimatedHours!='')
	{
		if(!ST_assignDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": ST_assign, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Select Sprint', modal: true};
			$("#assign_dialog").dialog(dialogOptions);
			ST_assignDialogOpened = true;
		}
		else
		{
			$("#assign_dialog").dialog('open');
		}
	}
	else
	{
		if(!ST_estimateDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": function() {$(this).dialog('close')}}, resizable: false, title: 'Not so fast!', modal: true};
			$("#estimate_dialog").dialog(dialogOptions);
			ST_estimateDialogOpened = true;
		}
		else
		{
			$("#estimate_dialog").dialog('open');
		}
	}	
}

function ST_archive(e) {
	if(confirm("Are you sure you want to send this task (and all of its subtasks) to the archive?"))
	{
		if (!e) var e = window.event;
		var tg = (window.event) ? e.srcElement : e.target;
		lastId = tg.getAttribute('id').split('-')[1];
		var myTask = ST_getTask(lastId);
		myTask.save('archive');
		$(myTask.regRow).toggle();
	}
}

function ST_assignPop(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	lastId = tg.getAttribute('id').split('-')[1];
	var myTask = ST_getTask(lastId);
	if(myTask.estimatedHours!='')
	{
		if(!ST_assignDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": ST_assign, "Cancel": function() {$(this).dialog('close')}}, resizable: false, title: 'Select Sprint', modal: true};
			$("#assign_dialog").dialog(dialogOptions);
			ST_assignDialogOpened = true;
		}
		else
		{
			$("#assign_dialog").dialog('open');
		}
	}
	else
	{
		if(!ST_estimateDialogOpened)
		{
			var dialogOptions = {buttons: {"Ok": function() {$(this).dialog('close')}}, resizable: false, title: 'Not so fast!', modal: true};
			$("#estimate_dialog").dialog(dialogOptions);
			ST_estimateDialogOpened = true;
		}
		else
		{
			$("#estimate_dialog").dialog('open');
		}
	}	
}

function ST_assign() {
	//close the dialog
	$("#assign_dialog").dialog('close');
	var myTask = ST_getTask(lastId);
	var mySelect = $('#assign_select').get(0);	
	myTask.sprintId = mySelect.options[mySelect.selectedIndex].value;
	$(myTask.regRow).toggle();
	myTask.save('assign');
}

function ST_refresh() {
	$('.subtask_table').removeClass('hide');
	$('.subtask_table').loading();
	var myURL = "/task/"+myObj.id+"/subtask-body";
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function() {alert('Failed to refresh. It is likely that your session has expired.');},
		   success: function(res){
				$('#backlog_body').replaceWith(res);
				st_backlog.taskHash = [];
				ST_attachTableBodyEvents();
				$('.subtask_table').killLoading();
		   }
		 });
}

function CD_create() {
	var name = encodeURIComponent($('#pop-name').get(0).value);
	var estimated_hours = $('#pop-estimated_hours').get(0).value;
	var parent_id = $('#pop-parent_id').get(0).value;
	var user_id = $('#pop-user_id').get(0).value;
	var status = $('#pop-status').get(0).value;
	var priority = $('#pop-priority').get(0).value;
	var project_id = myObj.project_id;
	
	var myData = 'task[project_id]='+project_id+'&task[name]='+name+'&task[estimated_hours]='+estimated_hours+'&task[parent_id]='+parent_id+'&task[user_id]='+user_id+'&task[status]='+status+'&task[priority]='+priority;
	
	if(myObj.sprint_id)
	{
		myData += '&task[sprint_id]=' + myObj.sprint_id;
	}
//alert(myData);
	$.ajax({
   		type: "POST",
   		url: "/task-ajax-create",
   		data: myData,
		dataType: 'json',
		error: function() {alert('Failed to save task. It is likely that your session has expired.');},
   		success: function(res){
   			CD_taskCreateSuccess(res);
 		}
 	});
}

function CD_taskCreateSuccess(res) {
	if(res.status=='error')
	{
		alert(res.message);
	}
	else
	{
		ST_refresh();
	}
}

function SubtaskBacklog() {
	this.taskHash = [];
	ST_attachTableBodyEvents();
}

function ST_taskSaveSuccess(action, res) {
	if(res.status=='error')
	{
		alert(res.message);
		ST_refresh();
	}
	else
	{
		var myUrl = '/create-dialog/task/'+myObj.id;
		refreshCreateDialog(myUrl);
	}
}

function Task_taskSaveSuccess(res) {
	if(res.status=='error')
	{
		alert(res.message);
		window.location = window.location;
	}
	else
	{
		var myUrl = '/create-dialog/task/'+myObj.id;
		refreshCreateDialog(myUrl);
	}
}

function refreshCreateDialog(myURL) {
	$.ajax({
		   type: "GET",
		   url: myURL,
			error: function(res) {
				alert('Failed to refresh. It is likely that your session has expired.');
			},
		   success: function(res){
				$('.ctd-innards').replaceWith(res);
		   }
		 });
}

function dialog(mes, s) {
	s = s || {bgColor: 'transparent'};
	
	$.fn.nyroModalManual({
		bgColor: s.bgColor,
		content: mes
	});
}

function deleteFile(e) {
	if (!e) var e = window.event;
	var tg = (window.event) ? e.srcElement : e.target;
	var taskFileId = tg.getAttribute('id').split('-')[1];
	var fileLink = document.getElementById('file_link-'+taskFileId);
	var fileName = fileLink.getAttribute('alt');
	if(confirm("Are you sure you want to delete "+fileName+"?"))
	{
		$('#file_item-'+taskFileId).hide();
		$.ajax({
	   		type: "POST",
	   		url: "/"+myObj.type+"-ajax-file-delete/"+myObj.id+"/"+taskFileId,
			dataType: 'json',
			error: function() {alert('It is possible that your file was not deleted.');},
	 	});
	}
}

function prepareFileList() {
	$('#file-list li').mouseenter(
		function(e) {
			$(this).children('span.file').show();
		}
	);
	$('#file-list li').mouseleave(
		function(e) {
			$(this).children('span.file').hide();
		}
	);
	$('#file-list span.file.delete').click(deleteFile);
	$('#file-list a.nyroModal').nyroModal();											   
	$('#file-list a').tooltip();
	
	$('#file-list a img').hover(
		function(e) {
			this.t = this.title;
			this.title = "";
		},
		function() {
			this.title = this.t;
		}	
	);
}

function startCallback() {
	var currentFile;
	var end = true;
	
	for (var i = 0; i < num_files_to_upload; i++){
		currentFile = $('#file-input'+i).get(0).value;
		
		if (!currentFile) end = false; 
	}
	
	if (end) $("#file-uploading").show(); else alert('You must select a file to upload'); 
	
	return end;
}

function completeCallback(response) {
	Files_Refresh();
	$("#addFiles").show();
	$("#addFilesForm").hide();
	$("#file-uploading").hide();
	$('#file-input0').get(0).value = '';
	removeAllFileToUpload( 'divFileUpload' );
}

function showHistory() {
	var taskUrl = "/"+ myObj.type + "/"+ myObj.id + "/history";
	$.ajax({
		type: "GET",
		url: taskUrl,
		dataType: 'html',
		error: function() {alert('Failed to retrieve task history list.'); },
		success: function (res) {
			var search_term = '<form id="login-form"';
			if (res.indexOf(search_term) != -1)
			{ 
				alert('Failed to retrieve task list. Your session may have expired.  Next time you log in, try using the "remember" feature.');
			}
			else
			{
				$('#history').append($(res));
				$("#history").animate({ 
					backgroundColor: '#FFEE88'
				  }, '800', 'linear', function() { $(this).animate({ backgroundColor: '#ffffff' }); }
				);

			}
		}
	});
	$(this).hide();
	return false;
}


function addFileToUpload(idForm){
	
	var form = document.getElementById(idForm);
	var newdiv = document.createElement('div');

	newdiv.style.padding = "5px 0 0 0";
	newdiv.setAttribute('id', 'div_file_'+num_files_to_upload);
	 
	newdiv.innerHTML = '<input autocomplete="off" id="file-input'+num_files_to_upload+'" type="file" name="Filedata'+num_files_to_upload+'" />';
	form.appendChild(newdiv);

	num_files_to_upload ++;
}

function removeFileToUpload(idForm){
	
	if (num_files_to_upload > 1){
		var form = document.getElementById(idForm);
		var lastdiv = document.getElementById('div_file_'+ (num_files_to_upload - 1));

		form.removeChild( lastdiv );
		num_files_to_upload--;
	}
}

function removeAllFileToUpload(idForm){
	while (num_files_to_upload > 1) removeFileToUpload(idForm);
}