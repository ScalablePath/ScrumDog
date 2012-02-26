jQuery.fn.extend({
	loading: function() {
		this.each(function() { 
			if(!$(this).hasClass('loading'))
			{
				var dimensions = {width:$(this).width(), height:$(this).height(), position: $(this).offset()};
				var div = document.createElement('div');
					div.style.width=dimensions.width+'px';
					div.style.height=dimensions.height+'px';
					div.style.top=dimensions.position.top+'px';
					div.style.left=dimensions.position.left+'px';
					div.className='scrum-loader';
				//var span = document.createElement('span');
				//	span.innerHTML = 'loading';
				//	span.style.left=(dimensions.width/2)-16+'px';
				
				//div.appendChild(span);
				document.body.appendChild(div);
				$(this).addClass('loading');
				
				return div;
			}
			return false;
		});
	},
	killLoading: function() {
		this.each(function() { 
			$(document.body).find('.scrum-loader').each(function() {
				document.body.removeChild(this);									  
			});
			return $(this).removeClass('loading');
		});
	}
});
