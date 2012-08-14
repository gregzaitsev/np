<?php /* Smarty version 2.6.18, created on 2011-12-21 14:08:54
         compiled from view.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
		<script type="text/javascript" src="/js/updateableElement.js"></script>
        <script type="text/javascript">

			// Setup updateable elements
			addUElement('name', ['nametext'], [1], ['nameedit'], ['nameeditctl'], '/task/doUpdateName', ['name'], '&tid=<?php echo $this->_tpl_vars['tid']; ?>
');
			addUElement('desc', ['desctext'], [1], ['descedit'], ['desceditctl'], '/task/doUpdateDesc', ['desc'], '&tid=<?php echo $this->_tpl_vars['tid']; ?>
');
			addUElement('steps', ['stepstext'], [1], ['stepsedit'], ['stepseditctl'], '/task/doUpdateSteps', ['steps'], '&tid=<?php echo $this->_tpl_vars['tid']; ?>
');
		
			var fedDesc = false;
			var fedSteps = false;
			var fedName = false;
			var fedTimest = false;
			
			function switchEdit(){
				if (fedDesc) saveDescription();
				if (fedSteps) saveSteps();
				if (fedName) saveName();
				if (fedTimest) saveTimest();
			}
			
			function editDescription() {
				if (fedDesc) return;
				switchEdit();
				onUElementEdit('desc');
				fedDesc = true;
			}
			function saveDescription() {
				onUElementSave('desc');
				fedDesc = false;
			}
			
			function editSteps() {
				if (fedSteps) return;
				switchEdit();
				onUElementEdit('steps');
				fedSteps = true;
			}
			function saveSteps() {
				onUElementSave('steps');
				fedSteps = false;
			}

			function editName() {
				if (fedName) return;
				switchEdit();
				onUElementEdit('name');
				fedName = true;
			}
			function saveName() {
				onUElementSave('name');
				fedName = false;
			}

			
			function editTimest() {
				if (fedTimest) return;
				switchEdit();

				var edittext = document.getElementById('timesttext').innerHTML;
				
				document.getElementById('timesteditctl').value = edittext;
				document.getElementById('timesttext').style.display = 'none';
				document.getElementById('timestedit').style.display = 'block';
				document.getElementById('timesteditctl').focus();
				fedTimest = true;
			}
			function saveTimest() {
			
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var newText;
				
				if (fedTimest) newText = document.getElementById('timesteditctl').value;
				else newText = document.getElementById('timesttext').innerHTML;
				
				var newPrec = document.getElementById('timestprecsel').value;
				
				ajaxRequest(
					'/task/doUpdateEstimate'
					,"tid="+tid+"&timest="+newText.valueOf()+"&prec="+newPrec
					,function(responseObject){
						document.getElementById('timesttext').innerHTML = newText.valueOf();
						document.getElementById('timesttext').style.display = 'block';
						document.getElementById('timestedit').style.display = 'none';
						fedTimest = false;
					}
					,function(responseObject){
						document.getElementById('timesttext').innerHTML = newText.valueOf();
						document.getElementById('timesttext').style.display = 'block';
						document.getElementById('timestedit').style.display = 'none';
						fedTimest = false;
					}
				);

			}
			
			function addNote(){
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var pid = <?php echo $this->_tpl_vars['pid']; ?>
;
				var tmp = document.getElementById('newnotetext').value;
				var newTextToDisplay = tmp.replace(new RegExp( "\n", "g" ), "<br>");
				var newText = escape(newTextToDisplay);
				
				if (newText.length == 0) return;
				
				switchEdit();
				
				ajaxRequest(
					'/task/doAddNote'
					,"tid="+tid+"&text="+newText
					,function(responseObject){
						gotoLocation("/task/view?tid="+tid+"&pid="+pid);
					}
					,function(responseObject){
						gotoLocation("/task/view?tid="+tid+"&pid="+pid);
					}
				);
			}
			
			function updateTaskStatus(){
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var newStatus = document.getElementById('statussel').value;
				
				ajaxRequest(
					'/task/doUpdateStatus'
					,"tid="+tid+"&status="+newStatus
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
				
				var updateProgress = false;
				var progress = document.getElementById('progresssel').value;
				if ((newStatus == 1) && (progress > 0)) {
					document.getElementById('progresssel').value = 0;
					updateProgress = true;
				}
				if ((newStatus == 2) && (progress == 0)) {
					document.getElementById('progresssel').value = 10;
					updateProgress = true;
				}
				if ((newStatus == 2) && (progress == 100)) {
					document.getElementById('progresssel').value = 90;
					updateProgress = true;
				}
				if ((newStatus >= 3) && (progress < 100)) {
					document.getElementById('progresssel').value = 100;
					updateProgress = true;
				}
				
				if (updateProgress) updateTaskProgress();
			}
			
			function updateTaskProgress(){
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var newProgress = document.getElementById('progresssel').value;
			
				ajaxRequest(
					'/task/doUpdateProgress'
					,"tid="+tid+"&progress="+newProgress
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
				
				var updateStatus = false;
				var status = document.getElementById('statussel').value;
				if ((newProgress == 100) && (status < 3)) {
					document.getElementById('statussel').value = 3;
					updateStatus = true;
				}
				if ((newProgress == 0) && (status != 1)) {
					document.getElementById('statussel').value = 1; // new
					updateStatus = true;
				}
				if ((newProgress > 0) && (newProgress < 100) && (status != 2)) {
					document.getElementById('statussel').value = 2; // in process
					updateStatus = true;
				}
				
				if (updateStatus) updateTaskStatus();
			}
			
			function updateTaskRelease(){
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var newRelease = document.getElementById('releasesel').value;
				
				ajaxRequest(
					'/task/doUpdateRelease'
					,"tid="+tid+"&release="+newRelease
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
			}
			
			function updateTaskOwner(){
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var newOwner = document.getElementById('ownersel').value;
				
				ajaxRequest(
					'/task/doUpdateOwner'
					,"tid="+tid+"&owner="+newOwner
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
			}
			
			function updateTaskCategory(){
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var newCategory = document.getElementById('categorysel').value;
				
				ajaxRequest(
					'/task/doUpdateCategory'
					,"tid="+tid+"&category="+newCategory
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
			}
			
			function addTaskDependency(){
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var pid = <?php echo $this->_tpl_vars['pid']; ?>
;
				var newdep = document.getElementById('dependencysel').value;
				
				ajaxRequest(
					'/task/doAddDependency'
					,"tid="+tid+"&dependency="+newdep
					,function(responseObject){
						gotoLocation("/task/view?tid="+tid+"&pid="+pid);
					}
					,function(responseObject){
						gotoLocation("/task/view?tid="+tid+"&pid="+pid);
					}
				);
			}
			
			function fillLLSelH(elid) {
				var x=document.getElementById(elid+'h');
				var i;

				for (i=0; i<24; i++) {
					var option=document.createElement("option");
					option.text = i;
					option.value = i;
					try
					{
						// for IE earlier than version 8
						x.add(option,x.options[null]);
					}
					catch (e)
					{
						x.add(option,null);
					}
				}
			}
			
			function fillLLSelMin(elid) {
				var x=document.getElementById(elid+'min');
				var i;

				for (i=0; i<60; i+=5) {
					var option=document.createElement("option");
					option.text = i;
					option.value = i;
					try
					{
						// for IE earlier than version 8
						x.add(option,x.options[null]);
					}
					catch (e)
					{
						x.add(option,null);
					}
				}
			}

			function fillLLSelM(elid) {
				var x=document.getElementById(elid+'m');
				var months = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
				var i;

				for (i=0; i<12; i++) {
					var option=document.createElement("option");
					option.text = months[i];
					option.value = parseInt(i)+1;
					try
					{
						// for IE earlier than version 8
						x.add(option,x.options[null]);
					}
					catch (e)
					{
						x.add(option,null);
					}
				}
			}
			
			function fillLLSelY(elid) {
				var x=document.getElementById(elid+'y');
				var years = new Array("2011","2012","2013","2014","2015");
				var i;

				for (i=0; i<5; i++) {
					var option=document.createElement("option");
					option.text = years[i];
					option.value = years[i];
					try
					{
						// for IE earlier than version 8
						x.add(option,x.options[null]);
					}
					catch (e)
					{
						x.add(option,null);
					}
				}
			}
			
			function fillLLSelD(elid) {
				var m=document.getElementById(elid+'m').value;
				var y=document.getElementById(elid+'y').value;
				var x=document.getElementById(elid+'d');
				var monthlastday = 31;
				if ((m == 4) || (m == 6) || (m == 9) || (m == 11)) monthlastday = 30;
				else if ((m == 2) && (!(y % 4))) monthlastday = 29;
				else if (m == 2) monthlastday = 28;
				
				// clear
				var sz = x.length;
				for (i=0; i<sz; i++)
				{
					x.remove(0);
				}
				
				// repopulate
				var i;
				for (i=0; i<monthlastday; i++) {
					var option=document.createElement("option");
					option.text=i+1;

					try
					{
						// for IE earlier than version 8
						x.add(option,x.options[null]);
					}
					catch (e)
					{
						x.add(option,null);
					}
				}
			}
			
			function saveLL() {

				// get selections
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var d = parseInt(document.getElementById('llseld').value);
				var m = parseInt(document.getElementById('llselm').value);
				var y = parseInt(document.getElementById('llsely').value);
				var h = parseInt(document.getElementById('llselh').value);
				var min = parseInt(document.getElementById('llselmin').value);
				
				fillLLSelD('llsel');
				document.getElementById('llseld').selectedIndex = d-1;
				
				var chk = document.getElementById('disableLL').checked;
				if (chk) y = 0;
				
				ajaxRequest(
					'/task/doUpdateLiveline'
					,"tid="+tid+"&d="+d+"&m="+m+"&y="+y+"&h="+h+"&min="+min
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
				
			}

			function saveDL() {
				// get selections
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var d = parseInt(document.getElementById('dlseld').value);
				var m = parseInt(document.getElementById('dlselm').value);
				var y = parseInt(document.getElementById('dlsely').value);
				var h = parseInt(document.getElementById('dlselh').value);
				var min = parseInt(document.getElementById('dlselmin').value);
				
				fillLLSelD('dlsel');
				document.getElementById('dlseld').selectedIndex = d-1;
				
				var chk = document.getElementById('disableDL').checked;
				if (chk) y = 0;
				
				ajaxRequest(
					'/task/doUpdateDeadline'
					,"tid="+tid+"&d="+d+"&m="+m+"&y="+y+"&h="+h+"&min="+min
					,function(responseObject){
					}
					,function(responseObject){
					}
				);

			}
			
			function ToggleDeadline () 
			{
				var chk = document.getElementById('disableDL').checked;
				var dis = false;
				if (chk) dis = true;

				document.getElementById('dlseld').disabled=dis;
				document.getElementById('dlselm').disabled=dis;
				document.getElementById('dlsely').disabled=dis;
				document.getElementById('dlselh').disabled=dis;
				document.getElementById('dlselmin').disabled=dis;
				
				saveDL();
			}

			function ToggleLiveline () 
			{
				var chk = document.getElementById('disableLL').checked;
				var dis = false;
				if (chk) dis = true;

				document.getElementById('llseld').disabled=dis;
				document.getElementById('llselm').disabled=dis;
				document.getElementById('llsely').disabled=dis;
				document.getElementById('llselh').disabled=dis;
				document.getElementById('llselmin').disabled=dis;
				
				saveLL();
			}
			
			function saveRepeat ()
			{
				// get type selection
				var tid = <?php echo $this->_tpl_vars['tid']; ?>
;
				var rt = document.getElementById('repeat_type_sel').value;
				var chkmo = document.getElementById('rep_mo').checked?1:0;
				var chktu = document.getElementById('rep_tu').checked?1:0;
				var chkwe = document.getElementById('rep_we').checked?1:0;
				var chkth = document.getElementById('rep_th').checked?1:0;
				var chkfr = document.getElementById('rep_fr').checked?1:0;
				var chksa = document.getElementById('rep_sa').checked?1:0;
				var chksu = document.getElementById('rep_su').checked?1:0;
				
				ajaxRequest(
					'/task/doUpdateRepeat'
					,"tid="+tid+"&repeat_type="+rt+"&mo="+chkmo+"&tu="+chktu+"&we="+chkwe+"&th="+chkth+"&fr="+chkfr+"&sa="+chksa+"&su="+chksu
					,function(responseObject){
					}
					,function(responseObject){
					}
				);
			}

			window.onload = function ()
			{
				fillLLSelM('llsel');
				fillLLSelY('llsel');
				fillLLSelD('llsel');
				fillLLSelH('llsel');
				fillLLSelMin('llsel');
				
				fillLLSelM('dlsel');
				fillLLSelY('dlsel');
				fillLLSelD('dlsel');
				fillLLSelH('dlsel');
				fillLLSelMin('dlsel');
				
				// Set current values for deadline
				var dly = document.getElementById('dlsely');
				var dlm = document.getElementById('dlselm');
				var dld = document.getElementById('dlseld');
				var dlh = document.getElementById('dlselh');
				var dlmin = document.getElementById('dlselmin');
				
				<?php if (( $this->_tpl_vars['endtime'] === false )): ?>
				var chk = document.getElementById('disableDL').checked = true;
				ToggleDeadline();
				<?php else: ?>
				dly.selectedIndex = parseInt("<?php echo $this->_tpl_vars['endyear']; ?>
") - 2011;
				dlm.selectedIndex = parseInt("<?php echo $this->_tpl_vars['endmonth']; ?>
")-1;
				dld.selectedIndex = parseInt("<?php echo $this->_tpl_vars['endday']; ?>
")-1;
				dlh.selectedIndex = parseInt("<?php echo $this->_tpl_vars['endhour']; ?>
");
				dlmin.selectedIndex = parseInt("<?php echo $this->_tpl_vars['endminute']; ?>
")/5;
				<?php endif; ?>
				
				// Set current values for liveline
				var lly = document.getElementById('llsely');
				var llm = document.getElementById('llselm');
				var lld = document.getElementById('llseld');
				var llh = document.getElementById('llselh');
				var llmin = document.getElementById('llselmin');
				
				<?php if (( $this->_tpl_vars['starttime'] === false )): ?>
				var chk = document.getElementById('disableLL').checked = true;
				ToggleLiveline();
				<?php else: ?>
				lly.selectedIndex = parseInt("<?php echo $this->_tpl_vars['startyear']; ?>
") - 2011;
				llm.selectedIndex = parseInt("<?php echo $this->_tpl_vars['startmonth']; ?>
")-1;
				lld.selectedIndex = parseInt("<?php echo $this->_tpl_vars['startday']; ?>
")-1;
				llh.selectedIndex = parseInt("<?php echo $this->_tpl_vars['starthour']; ?>
");
				llmin.selectedIndex = parseInt("<?php echo $this->_tpl_vars['startminute']; ?>
")/5;
				<?php endif; ?>
			}
			
			
			function goBack(){
				var pid = <?php echo $this->_tpl_vars['pid']; ?>
;
				switchEdit();
				gotoLocation("/project/tasks?pid="+pid);
			}
		
        </script>
    </head>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyHeader.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<button onclick="goBack()">< Go Back</button>
		<table border='0'>
		<tbody>
			<tr>
				<td width = '100' valign='top' bgcolor="#E0E0F0">Name</td>
				<td width = '800' height='30' bgcolor="#EEEEFF" valign="top">
					<div id="nametext" style="display:block" onclick="editName()"><?php echo $this->_tpl_vars['tname']; ?>
</div>
					<div id="nameedit" style="display:none">
						<input id="nameeditctl" size="20"></input><br>
						<button onclick="saveName()">Save</button>
					</div>
				</td>
			</tr>
			<tr>
				<td width = '100' valign='top' bgcolor="#E0E0F0">Description</td>
				<td width = '800' height='100' bgcolor="#EEEEFF" valign="top">
					<div id="desctext" style="display:block; width:100%; height:100%;" onclick="editDescription()"><?php echo $this->_tpl_vars['description']; ?>
</div>
					<div id="descedit" style="display:none">
						<textarea id="desceditctl" rows="5" cols="100"><?php echo $this->_tpl_vars['description']; ?>
</textarea><br>
						<button onclick="saveDescription()">Save</button>
					</div>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Depends On</td>
				<td bgcolor="#EEEEFF">
					<table>
					<tbody>
					<tr><td width='40'>ID</td><td width='150'>Name</td><td width='30'></td></tr>
					<?php $_from = $this->_tpl_vars['dependencies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<tr>
							<td bgcolor="#EEFFFF" valign='top'>
								<?php echo $this->_tpl_vars['item']['task_id']; ?>

							</td>
							<td bgcolor="#EEFFFF" valign='top'>
								<?php echo $this->_tpl_vars['item']['name']; ?>

							</td>
							<td bgcolor="#EEFFFF" valign='top'>
								<img src="/img/delete.gif" onclick=''>
							</td>
						</tr>
					<?php endforeach; endif; unset($_from); ?>
					</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Add dependency</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="dependencysel">
					<?php $_from = $this->_tpl_vars['curTaskList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"><?php echo $this->_tpl_vars['item']['id']; ?>
 : <?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
					<img src="/img/add.gif" onclick='addTaskDependency()'>
				</td>
			</tr>

			<tr>
				<td valign='top' bgcolor="#E0E0F0">Release</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="releasesel" onchange="updateTaskRelease()">
					<?php $_from = $this->_tpl_vars['release']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if ($this->_tpl_vars['item']['selected'] == 1): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Status</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="statussel" onchange="updateTaskStatus()">
					<?php $_from = $this->_tpl_vars['status']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if ($this->_tpl_vars['item']['selected'] == 1): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Progress</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="progresssel" onchange="updateTaskProgress()">
					<?php $_from = $this->_tpl_vars['progressVals']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="<?php echo $this->_tpl_vars['item']; ?>
"<?php if ($this->_tpl_vars['progress'] == $this->_tpl_vars['item']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']; ?>
%</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Owner</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="ownersel" onchange="updateTaskOwner()">
					<?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if ($this->_tpl_vars['item']['selected'] == 1): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['firstname']; ?>
 <?php echo $this->_tpl_vars['item']['lastname']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Category</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="categorysel" onchange="updateTaskCategory()">
					<?php $_from = $this->_tpl_vars['category']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<option value="<?php echo $this->_tpl_vars['item']['id']; ?>
"<?php if ($this->_tpl_vars['item']['selected'] == 1): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['item']['name']; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Time Estimate</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<table>
					<tr>
						<td onclick="editTimest()" bgcolor="#EEFFFF">
							<div id="timesttext" style="display:block"><?php echo $this->_tpl_vars['timest']; ?>
</div>
						</td>
						<td>
							<div id="timestedit" style="display:none">
								<input id="timesteditctl" size="5" value='<?php echo $this->_tpl_vars['timest']; ?>
'></input>
								<button onclick="saveTimest()">Save</button>
							</div>
						</td>
						<td>
						+/-
						</td>
						<td>
							<select id="timestprecsel" onchange="saveTimest()">
								<option value="10" <?php if ($this->_tpl_vars['timest_prec'] == 10): ?>selected="selected"<?php endif; ?>>10%</option>
								<option value="20" <?php if ($this->_tpl_vars['timest_prec'] == 20): ?>selected="selected"<?php endif; ?>>20%</option>
								<option value="50" <?php if ($this->_tpl_vars['timest_prec'] == 50): ?>selected="selected"<?php endif; ?>>50%</option>
								<option value="100" <?php if ($this->_tpl_vars['timest_prec'] == 100): ?>selected="selected"<?php endif; ?>>100%</option>
								<option value="200" <?php if ($this->_tpl_vars['timest_prec'] == 200): ?>selected="selected"<?php endif; ?>>200%</option>
								<option value="500" <?php if ($this->_tpl_vars['timest_prec'] == 500): ?>selected="selected"<?php endif; ?>>500%</option>
								<option value="1000" <?php if ($this->_tpl_vars['timest_prec'] == 1000): ?>selected="selected"<?php endif; ?>>1000%</option>
							</select>
							HH:MM 
						</td>
						
					</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Lifeline</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<table>
					<tr>
						<td bgcolor="#EEFFFF">
							<select id="llseld" onchange="saveLL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="llselm" onchange="saveLL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="llsely" onchange="saveLL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="llselh" onchange="saveLL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="llselmin" onchange="saveLL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<input type="checkbox" id="disableLL" value="disLL" onchange="ToggleLiveline()" /> No lifeline
						</td>
						
					</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td valign='top' bgcolor="#E0E0F0">Deadline</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<table>
					<tr>
						<td bgcolor="#EEFFFF">
							<select id="dlseld" onchange="saveDL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="dlselm" onchange="saveDL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="dlsely" onchange="saveDL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="dlselh" onchange="saveDL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<select id="dlselmin" onchange="saveDL()">
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<input type="checkbox" id="disableDL" value="disDL" onchange="ToggleDeadline()" /> No deadline
						</td>
						
					</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Repeat</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<table>
					<tr>
						<td bgcolor="#EEFFFF">
							<select id="repeat_type_sel" onchange="saveRepeat()">
							<option value="0" <?php if ($this->_tpl_vars['repeat']['repeat_type'] == ''): ?>selected="selected"<?php endif; ?>>No repeat</option>
							<option value="weekly" <?php if ($this->_tpl_vars['repeat']['repeat_type'] == 'weekly'): ?>selected="selected"<?php endif; ?>>Weekly</option>
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<input type="checkbox" id="rep_mo" <?php if (( $this->_tpl_vars['repeat']['mo'] )): ?>checked<?php endif; ?> onchange="saveRepeat()" />Mo
							<input type="checkbox" id="rep_tu" <?php if (( $this->_tpl_vars['repeat']['tu'] )): ?>checked<?php endif; ?> onchange="saveRepeat()" />Tu
							<input type="checkbox" id="rep_we" <?php if (( $this->_tpl_vars['repeat']['we'] )): ?>checked<?php endif; ?> onchange="saveRepeat()" />We
							<input type="checkbox" id="rep_th" <?php if (( $this->_tpl_vars['repeat']['th'] )): ?>checked<?php endif; ?> onchange="saveRepeat()" />Th
							<input type="checkbox" id="rep_fr" <?php if (( $this->_tpl_vars['repeat']['fr'] )): ?>checked<?php endif; ?> onchange="saveRepeat()" />Fr
							<input type="checkbox" id="rep_sa" <?php if (( $this->_tpl_vars['repeat']['sa'] )): ?>checked<?php endif; ?> onchange="saveRepeat()" />Sa
							<input type="checkbox" id="rep_su" <?php if (( $this->_tpl_vars['repeat']['su'] )): ?>checked<?php endif; ?> onchange="saveRepeat()" />Su
						</td>
						
					</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Notes</td>
				<td bgcolor="#EEEEFF">
					<table width='100%'>
					<tbody>
					<?php $_from = $this->_tpl_vars['notes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<tr>
							<td bgcolor="#EEFFFF" width='150' valign='top'>
								<table class="noteheader">
									<tr><td width='40'>ID</td><td><?php echo $this->_tpl_vars['item']['id']; ?>
</td></td></tr>
									<tr><td valign="top">Author</td><td><?php echo $this->_tpl_vars['item']['firstname']; ?>
 <?php echo $this->_tpl_vars['item']['lastname']; ?>
</td></td></tr>
									<tr><td valign="top">Time</td><td><?php echo $this->_tpl_vars['item']['dt']; ?>
</td></td></tr>
								</table>
							</td>
							<td bgcolor="#EEFFFF" valign='top'><?php echo $this->_tpl_vars['item']['text']; ?>
</td>
						</tr>
					<?php endforeach; endif; unset($_from); ?>
					</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Add Note</td>
				<td bgcolor="#EEEEFF">
					<table width='100%'>
					<tbody>
						<tr>
							<td width='200'><textarea id="newnotetext" rows="8" cols="100"></textarea></td>
							<td width='16' valign='bottom'><img src="/img/add.gif" onclick='addNote()'></td>
						</tr>
					</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">History</td>
				<td bgcolor="#EEEEFF">
					<table width='100%' class='history'>
					<thead>
					<th width='100'>Date</th>
					<th width='100'>User</th>
					<th>Action</th>
					</thead>
					<tbody>
						<?php $_from = $this->_tpl_vars['history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<tr>
							<td><?php echo $this->_tpl_vars['item']['datetime']; ?>
</td>
							<td><?php echo $this->_tpl_vars['item']['owner_name']; ?>
</td>
							<td><?php echo $this->_tpl_vars['item']['action_name']; ?>
</td>
						</tr>
						<?php endforeach; endif; unset($_from); ?>
					</tbody>
					</table>
				</td>
			</tr>
		</tbody>
		</table>
		<button onclick="goBack()">< Go Back</button>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "showMarkupBodyFooter.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
