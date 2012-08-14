	{% include file = "showMarkupHeaderM.tpl.html" %}
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
        <script type="text/javascript">
			function openProject(id){
				gotoLocation("/project/tasks?pid="+id);
			}
        </script>
    </head>
	{% include file = "showMarkupBodyHeaderM.tpl.html" %}
		<CENTER>
		<table border='1' cellpadding='3' style='border-collapse:collapse;' width='250'>
			{% foreach from = $projects item = "item" %}
				<tr>
					<td style='text-align:center;' onclick='openProject({% $item.id %})' height='60'>{% $item.name %}</td>
				</tr>
			{% /foreach %}
		</table>
		</CENTER>
	{% include file = "showMarkupBodyFooter.tpl.html" %}