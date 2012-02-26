jQuery.fn.extend({
	tooltip : function() {	
	
		var xOffset = 10,
			yOffset = 20;	
			
		this.each(function() { 
			$(this).unbind('hover');
			$(this).hover(function(e){	
				this.t = this.title;
				this.title = "";
				var div = $("<div class='tooltip' />").html(this.t);
				$("body").append(div);
				$(".tooltip")
					.css("top",(e.pageY - xOffset) + "px")
					.css("left",(e.pageX + yOffset) + "px")
					.fadeIn("fast");		
			},
			function(){
				this.title = this.t;		
				$(".tooltip").remove();
			});	
			$(this).mousemove(function(e){
				$(".tooltip")
					.css("top",(e.pageY - xOffset) + "px")
					.css("left",(e.pageX + yOffset) + "px");
			});		
		});
	}
});
