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

		{% if ($scheduleOK == 1) %}
		<table border='1' cellpadding='5' cellspacing='0'>
			{% foreach from = $tasks item = "item" %}
				<tr>
					<td>
						{% $item.actual_start_date %}<br>
						{% $item.actual_start_time %}
					</td>
					<td onclick='openProject({% $item.id %})'>
						{% $item.name %}<br>
						{% $item.owner_first_name %} {% $item.owner_last_name %}
					</td>
				</tr>
			{% /foreach %}
		</table>
		{% else %}
		Problem with tasks: {% $troublemakers %}<br>
		Possible causes:<br>
		1. Check deadlines<br>
		2. Check for loops in task dependencies<br>
		{% /if %}

	{% include file = "showMarkupBodyFooter.tpl.html" %}

