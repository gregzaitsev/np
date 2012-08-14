{% include file = "showMarkupHeader.tpl.html" %}

		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
		<script type="text/javascript" src="/js/cookies.js"></script>
		<script type="text/javascript" src="/js/fade.js"></script>
        <script type="text/javascript">
		
			var pid = {% $pid %};
			var relid = "R0";
			var catid = "C0";
			var user_id = "U0";
			var status_id = "S0";

			function LoadTasksAJAX() {
				document.getElementById('taskdata').innerHTML = "<h1>Loading tasks...</h1>";
				SetOpacity('taskdata', 5);
				ajaxRequest(
					'/task/doLoad'
					,"pid="+pid+"&relid="+relid+"&catid="+catid+"&user_id="+user_id+"&status_id="+status_id
					,function(responseObject){
						var userdata='';
						eval(responseObject);
						SetOpacity('taskdata', 0);
						document.getElementById('taskdata').innerHTML = userdata;
						FadeIn('taskdata', null);
					}
					,function(responseObject){
						alert("AJAX error");
					}
				);
			}
		
			function LoadTasks() {
				relid = document.getElementById('releases').value;
				catid = document.getElementById('categories').value;
				user_id = document.getElementById('users').value;
				status_id = document.getElementById('statuses').value;
				
				saveCookies();
			
				FadeOut('taskdata', LoadTasksAJAX);
			}
			
			function addTask(){
				var pid = {% $pid %};
				var tname = document.getElementById('adname').value;
				var tpriority = document.getElementById('adpriority').value;
				var towner = document.getElementById('aduser').value;
				var trel = document.getElementById('adrelease').value;
				var tcat = document.getElementById('adcategory').value;
				
				if (tname.length == 0) return;
				
				ajaxRequest(
					'/task/doAdd'
					,"pid="+pid+"&tname="+tname+"&tpriority="+tpriority+"&towner="+towner+"&trel="+trel+"&tcat="+tcat
					,function(responseObject){
						LoadTasks();
					}
					,function(responseObject){
						LoadTasks();
					}
				);
			}
			
			function openTask(id) {
				var pid = {% $pid %};
				gotoLocation('/task/view?tid='+id+'&pid='+pid);
			}
			
			function deleteTask(id){
				var answer = confirm ("Press OK to delete task with ID "+id+".");
				if (answer) {
					ajaxRequest(
						'/task/doDelete'
						,"tid="+id
						,function(responseObject){
							LoadTasks();
						}
						,function(responseObject){
							LoadTasks();
						}
					);
				}
			}
			
			function qaddkeypressed(e) {
			
				var keynum = 0;
				if(window.event) // IE
				{
					keynum = e.keyCode;
				}
				else if(e.which) // Netscape/Firefox/Opera
				{
					keynum = e.which;
				}
				
				if (keynum == 13) {
					addTask();
					document.getElementById('adname').value = '';
				}
			}
			
			function saveCookies(){
				var pid = {% $pid %};

				setCookie('release'+pid, relid, 60);
				setCookie('category'+pid, catid, 60);
				setCookie('user'+pid, user_id, 60);
				setCookie('status'+pid, status_id, 60);
			}
			
			function loadCookies(){
				var pid = {% $pid %};
				relid = getCookie('release'+pid);
				catid = getCookie('category'+pid);
				user_id = getCookie('user'+pid);
				status_id = getCookie('status'+pid);
				
				if (relid == null) relid = 'R0';
				if (catid == null) catid = 'C0';
				if (user_id == null) user_id = 'U0';
				if (status_id == null) status_id = 'S0';
				
				document.getElementById('releases').value = relid;
				document.getElementById('categories').value = catid;
				document.getElementById('users').value = user_id;
				document.getElementById('statuses').value = status_id;
			}

			window.onload=function(){
				loadCookies();
				LoadTasks();
			}
			
			function toggleDetails() {
				var detdiv = document.getElementById('estdetails');
				
				if (detdiv.style.display == "block") {
					detdiv.style.display = "none";
					document.getElementById("plus").style.display = "block";
					document.getElementById("minus").style.display = "none";
				}
				else {
					detdiv.style.display = "block";
					document.getElementById("plus").style.display = "none";
					document.getElementById("minus").style.display = "block";
				}
			}

        </script>
    </head>
{% include file = "showMarkupBodyHeader.tpl.html" %}
		
		<table border='0'>
		<tbody>
			<tr>
				<td width='100'>Releases:</td>
				<td width='100'>
					<select id='releases' onchange='LoadTasks()'>
					<option value="R0" selected="selected">ALL</option>
					{% foreach from = $releases item = "item" %}
						<option value="R{% $item.id %}">{% $item.name %} ({% $item.date %})</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td width='100'>Categories:</td>
				<td width='100'>
					<select id='categories' onchange='LoadTasks()'>
					<option value="C0" selected="selected">ALL</option>
					{% foreach from = $categories item = "item" %}
						<option value="C{% $item.id %}">{% $item.name %}</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td width='100'>Users:</td>
				<td width='100'>
					<select id='users' onchange='LoadTasks()'>
					<option value="U0" selected="selected">ALL</option>
					{% foreach from = $users item = "item" %}
						<option value="U{% $item.id %}">{% $item.firstname %} {% $item.lastname %}</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td width='100'>Status:</td>
				<td width='100'>
					<select id='statuses' onchange='LoadTasks()'>
					<option value="S0" selected="selected">ALL</option>
					<optgroup label='-------'>
						<option value="S100">Not finished</option>
					</optgroup>
					<optgroup label='-------'>
					{% foreach from = $statuses item = "item" %}
						<option value="S{% $item.id %}">{% $item.name %}</option>
					{% /foreach %}
					</optgroup>
					</select>
				</td>
			</tr>
		</tbody>
		</table>
		
		<div id='taskdata'>
		Loading tasks...
		</div>

		<hr>

		<!-- Quick add -->
		<table border='0'>
		<tbody>
			<tr>
				<td width='20'></td>
				<td width='20'></td>
				<td width='40'></td>
				<td width='200'><input type="text" id="adname" size='25' onkeypress="qaddkeypressed(event)"/></td>
				<td width='80'>
					<select id='adpriority'>
						<option value="1">High</option>
						<option value="2" selected="selected">Normal</option>
						<option value="3">Low</option>
					</select>
				</td>
				
				<td width='100'>
					<select id='aduser'>
					{% foreach from = $users item = "item" %}
						<option value="U{% $item.id %}" {% if $item.lead %}selected='selected'{% /if %} >{% $item.firstname %}</option>
					{% /foreach %}
					</select>
				</td>

				<td width='100'>
					<select id='adrelease'>
					{% foreach from = $futurereleases item = "item" %}
						<option value="R{% $item.id %}">{% $item.name %}</option>
					{% /foreach %}
					</select>
				</td>

				<td width='100'>
					<select id='adcategory'>
					{% foreach from = $categories item = "item" %}
						<option value="C{% $item.id %}">{% $item.name %}</option>
					{% /foreach %}
					</select>
				</td>
				
				<td width='16'><img src="/img/add.gif" onclick='addTask()'></td>
			</tr>

		</tbody>
		</table>

		
{% include file = "showMarkupBodyFooter.tpl.html" %}

