	{% include file = "showMarkupHeader.tpl.html" %}
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
        <script type="text/javascript">
			
			function subscribeToProject(id, subscribe){
				var uid = {% $uid %};
				var pid = id;
				
				var ajaxURL = "";
				if (subscribe) ajaxURL = "/notifications/doSubscribe";
				else ajaxURL = "/notifications/doUnsubscribe";
				
				ajaxRequest(
					ajaxURL
					,"uid="+uid+"&pid="+pid
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
			}

			function goBack(){
				gotoLocation("/");
			}
			
        </script>
    </head>
	{% include file = "showMarkupBodyHeader.tpl.html" %}

		<b>Subscribe by project:</b>
		<table border='0' cellpadding='3'>
		<tbody>
			{% foreach from = $projects item = "item" %}
				<tr>
					<td><input type='checkbox' onchange='subscribeToProject({% $item.id %}, this.checked)' {% if $item.subscribed %}checked{% /if %} /></td>
					<td onclick='openProject({% $item.id %})'>{% $item.name %}</td>
				</tr>
			{% /foreach %}
		</tbody>
		</table>

		<hr>
		<button onclick="goBack()">< Go Back</button>
		
	{% include file = "showMarkupBodyFooter.tpl.html" %}

