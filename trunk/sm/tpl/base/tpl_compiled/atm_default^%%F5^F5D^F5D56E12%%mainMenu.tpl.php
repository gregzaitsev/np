<?php /* Smarty version 2.6.18, created on 2011-12-13 16:47:09
         compiled from mainMenu.tpl */ ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

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
			<?php $_from = $this->_tpl_vars['projects']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
				<tr>
					<td><img src="/img/edit.gif" onclick='editProject(<?php echo $this->_tpl_vars['item']['id']; ?>
)'></td>
					<td><img src="/img/task.png" onclick="openProject(<?php echo $this->_tpl_vars['item']['id']; ?>
)"></td>
					<td onclick='openProject(<?php echo $this->_tpl_vars['item']['id']; ?>
)'><?php echo $this->_tpl_vars['item']['name']; ?>
</td>
					<td><?php echo $this->_tpl_vars['item']['leadname']; ?>
</td>
				</tr>
			<?php endforeach; endif; unset($_from); ?>
		</table>

		<hr>
		<img src="/img/status_online.gif"> <b>online:</b> <?php $_from = $this->_tpl_vars['onlineusers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?><?php echo $this->_tpl_vars['item']; ?>
 <?php endforeach; endif; unset($_from); ?>
		
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyFooter.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
