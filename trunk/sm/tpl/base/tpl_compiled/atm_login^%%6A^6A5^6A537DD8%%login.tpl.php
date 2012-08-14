<?php /* Smarty version 2.6.18, created on 2011-12-13 16:46:49
         compiled from login.tpl */ ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
		<script type="text/javascript" src="/js/sha1.js"></script>
	
		<script type="text/javascript">

			function doLogin(){
				var login = document.getElementById('login').value;
				var tmp = document.getElementById('password').value;
				
				var password = Sha1.hash(tmp);
				
				ajaxRequest(
					'/login/doLogin'
					,"login="+login+"&password="+password
					,function(responseObject){
						gotoLocation("/");
					}
					,function(responseObject){
						gotoLocation("/");
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
					doLogin();
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
				<td width='20'><img src="/img/img-user.png"/></td>
				<td width ='20'></td>
				<td><input name="login" type="text" class="text-input" id="login" onkeypress="keypressed(event)" /></td>
			<tr>
			<tr>
				<td width='10'></td>
				<td width='20'><img src="/img/img-lock.png"/></td>
				<td width ="20"></td>
				<td><input name="password" type="password" class="text-input" id="password" onkeypress="keypressed(event)" /></td>
			<tr>
		</table>
		<button onclick='doLogin()'>Login</button>
			
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyFooter.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	