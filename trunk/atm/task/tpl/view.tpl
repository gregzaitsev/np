{% include file = "showMarkupHeader.tpl.html" %}
		<script type="text/javascript" src="/js/ajaxRequest.js"></script>
		<script type="text/javascript" src="/js/updateableElement.js"></script>
        <script type="text/javascript">

			// Setup updateable elements
			addUElement('name', ['nametext'], [1], ['nameedit'], ['nameeditctl'], '/task/doUpdateName', ['name'], '&tid={% $tid %}');
			addUElement('desc', ['desctext'], [1], ['descedit'], ['desceditctl'], '/task/doUpdateDesc', ['desc'], '&tid={% $tid %}');
			addUElement('steps', ['stepstext'], [1], ['stepsedit'], ['stepseditctl'], '/task/doUpdateSteps', ['steps'], '&tid={% $tid %}');
		
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
			
				var tid = {% $tid %};
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
				var tid = {% $tid %};
				var pid = {% $pid %};
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
				var tid = {% $tid %};
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
				var tid = {% $tid %};
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
				var tid = {% $tid %};
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
				var tid = {% $tid %};
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
				var tid = {% $tid %};
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
				var tid = {% $tid %};
				var pid = {% $pid %};
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
				var tid = {% $tid %};
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
				var tid = {% $tid %};
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
				var tid = {% $tid %};
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
				
				{% if ($endtime === false) %}
				var chk = document.getElementById('disableDL').checked = true;
				ToggleDeadline();
				{% else %}
				dly.selectedIndex = parseInt("{% $endyear %}") - 2011;
				dlm.selectedIndex = parseInt("{% $endmonth %}")-1;
				dld.selectedIndex = parseInt("{% $endday %}")-1;
				dlh.selectedIndex = parseInt("{% $endhour %}");
				dlmin.selectedIndex = parseInt("{% $endminute %}")/5;
				{% /if %}
				
				// Set current values for liveline
				var lly = document.getElementById('llsely');
				var llm = document.getElementById('llselm');
				var lld = document.getElementById('llseld');
				var llh = document.getElementById('llselh');
				var llmin = document.getElementById('llselmin');
				
				{% if ($starttime === false) %}
				var chk = document.getElementById('disableLL').checked = true;
				ToggleLiveline();
				{% else %}
				lly.selectedIndex = parseInt("{% $startyear %}") - 2011;
				llm.selectedIndex = parseInt("{% $startmonth %}")-1;
				lld.selectedIndex = parseInt("{% $startday %}")-1;
				llh.selectedIndex = parseInt("{% $starthour %}");
				llmin.selectedIndex = parseInt("{% $startminute %}")/5;
				{% /if %}
			}
			
			
			function goBack(){
				var pid = {% $pid %};
				switchEdit();
				gotoLocation("/project/tasks?pid="+pid);
			}
		
        </script>
    </head>
{% include file = "showMarkupBodyHeader.tpl.html" %}

		<button onclick="goBack()">< Go Back</button>
		<table border='0'>
		<tbody>
			<tr>
				<td width = '100' valign='top' bgcolor="#E0E0F0">Name</td>
				<td width = '800' height='30' bgcolor="#EEEEFF" valign="top">
					<div id="nametext" style="display:block" onclick="editName()">{% $tname %}</div>
					<div id="nameedit" style="display:none">
						<input id="nameeditctl" size="20"></input><br>
						<button onclick="saveName()">Save</button>
					</div>
				</td>
			</tr>
			<tr>
				<td width = '100' valign='top' bgcolor="#E0E0F0">Description</td>
				<td width = '800' height='100' bgcolor="#EEEEFF" valign="top">
					<div id="desctext" style="display:block; width:100%; height:100%;" onclick="editDescription()">{% $description %}</div>
					<div id="descedit" style="display:none">
						<textarea id="desceditctl" rows="5" cols="100">{% $description %}</textarea><br>
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
					{% foreach from = $dependencies item = "item" %}
						<tr>
							<td bgcolor="#EEFFFF" valign='top'>
								{% $item.task_id %}
							</td>
							<td bgcolor="#EEFFFF" valign='top'>
								{% $item.name %}
							</td>
							<td bgcolor="#EEFFFF" valign='top'>
								<img src="/img/delete.gif" onclick=''>
							</td>
						</tr>
					{% /foreach %}
					</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Add dependency</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="dependencysel">
					{% foreach from = $curTaskList item = "item" %}
						<option value="{% $item.id %}">{% $item.id %} : {% $item.name %}</option>
					{% /foreach %}
					</select>
					<img src="/img/add.gif" onclick='addTaskDependency()'>
				</td>
			</tr>

			<tr>
				<td valign='top' bgcolor="#E0E0F0">Release</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="releasesel" onchange="updateTaskRelease()">
					{% foreach from = $release item = "item" %}
						<option value="{% $item.id %}"{% if $item.selected == 1 %}selected="selected"{% /if %}>{% $item.name %}</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Status</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="statussel" onchange="updateTaskStatus()">
					{% foreach from = $status item = "item" %}
						<option value="{% $item.id %}"{% if $item.selected == 1 %}selected="selected"{% /if %}>{% $item.name %}</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Progress</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="progresssel" onchange="updateTaskProgress()">
					{% foreach from = $progressVals item = "item" %}
						<option value="{% $item %}"{% if $progress == $item %}selected="selected"{% /if %}>{% $item %}%</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Owner</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="ownersel" onchange="updateTaskOwner()">
					{% foreach from = $users item = "item" %}
						<option value="{% $item.id %}"{% if $item.selected == 1 %}selected="selected"{% /if %}>{% $item.firstname %} {% $item.lastname %}</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Category</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<select id="categorysel" onchange="updateTaskCategory()">
					{% foreach from = $category item = "item" %}
						<option value="{% $item.id %}"{% if $item.selected == 1 %}selected="selected"{% /if %}>{% $item.name %}</option>
					{% /foreach %}
					</select>
				</td>
			</tr>
			<tr>
				<td valign='top' bgcolor="#E0E0F0">Time Estimate</td>
				<td height='20' bgcolor="#EEEEFF" valign="top">
					<table>
					<tr>
						<td onclick="editTimest()" bgcolor="#EEFFFF">
							<div id="timesttext" style="display:block">{% $timest %}</div>
						</td>
						<td>
							<div id="timestedit" style="display:none">
								<input id="timesteditctl" size="5" value='{% $timest %}'></input>
								<button onclick="saveTimest()">Save</button>
							</div>
						</td>
						<td>
						+/-
						</td>
						<td>
							<select id="timestprecsel" onchange="saveTimest()">
								<option value="10" {% if $timest_prec == 10 %}selected="selected"{% /if %}>10%</option>
								<option value="20" {% if $timest_prec == 20 %}selected="selected"{% /if %}>20%</option>
								<option value="50" {% if $timest_prec == 50 %}selected="selected"{% /if %}>50%</option>
								<option value="100" {% if $timest_prec == 100 %}selected="selected"{% /if %}>100%</option>
								<option value="200" {% if $timest_prec == 200 %}selected="selected"{% /if %}>200%</option>
								<option value="500" {% if $timest_prec == 500 %}selected="selected"{% /if %}>500%</option>
								<option value="1000" {% if $timest_prec == 1000 %}selected="selected"{% /if %}>1000%</option>
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
							<option value="0" {% if $repeat.repeat_type == '' %}selected="selected"{% /if %}>No repeat</option>
							<option value="weekly" {% if $repeat.repeat_type == 'weekly' %}selected="selected"{% /if %}>Weekly</option>
							</select>
						</td>
						<td bgcolor="#EEFFFF">
							<input type="checkbox" id="rep_mo" {% if ($repeat.mo) %}checked{% /if %} onchange="saveRepeat()" />Mo
							<input type="checkbox" id="rep_tu" {% if ($repeat.tu) %}checked{% /if %} onchange="saveRepeat()" />Tu
							<input type="checkbox" id="rep_we" {% if ($repeat.we) %}checked{% /if %} onchange="saveRepeat()" />We
							<input type="checkbox" id="rep_th" {% if ($repeat.th) %}checked{% /if %} onchange="saveRepeat()" />Th
							<input type="checkbox" id="rep_fr" {% if ($repeat.fr) %}checked{% /if %} onchange="saveRepeat()" />Fr
							<input type="checkbox" id="rep_sa" {% if ($repeat.sa) %}checked{% /if %} onchange="saveRepeat()" />Sa
							<input type="checkbox" id="rep_su" {% if ($repeat.su) %}checked{% /if %} onchange="saveRepeat()" />Su
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
					{% foreach from = $notes item = "item" %}
						<tr>
							<td bgcolor="#EEFFFF" width='150' valign='top'>
								<table class="noteheader">
									<tr><td width='40'>ID</td><td>{% $item.id %}</td></td></tr>
									<tr><td valign="top">Author</td><td>{% $item.firstname %} {% $item.lastname %}</td></td></tr>
									<tr><td valign="top">Time</td><td>{% $item.dt %}</td></td></tr>
								</table>
							</td>
							<td bgcolor="#EEFFFF" valign='top'>{% $item.text %}</td>
						</tr>
					{% /foreach %}
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
						{% foreach from = $history item = "item" %}
						<tr>
							<td>{% $item.datetime %}</td>
							<td>{% $item.owner_name %}</td>
							<td>{% $item.action_name %}</td>
						</tr>
						{% /foreach %}
					</tbody>
					</table>
				</td>
			</tr>
		</tbody>
		</table>
		<button onclick="goBack()">< Go Back</button>

{% include file = "showMarkupBodyFooter.tpl.html" %}

