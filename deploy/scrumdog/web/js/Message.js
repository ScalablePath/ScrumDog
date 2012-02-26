var myObj;

function Message() {
	this.id = $('#id_input').get(0).value;
	$('.message-edit').click(Message_Edit);
	$('.message-cancel').click(Message_Cancel);
	$('.message-save').click(Message_Save);
	this.type = 'message';
	//title
	this.titleHeader = $('#title-header').get(0);
	this.titleInput = $('#title_input').get(0);
	this.title = this.titleInput.value;
	//content
	this.contentParagraph = $('#content_paragraph').get(0);
	this.contentTextarea = $('#content_textarea').get(0);
	this.content = this.contentTextarea.innerHTML;
	this.contentSize = $('#content_paragraph').height();
	this.setcontentTextareaSize = function() { };
	$(this.contentTextarea).autoResize();
	this.contentSize = this.contentSize==0 ? 35 : this.contentSize;
	$(this.contentTextarea).css({height: this.contentSize+'px'});

	//comment
	$('#comment_save').click(Message_commentSave);
	this.commentParagraph = $('#comment_paragraph').get(0);
	this.commentTextarea = $('#comment_textarea').get(0);
	this.comment = this.commentTextarea.innerHTML;
	//functions
	this.save = function() {
		//make the ajax call
		myData = 'message[id]=' + this.id;
		myData += '&message[title]=' + encodeURIComponent(this.title);
		myData += '&message[content]=' + encodeURIComponent(this.content);
		myData += '&message_id=' + this.id;

		$.ajax({
		   type: "POST",
		   url: "/message-ajax-save",
		   data: myData,
			dataType: 'json',
			error: function() {alert('Failed to save message.');},
		   success: function(res){
				//TBD
		   }
		 });
	}
	
	this.saveComment = function(myData) {
		//make the ajax call
		var saveUrl = "/message-ajax-comment-save/"+ this.id;
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

function Message_Edit(e) {
	$('.display').addClass('hide');	
	$('.field').removeClass('hide');
}

function Message_Cancel(e) {
	//undo any changes (ToDo)
	myObj.titleInput.value = myObj.title;
	myObj.contentTextarea.value = myObj.content;
	$('.display').removeClass('hide');
	$('.field').addClass('hide');
}

function Message_Save(e) {
	//set any changes
	//title
	myObj.title = myObj.titleInput.value;
	myObj.titleHeader.innerHTML = myObj.title;
	//content
	myObj.content = myObj.contentTextarea.value;
	myObj.contentParagraph.innerHTML = nl2br(myObj.content);
	myObj.setcontentTextareaSize();

	$('.display').removeClass('hide');
	$('.field').addClass('hide');
	myObj.save();
}

function Message_commentSave(e) {
	myObj.comment = encodeURIComponent(myObj.commentTextarea.value);
	var myData = 'message[comment]=' + myObj.comment;
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