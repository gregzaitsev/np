{% include file = "showMarkupHeader.tpl.html" %}
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
        <script type="text/javascript">
		
			function goBack(){
				gotoLocation("/");
			}
		
        </script>
    </head>
{% include file = "showMarkupBodyHeader.tpl.html" %}
		
		<button onclick="goBack()">< Go Back</button>
		<table border="1" width='100%' class='fullhistory'>
		<thead>
		<th style='min-width:150'>Date</th>
		<th style='min-width:120'>User</th>
		<th style='min-width:100'>Project</th>
		<th style='min-width:150'>Task</th>
		<th style='min-width:300'>Action</th>
		</thead>
		<tbody>
			{% foreach from = $history item = "item" %}
			<tr>
				<td>{% $item.datetime %}</td>
				<td>{% $item.owner_name %}</td>
				<td>{% $item.project_name %}</td>
				<td>{% $item.task_name %}</td>
				<td>{% $item.action_name %}</td>
			</tr>
			{% /foreach %}
		</tbody>
		</table>
		<button onclick="goBack()">< Go Back</button>

{% include file = "showMarkupBodyFooter.tpl.html" %}

