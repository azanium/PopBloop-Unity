
<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/UnityObject.js"></script>
<script type="text/javascript">

function GetUnity() {
	if (typeof unityObject != "undefined") {
		return unityObject.getObjectById("unityPlayer");
	}
	return null;
}
if (typeof unityObject != "undefined") {
	var params = {
		disableContextMenu: true,
		backgroundcolor: "FFFFFF",
		bordercolor: "00CCFF",
		textcolor: "00CCFF",
		logoimage: "<?php print($this->basepath); ?>bundles/webplayer/images/lilologo.png",
		progressbarimage: "<?php print($this->basepath); ?>bundles/webplayer/images/progressbar.png",
		progressframeimage: "<?php print($this->basepath); ?>bundles/webplayer/images/progressframe.png"
	};
	unityObject.embedUnity("unityPlayer", "<?php print($this->basepath); ?>bundles/webplayer/Play.unity3d", 800, 480, params);
	
}
function OnLiloLoaded() {
//	$("#config_form").show();

//	GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", "wwwwww");
}

function get_session_id(){
	var session_id = "<?php echo $this->session_id; ?>";
//	alert(session_id);
	GetUnity().SendMessage("_Game", "GetUserId", session_id);
}

</script>

<div class="centered shadow transbg" style="width:960px;">

	<div style="float:left; width:960px; height:28px;">

  </div>

	<div style="float:left; width:60px; text-align:center;" id="div_webplayer" class="centered">
  	<img src="<?php print($this->basepath); ?>modules/000_user_interface/images/play.the.game.medium.png" />
  </div>
	<div style="float:left; width:440px; text-align:center;" id="div_webplayer" class="centered">
		
		<div class="content" class="centered shadow">
			<div id="unityPlayer" class="centered shadow">
				<div class="missing">
					<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
						<img alt="Unity Web Player. Install now!" src="http://webplayer.unity3d.com/installation/getunity.png" width="193" height="63" />
					</a>
				</div>
			</div>
		</div>
		
  </div>
  <input type="hidden" name="session_id" id="session_id" value="" />

</div>



