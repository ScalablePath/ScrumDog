jQuery.fn.extend({
	parseclass : function() {	
		$(this).each(function() {
			var classes = this.className.split(" ");
			
			for (var i=0, class2; typeof(class2 = classes[i]) !== "undefined"; i++) {
				try {
					class2 = class2.replace(/([^\\])'/g, '$1"');
					class2 = unescape(class2);
					var results =eval(class2);
					console.log(results);
					return results;
				} catch (e) {
				}
			}
			return false;
		});
	}
});