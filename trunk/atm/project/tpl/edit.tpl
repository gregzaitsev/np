{% include file = "showMarkupHeader.tpl.html" %}

		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
		<script type="text/javascript" src="/js/updateableElement.js"></script>

        <script type="text/javascript">

			{% foreach from = $releases item = "item" %}
			addUElement('R{% $item.id %}', 
				['RNT{% $item.id %}', 'RDT{% $item.id %}', 'RIMG{% $item.id %}'],
				[1, 1, 0],
				['RNE{% $item.id %}', 'RDE{% $item.id %}', 'RBTN{% $item.id %}'],
				['RNECTL{% $item.id %}', 'RDECTL{% $item.id %}'],
				'/release/doUpdate',
				['name', 'date'],
				'');
			{% /foreach %}

			{% foreach from = $categories item = "item" %}
			addUElement('C{% $item.id %}', ['CT{% $item.id %}', 'CIMG{% $item.id %}'], [1, 0], ['CE{% $item.id %}', 'CBTN{% $item.id %}'], ['CECTL{% $item.id %}'], '/category/doUpdate', ['name'], '');
			{% /foreach %}

			function addRelease(){

				var pid = {% $pid %};
				var rname = document.getElementById('adrelname').value;
				var rdate = document.getElementById('adreldate').value;

				ajaxRequest(
					'/release/doAdd'
					,"pid="+pid+"&rname="+rname+"&rdate="+rdate
					,function(responseObject){
						gotoLocation("/project/edit?pid="+pid);
					}
					,function(responseObject){
						gotoLocation("/project/edit?pid="+pid);
					}
				);
			}

			function addCategory(){

				var pid = {% $pid %};
				var cname = document.getElementById('adcategory').value;

				ajaxRequest(
					'/category/doAdd'
					,"pid="+pid+"&cname="+cname
					,function(responseObject){
						gotoLocation("/project/edit?pid="+pid);
					}
					,function(responseObject){
						gotoLocation("/project/edit?pid="+pid);
					}
				);
			}

			function updateProject(){
			
				var pid = {% $pid %};
				var pname = document.getElementById('pname').value;
				var lead_id = document.getElementById('plead').value;
				
				ajaxRequest(
					'/project/doUpdate'
					,"pid="+pid+"&pname="+pname+"&lead_id="+lead_id
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
				<td width='100' valign="top">Name</td>
				<td><input id='pname' value='{% $pname %}'></input></td>
			</tr>
			<tr>
				<td valign="top">Lead</td>
				<td>
					<select id='plead'>
					{% foreach from = $users item = "item" %}
						<option value="L{% $item.id %}"{% if $item.id == $lead_id %}selected="selected"{% /if %}>
						{% $item.firstname %} {% $item.lastname %}</option>
					{% /foreach %}
					
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">Releases</td>
				<td>
					<table border='0'>
					<tbody>
						{% foreach from = $releases item = "item" %}
						<tr>
								<td bgcolor='#EEEEFF' width='100'>
									<div id='RNT{% $item.id %}'>{% $item.name %}</div>
									<div id='RNE{% $item.id %}' style='display:none'><input type="text" id="RNECTL{% $item.id %}" value="{% $item.name %}" size='10'/></div>
								</td>
								<td bgcolor='#EEEEFF' width='5'></td>
								<td bgcolor='#EEEEFF' width='100'>
									<div id='RDT{% $item.id %}'>{% $item.date %}</div>
									<div id='RDE{% $item.id %}' style='display:none'><input type="text" id="RDECTL{% $item.id %}" value="{% $item.date %}" size='10'/></div>
								</td>
								<td bgcolor='#FFFFFF' width='16'>
									<div id='RIMG{% $item.id %}'><img src="/img/edit.gif" onclick="onUElementEdit('R{% $item.id %}')"></div>
									<div id='RBTN{% $item.id %}' style='display:none'><button onclick="onUElementSave('R{% $item.id %}')">Save</button></div>
								</td>
						</tr>
						{% /foreach %}
					</tbody>
					</table>
					
					<hr>
					
					<table border='0'>
					<tbody>
						<tr>
								<td width='100'><input type="text" id="adrelname" size='10'/></td>
								<td width='5'></td>
								<td width='100'><input type="text" id="adreldate" size='10' value="YYYY/MM/DD"/></td>
								<td width='16'><img src="/img/add.gif" onclick='addRelease()'></td>
						</tr>
					</tbody>
					</table>

					
				</td>
			</tr>
			<tr>
				<td valign="top">Categories</td>
				<td>
					<table border='0'>
					<tbody>
						{% foreach from = $categories item = "item" %}
						<tr>
								<td bgcolor='#EEEEFF' width='200'>
									<div id='CT{% $item.id %}'>{% $item.name %}</div>
									<div id='CE{% $item.id %}' style='display:none'><input type="text" id="CECTL{% $item.id %}" value="{% $item.name %}" size='10'/></div>
								</td>
								<td bgcolor='#FFFFFF' width='16'>
									<div id='CIMG{% $item.id %}'><img src="/img/edit.gif" onclick="onUElementEdit('C{% $item.id %}')"></div>
									<div id='CBTN{% $item.id %}' style='display:none'><button onclick="onUElementSave('C{% $item.id %}')">Save</button></div>
								</td>
						</tr>
						{% /foreach %}
					</tbody>
					</table>
					
					<hr>
					
					<table border='0'>
					<tbody>
						<tr>
								<td width='200'><input type="text" id="adcategory" size='25'/></td>
								<td width='16'><img src="/img/add.gif" onclick='addCategory()'></td>
						</tr>
					</tbody>
					</table>

					
				</td>
			</tr>
		</tbody>
		</table>

		<hr>
		<BUTTON onclick="updateProject()">Save</BUTTON>
		
{% include file = "showMarkupBodyFooter.tpl.html" %}

