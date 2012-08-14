<?php /* Smarty version 2.6.18, created on 2011-12-13 16:47:01
         compiled from changepwd.tpl */ ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
		<script type="text/javascript" src="/js/sha1.js"></script>
	
		<script type="text/javascript">

			function doSave(){
			
				var passwordold = Sha1.hash(document.getElementById('passwordold').value);
				var password1 = document.getElementById('password1').value;
				var password2 = document.getElementById('password2').value;
				
				if (password1 != password2) {
					alert("Passwords don't match");
					return;
				}
				if (password1.length < 5) {
					alert("New password is too short (should be longer then 5 characters)");
					return;
				}
				password1 = Sha1.hash(password1);
				password2 = Sha1.hash(password2);
				var login = "<?php echo $this->_tpl_vars['login']; ?>
";
				
				ajaxRequest(
					'/login/doChangePwd'
					,"login="+login+"&passwordold="+passwordold+"&passwordnew="+password1
					,function(responseObject){
					
						var ajaxerror = '';
						eval(responseObject);
						if ((ajaxerror != '') && (ajaxerror != '0')) {
							gotoLocation("/login/changePwd");
						} else {
							alert("OK, Password changed");
							gotoLocation("/");
						}
					}
					,function(responseObject){
						gotoLocation("/login/changePwd");
					}
				);
			}
			
			function keypressed(e) {
			
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
					doSave();
				}
			}

			
		</script>
        
	</head>
	
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupLoginBodyHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width='10'></td>
				<td width='150' style="text-align:right;">User</td>
				<td width ='20'></td>
				<td><?php echo $this->_tpl_vars['login']; ?>
</td>
			<tr>
			<tr>
				<td width='10'></td>
				<td width='150' style="text-align:right;">Old password</td>
				<td width ="20"></td>
				<td><input name="passwordold" type="password" class="text-input" id="passwordold" onkeypress="keypressed(event)" /></td>
			<tr>
			<tr>
				<td width='10'></td>
				<td width='150' style="text-align:right;">New password</td>
				<td width ="20"></td>
				<td><input name="password1" type="password" class="text-input" id="password1" onkeypress="keypressed(event)" /></td>
			<tr>
			<tr>
				<td width='10'></td>
				<td width='150' style="text-align:right;">Ret-type new password</td>
				<td width ="20"></td>
				<td><input name="password2" type="password" class="text-input" id="password2" onkeypress="keypressed(event)" /></td>
			<tr>
		</table>
		<button onclick='doSave()'>Change Password</button>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyFooter.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	