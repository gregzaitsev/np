<?php /* Smarty version 2.6.18, created on 2011-12-22 13:11:20
         compiled from tasks.tpl */ ?>
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

		<?php if (( $this->_tpl_vars['scheduleOK'] == 1 )): ?>
		<table border='1' cellpadding='5' cellspacing='0'>
			<?php $_from = $this->_tpl_vars['tasks']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
				<tr>
					<td>
						<?php echo $this->_tpl_vars['item']['actual_start_date']; ?>
<br>
						<?php echo $this->_tpl_vars['item']['actual_start_time']; ?>

					</td>
					<td onclick='openProject(<?php echo $this->_tpl_vars['item']['id']; ?>
)'>
						<?php echo $this->_tpl_vars['item']['name']; ?>
<br>
						<?php echo $this->_tpl_vars['item']['owner_first_name']; ?>
 <?php echo $this->_tpl_vars['item']['owner_last_name']; ?>

					</td>
				</tr>
			<?php endforeach; endif; unset($_from); ?>
		</table>
		<?php else: ?>
		Problem with tasks: <?php echo $this->_tpl_vars['troublemakers']; ?>
<br>
		Possible causes:<br>
		1. Check deadlines<br>
		2. Check for loops in task dependencies<br>
		<?php endif; ?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyFooter.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
