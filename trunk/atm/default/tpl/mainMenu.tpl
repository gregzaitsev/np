	{% include file = "showMarkupHeader.tpl.html" %}
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
        <script type="text/javascript">
			function openProject(id){
				gotoLocation("/project/tasks?pid="+id);
			}
			function editProject(id){
				gotoLocation("/project/edit?pid="+id);
			}
        </script>
    </head>
	{% include file = "showMarkupBodyHeader.tpl.html" %}

		<table border='0' cellpadding='0' cellspacing='0'>
		<tr>
			<td width='10'></td>
			<td><a href="/project/add">New project</a></td>
			<td width='20' style="text-align:center;">|</td>
			<td><a href="/notifications">Notifications</a></td>
			<td width='20' style="text-align:center;">|</td>
			<td><a href="/activity">Recent Activity</a></td>
			<td width='20' style="text-align:center;">|</td>
			<td><a href="/login/changePwd">Change Password</a></td>
			<td width='20' style="text-align:center;">|</td>
			<td><a href="/tasks">Tasks</a></td>
		</tr>
		</table>
		<hr>
		<h2>Projects</h2>
		<table border='0' cellpadding='3'>
			<tr>
				<th width='16'></td>
				<th width='16'></td>
				<th width='200'>Name</th>
				<th>Lead</th>
			</tr>
			{% foreach from = $projects item = "item" %}
				<tr>
					<td><img src="/img/edit.gif" onclick='editProject({% $item.id %})'></td>
					<td><img src="/img/task.png" onclick="openProject({% $item.id %})"></td>
					<td onclick='openProject({% $item.id %})'>{% $item.name %}</td>
					<td>{% $item.leadname %}</td>
				</tr>
			{% /foreach %}
		</table>

		<hr>
		<img src="/img/status_online.gif"> <b>online:</b> {% foreach from = $onlineusers item = "item" %}{% $item %} {% /foreach %}
		
	{% include file = "showMarkupBodyFooter.tpl.html" %}

