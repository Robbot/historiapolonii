$.noConflict();

jQuery.(document).ready(function($) {
	$('#select-thumbnail-link').click(function() {
		var reqTypeVar = $(this).attr('rel');
		$('#reqType').attr('value', reqTypeVar);
		$('#controller').attr('value', 'folders');
		$('#task').attr('value', 'add');
		$('#adminForm').submit();
		
		return false;
	});
	
	$('#select-folder-link').click(function() {
		var reqTypeVar = $(this).attr('rel');
		$('#reqType').attr('value', reqTypeVar);
		$('#controller').attr('value', 'folders');
		$('#task').attr('value', 'add');
		$('#adminForm').submit();
		
		return false;
	});
});
