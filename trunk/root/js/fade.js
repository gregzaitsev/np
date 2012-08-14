var curOpac = 0;
var fadeID = 0;
var FadeCallback = null;
function SetOpacity(id, value) {
	document.getElementById(id).style.opacity = value/10;
	document.getElementById(id).style.filter = 'alpha(opacity=' + value*10 + ')';
}
function FadeInLoop() {
	if (curOpac <= 10) {
		SetOpacity(fadeID, curOpac);
		curOpac+=1;
		setTimeout("FadeInLoop()", 10);
	} else if (FadeCallback != null) FadeCallback();
}
function FadeIn(id, callBack) {
	curOpac = 0;
	fadeID = id;
	FadeCallback = callBack;
	FadeInLoop();
}
function FadeOutLoop() {
	if (curOpac >= 0) {
		SetOpacity(fadeID, curOpac);
		curOpac-=1;
		setTimeout("FadeOutLoop()", 10);
	} else if (FadeCallback != null) FadeCallback();
}
function FadeOut(id, callBack) {
	curOpac = 10;
	fadeID = id;
	FadeCallback = callBack;
	FadeOutLoop();
}
