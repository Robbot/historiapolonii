/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component XGallery Component
 * @copyright Copyright (C) Dana Harris optikool.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

function cUploadComplete(file) {
    if (this.getStats().files_queued === 0) {
        var cancelButton = document.getElementById(this.customSettings.cancelButtonId);
        if (cancelButton) {
            document.getElementById(this.customSettings.cancelButtonId).disabled = true;
        }
    }

    try {
        if (this.getStats().files_queued > 0) {
            this.startResizedUpload(this.getQueueFile(0).id, this.customSettings.thumbnail_width, this.customSettings.thumbnail_height, SWFUpload.RESIZE_ENCODING.JPEG, this.customSettings.thumbnail_quality, false);
        } else {
        	var progress = new FileProgress(file,  this.customSettings.upload_target);
			progress.setComplete();
			progress.setStatus("All images received.");
			progress.toggleCancel(false);
			var feedback = document.getElementById("progressFeedback");
			feedback.innerHTML = "";
			document.getElementById(this.customSettings.cancelButtonId).disabled = true;
        }
    } catch (ex) {
        this.debug(ex);
    }

    if (this.getStats().files_queued > 0) {
        // Disable the standard upload routine
        return false;
    } else {
        return true;
    }
}

function cUploadSuccess(file, serverData) {
	var rdata = jQuery.parseJSON(serverData);
	try {
		switch(serverData) {
		case '500':
			alert('500: There was an error while transferring selected files.');
			break;
		default:
			var progress = new FileProgress(file, this.customSettings.progressTarget);
			progress.setComplete();
			progress.setStatus(rdata.msg);
			progress.toggleCancel(false);
			//addSelectOption(rdata.name);
		}
	} catch (ex) {
		this.debug(ex);
	}
}

/*
function cUploadSuccess(file, serverData) {
	var rdata = jQuery.parseJSON(serverData);
	try {
		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setComplete();
		progress.setStatus(rdata.msg);
		progress.toggleCancel(false);
		addSelectOption(rdata.name);

	} catch (ex) {
		this.debug(ex);
	}
}
*/

function addSelectOption(name) {	
	var selObj = document.getElementById('thumbnail-name');
	var optn = document.createElement("OPTION");
	optn.text = name;
	optn.value = name;
	selObj.options.add(optn);
}

jQuery(document).ready(function() { 
	var tlink = jQuery("#thumblink").attr("value");
	var fpath = jQuery("#folder").attr("value");
	var fullLink = '';

	jQuery('#thumbnail-name').change(function() {
		var thb = jQuery(this).val();
		fullLink = fpath + '/' + thb;
		jQuery('#selected-thumb-view').attr('src', tlink + fullLink);
		jQuery('#selected-thumb').attr('value', fullLink);
		jQuery('#selected-thumb-view').show();	
	});
});
