{% include file = "showMarkupHeader.tpl.html" %}
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
        <script type="text/javascript">
			function createProject(){
				var pname = document.getElementById('pname').value;
				var lead_id = document.getElementById('plead').value;
				
				ajaxRequest(
					'/project/doAdd'
					,"pname="+pname+"&lead_id="+lead_id
					,function(responseObject){
						gotoLocation("/");
					}
					,function(responseObject){
						gotoLocation("/");
					}
				);

			}
        </script>
    </head>
{% include file = "showMarkupBodyHeader.tpl.html" %}
			
		<table border='0'>
		<tbody>
			<tr>
				<td>Name</td>
				<td><input id='pname'></input></td>
			</tr>
			<tr>
				<td>Lead</td>
				<td>
					<select id='plead'>
					{% foreach from = $users item = "item" %}
						<option value="L{% $item.id %}">{% $item.firstname %} {% $item.lastname %}</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
		</tbody>
		</table>

		<BUTTON onclick="createProject()">Create</BUTTON>
		
{% include file = "showMarkupBodyFooter.tpl.html" %}

