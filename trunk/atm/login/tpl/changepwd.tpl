        {% include file = "showMarkupHeader.tpl.html" %}
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
				var login = "{% $login %}";
				
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
	
	{% include file = "showMarkupLoginBodyHeader.tpl.html" %}

		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width='10'></td>
				<td width='150' style="text-align:right;">User</td>
				<td width ='20'></td>
				<td>{% $login %}</td>
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

	{% include file = "showMarkupBodyFooter.tpl.html" %}
	
