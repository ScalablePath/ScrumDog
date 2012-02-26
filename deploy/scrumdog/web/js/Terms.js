var termsPopup = false;

$(document).ready(function (){
	
	$('.jscrip').html('');
	
	$('#terms-open-popup').attr("href",function() {return '#';});
	
	$('#terms-open-popup').click(function(){
		
		if(!termsPopup)
		{
			var dialogOptions = {height: 400, width: 600, resizable: false, title: 'Terms of Service', modal: true};
			var res = $("#terms-popup").dialog(dialogOptions);
			termsPopup = true;
		}
		else
		{
			$("#terms-popup").dialog('open');
		}
		$('#terms-popup').scrollTop(0);
	});
});



