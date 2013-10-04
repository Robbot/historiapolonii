//window.addEvent('domready', function(){
window.onload = function() {
	var selectThumbnailLink = document.getElementById('select-thumbnail-link').onclick = function() {
		
		var selectThumbnailLinkInner = document.getElementById('select-thumbnail-link');
		var reqTypeVar = selectThumbnailLinkInner.getAttribute('rel');
		var selectReqType = document.getElementById('reqType');
		selectReqType.setAttribute('value', reqTypeVar);
		var selectController = document.getElementById('controller');
		selectController.setAttribute('value', 'folders');
		var selectTask = document.getElementById('task');
		selectTask.setAttribute('value', 'add');
		
		document.adminForm.submit();

		return false;
	};
	
	var selectFolderLink = document.getElementById('select-folder-link').onclick = function() {
		
		var selectFolderLinkInner = document.getElementById('select-folder-link');
		var reqTypeVar = selectFolderLinkInner.getAttribute('rel');
		var selectReqType = document.getElementById('reqType');
		selectReqType.setAttribute('value', reqTypeVar);
		var selectController = document.getElementById('controller');
		selectController.setAttribute('value', 'folders');
		var selectTask = document.getElementById('task');
		selectTask.setAttribute('value', 'add');
		
		document.adminForm.submit();

		return false;
	};
};
