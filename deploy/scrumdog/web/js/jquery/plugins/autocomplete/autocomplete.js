$().ready(function() {
	
	$("#addmembers_usernames").autocomplete('/project/autocomplete', {
		multiple: true,
		minChars: 0,
		max: 1000,
		matchContains: "word",
		autoFill: false,
		dataType: "json",
		parse: function(data) {
			return $.map(data, function(row) {
				return {
					data: row,
					value: row.name,
					result: row.username
				}
			});
		},
		formatItem: function(row, i, max) {
			return row.name + " (" + row.username + ")";
		},
		formatMatch: function(row, i, max) {
			return row.name + " " + row.username;
		},
		
	});
		
});