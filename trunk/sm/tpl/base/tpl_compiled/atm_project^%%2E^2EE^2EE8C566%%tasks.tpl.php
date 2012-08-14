<?php /* Smarty version 2.6.18, created on 2011-12-13 16:47:13
         compiled from tasks.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
		<script type="text/javascript" src="/js/cookies.js"></script>
		<script type="text/javascript" src="/js/fade.js"></script>
        <script type="text/javascript">
		
			var pid = <?php echo $this->_tpl_vars['pid']; ?>
;
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
				var pid = <?php echo $this->_tpl_vars['pid']; ?>
;
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
				var pid = <?php echo $this->_tpl_vars['pid']; ?>
;
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
				var pid = <?php echo $this->_tpl_vars['pid']; ?>
;

				setCookie('release'+pid, relid, 60);
				setCookie('category'+pid, catid, 60);
				setCookie('user'+pid, user_id, 60);
				setCookie('status'+pid, status_id, 60);
			}
			
			function loadCookies(){
				var pid = <?php echo $this->_tpl_vars['pid']; ?>
;
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
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<table border='0'>
		<tbody>
			<tr>
				<td width='100'>Releases:</td>
				<td width='100'>
					<select id='releases' onchange='LoadTasks()'>
					<option value="R0" selected="selected">ALL</option>
					<?php $_from = $this->_tpl_vars['releases']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="R<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
 (<?php echo $this->_tpl_vars['item']['date']; ?>
)</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td width='100'>Categories:</td>
				<td width='100'>
					<select id='categories' onchange='LoadTasks()'>
					<option value="C0" selected="selected">ALL</option>
					<?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="C<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td width='100'>Users:</td>
				<td width='100'>
					<select id='users' onchange='LoadTasks()'>
					<option value="U0" selected="selected">ALL</option>
					<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="U<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['firstname']; ?>
 <?php echo $this->_tpl_vars['item']['lastname']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
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
					<?php $_from = $this->_tpl_vars['statuses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="S<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
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
					<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="U<?php echo $this->_tpl_vars['item']['id']; ?>
" <?php if ($this->_tpl_vars['item']['lead']): ?>selected='selected'<?php endif; ?> ><?php echo $this->_tpl_vars['item']['firstname']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>

				<td width='100'>
					<select id='adrelease'>
					<?php $_from = $this->_tpl_vars['futurereleases']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="R<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>

				<td width='100'>
					<select id='adcategory'>
					<?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="C<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
				
				<td width='16'><img src="/img/add.gif" onclick='addTask()'></td>
			</tr>

		</tbody>
		</table>

		
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyFooter.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
