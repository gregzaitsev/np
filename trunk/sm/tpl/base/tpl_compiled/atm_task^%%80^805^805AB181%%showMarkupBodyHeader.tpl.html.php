<?php /* Smarty version 2.6.18, created on 2011-12-13 16:44:18
         compiled from showMarkupBodyHeader.tpl.html */ ?>
<script type="text/javascript">

	function jumpToTaskKeyPressed(e){
	
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
			var tid = document.getElementById('taskIDToJump').value;
			window.location="/task/view?tid="+tid;
		}
	}

</script>
<body>
	<div class="header">
		<table border="0" width="100%">
			<tr>
				<td>
					<h1><?php echo $this->_tpl_vars['title']; ?>
</h1>
				</td>
				<td></td>
				<td width="100">
					Jump to task:
				</td>
				<td width="100">
					<input type="text" size="3" onkeypress="jumpToTaskKeyPressed(event)" id="taskIDToJump"></input>
				</td>
				<td width="100">
					<a href="/">Home</a>
				</td>
				<td width="100">
					<a href="/login/doLogout">Log out</a>
				</td>
			</tr>
		</table>
		<hr>
	</div>