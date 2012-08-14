var ajax_request_executing=0;
var ajaxOnHardFailureURL = "/";
var onAjaxFinishCB = function() {};

function gotoLocation(url){
	window.location=url;
}

function ajaxRequest(url, params, onSuccessHandler, onFailureHandler){

	var xmlhttp;
	if (window.XMLHttpRequest)
	{ // code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{ // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			if (xmlhttp.responseText.indexOf('ajaxerror') == -1) alert('Got bad response. Text = '+xmlhttp.responseText);
		
			var ajaxerror = '';
			var userdata = '';
			eval(xmlhttp.responseText);
			//if ((ajaxerror == undefined) || (userdata == undefined)) alert(xmlhttp.responseText);
			if ((ajaxerror != '') && (ajaxerror != '0')) alert(ajaxerror);

			onSuccessHandler(xmlhttp.responseText);
		}
		else if (xmlhttp.readyState==4)
		{
			alert('got response Error');
		
			var ajaxerror = '';
			eval(xmlhttp.responseText);
			if (ajaxerror != '') alert(ajaxerror);

			onFailureHandler(xmlhttp.responseText);
		}
	}
	// add a parameter indicating that this is a request from AJAX
	xmlhttp.open("GET",url+'?'+params+'&rtype=ajax',true);
	xmlhttp.send();
}
