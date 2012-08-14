var updateableElements = [];
var elementCount = 0;

function addUElement(id  // Updateable Element ID
	, displayDivs        // Array of IDs of DIVs that are shown in "show" mode
	, displayDivsUpd     // Flags that indicate whether or not the display element has to be updated (it is not an image or button)
	, editDivs           // Array of IDs of DIVs that are shown in "edit" mode
	, editValues         // Array of IDs of edit controls with actual edited data to update
	, ajaxURL            // URL for update
	, urlParams          // URL parameters
	, urlPostfix)        // Constant parameters (string to attach to the end of URL)
{
	updateableElements[id] = new Object();
	updateableElements[id].displayDivs = displayDivs;
	updateableElements[id].displayDivsUpd = displayDivsUpd;
	updateableElements[id].editDivs = editDivs;
	updateableElements[id].editValues = editValues;
	updateableElements[id].ajaxURL = ajaxURL;
	updateableElements[id].urlParams = urlParams;
	updateableElements[id].modified = 0;
	updateableElements[id].editmode = 0; // being currently edited
	updateableElements[id].urlPostfix = urlPostfix;
	elementCount++;
}

function onUElementEdit(id){
	if (updateableElements[id].editmode) return;
	for (i in updateableElements[id].displayDivs)
		document.getElementById(updateableElements[id].displayDivs[i]).style.display = 'none';
	for (i in updateableElements[id].editDivs) {
		if (updateableElements[id].displayDivsUpd[i]) {
			var edittext = document.getElementById(updateableElements[id].displayDivs[i]).innerHTML;
			edittext = edittext.replace(new RegExp( "<br>", "ig" ), "\r\n");
			edittext = edittext.replace(new RegExp( "\t", "ig" ), "");
			document.getElementById(updateableElements[id].editValues[i]).value = edittext;
		}
		document.getElementById(updateableElements[id].editDivs[i]).style.display = 'block';
	}
	updateableElements[id].editmode = 1;
}

function finishUElementUpdate(id, newvals){
	for (i in updateableElements[id].displayDivs) {
		var el = document.getElementById(updateableElements[id].displayDivs[i]);
		if (updateableElements[id].displayDivsUpd[i]) el.innerHTML = newvals[i];
		el.style.display = 'block';
	}
	for (i in updateableElements[id].editDivs) {
		document.getElementById(updateableElements[id].editDivs[i]).style.display = 'none';
	}
	updateableElements[id].modified = 0;
	updateableElements[id].editmode = 0;
}

function onUElementSave(id){
	if (updateableElements[id].editmode == 0) return;
	// Retrieve new values, format URL parameters
	// Compare to old value to see if field has changed
	var newvalsDisplay = new Array(); // formatted for displaying
	var params = "id="+id;
	for (i in updateableElements[id].editValues) {
		var tmp = document.getElementById(updateableElements[id].editValues[i]).value;
		newvalsDisplay[i] = tmp.replace(new RegExp( "\n", "g" ), "<br>");
		params += "&"+updateableElements[id].urlParams[i]+"="+escape(newvalsDisplay[i]);
		if (document.getElementById(updateableElements[id].displayDivs[i]).innerHTML != newvalsDisplay[i])
			updateableElements[id].modified = 1;
	}
	if (updateableElements[id].modified) {
		params += updateableElements[id].urlPostfix;
		ajaxRequest(
			updateableElements[id].ajaxURL
			,params
			,function(responseObject){
				finishUElementUpdate(id, newvalsDisplay);
			}
			,function(responseObject){
				finishUElementUpdate(id, newvalsDisplay);
			}
		);
	} else {
		finishUElementUpdate(id, newvalsDisplay);
	}
}