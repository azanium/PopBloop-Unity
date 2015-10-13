<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/UnityObject.js"></script>
<script type="text/javascript">
<!--
function GetUnity() {
	if (typeof unityObject != "undefined") {
		return unityObject.getObjectById("unityPlayer");
	}
	return null;
}
if (typeof unityObject != "undefined") {
	unityObject.embedUnity("unityPlayer", "<?php print($this->basepath); ?>bundles/webplayer/AvatarEditor.unity3d?<?php echo time(); ?>", 400, 480);
	
}
function OnLiloLoaded() {
	$("#config_form").show();
//	GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", "wwwwww");
}
-->
</script>


<script language="javascript">
	$(document).ready(function(){

		// dapetin session:
//		var session_id = '';
//		$.get("index.php?op=login&func=get_session", {}, function(data){
//			$("#session_id").val(data);
//		});
		
		$(".female").show();
		$(".male").hide();
		
		$("#radio_female").click(function(){
			$(".female").show();
			$(".male").hide();
			$("#select_gender").val("female");

			var message = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_face-1','material':'female_face-1'},{'tipe':'Hair','element':'female_hair-2','material':'female_hair-2_pink'},{'tipe':'Body','element':'female_top-2','material':'female_top-2_green'},{'tipe':'Pants','element':'female_pants-1','material':'female_pants-1_green'},{'tipe':'Shoes','element':'female_shoes-2','material':'female_shoes-2_red'}]";
//			alert(message);
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
		});
		
		$("#radio_male").click(function(){
			$(".male").show();
			$(".female").hide();
			$("#select_gender").val("male");

			var message = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_face-1','material':'male_face-1'},{'tipe':'Hair','element':'male_hair-2','material':'male_hair-2_blond'},{'tipe':'Body','element':'male_top-2','material':'male_top-2_green'},{'tipe':'Pants','element':'male_pants-1','material':'male_pants-1_green'},{'tipe':'Shoes','element':'male_shoes-2','material':'male_shoes-2_red'}]";
//			alert(message);
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
		});


		$("#previewBtn").click(function(){
			var gender = $("#select_gender").val();
			var tipe = $("#select_tipe").val();
			var element = $("#select_element_" + gender).val();
			var material = $("#select_material_" + gender).val();
			
			var session_id = $("#session_id").val();
			var message = "{'gender':'"+gender+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"','session_id':'"+session_id+"'}";
//			alert(message);
			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);

		});
		
		$("#saveConfBtn").click(function(){
			// update the database
			var message = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_face-1','material':'male_face-1'},{'tipe':'Hair','element':'male_hair-2','material':'male_hair-2_blond'},{'tipe':'Body','element':'male_top-2','material':'male_top-2_green'},{'tipe':'Pants','element':'male_pants-1','material':'male_pants-1_green'},{'tipe':'Shoes','element':'male_shoes-2','material':'male_shoes-2_red'}]";
			$.post("<?php //print($this->basepath); ?>avatar/user/set_configuration", {'avatar_conf':message}, function(data){
				alert(data);
			});
		});
		

	});

</script>

<div class="centered shadow transbg" style="width:960px; height:540px; border:#00ccff medium solid; border-radius: 8px;">

	<div style="float:left; width:960px; height:28px;">

  </div>

	<div style="float:left; width:120px; text-align:center;" id="div_webplayer" class="centered">
  	<img src="<?php print($this->basepath); ?>modules/000_user_interface/images/avatar.editor.001.ccw.png" />
  </div>
	<div style="float:left; width:440px; text-align:center;" id="div_webplayer" class="centered">
		<div class="content" class="centered shadow">
			<div id="unityPlayer" class="centered shadow">
				<div class="missing">
					<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
						<!--img alt="Unity Web Player. Install now!" src="http://webplayer.unity3d.com/installation/getunity.png" width="193" height="63" /-->
						<img alt="Unity Web Player. Install now!" src="<?php print($this->basepath); ?>bundles/webplayer/images/getunity.png" width="380px" height="456px" />
					</a>
				</div>
			</div>
		</div>
  </div>
	<div style="float:left; width:400px; text-align:center" id="div_asset_chooser">
		<input type="hidden" name="session_id" id="session_id" value="<?php echo $_SESSION['session_id']; ?>" />
  	<div id="config_form" style="display:none;">
      <table style="width:100%;" class="input_form">
        <tr>
          <th>Gender</th>
          <td>
          	<input type="radio" name="gender" value="female" id="radio_female" />&nbsp;<label for="radio_female">Female</label>
          	<input type="radio" name="gender" value="male" id="radio_male" checked="checked" />&nbsp;<label for="radio_male">Male</label>
            <input type="hidden" name="select_gender" id="select_gender" value="male" />
          </td>
        </tr>

				<tr>
        	<th>Tipe</th>
        	<td>
            <select name="tipe" id="select_tipe" class="bluetext">
              <option value="face">Face</option>
              <option value="eyes">Eyes</option>
              <option value="hair">Hair</option>

              <option value="pants">Pants</option>
              <option value="shoes">Shoes</option>
              <option value="body">Top Body</option>

            </select>
          </td>
        </tr>

        <tr class="female">
          <th>Element</th>
          <td>
            <select name="element_female" id="select_element_female" class="bluetext">
              <option value="female_eyes">female_eyes</option>
              <option value="female_face-1">female_face-1</option>
              <option value="female_face-2">female_face-2</option>
              <option value="female_hair-1">female_hair-1</option>
              <option value="female_hair-2">female_hair-2</option>

              <option value="female_pants-1">female_pants-1</option>
              <option value="female_pants-2">female_pants-2</option>
              <option value="female_shoes-1">female_shoes-1</option>
              <option value="female_shoes-2">female_shoes-2</option>
              <option value="female_top-1">female_top-1</option>
              <option value="female_top-2">female_top-2</option>
            </select>
          </td>
        </tr>

        <tr class="male">
          <th>Element</th>
          <td>
            <select name="element_male" id="select_element_male" class="bluetext">
              <option value="male_eyes">male_eyes</option>
              <option value="male_face-1">male_face-1</option>
              <option value="male_face-2">male_face-2</option>
              <option value="male_hair-1">male_hair-1</option>
              <option value="male_hair-2">male_hair-2</option>


              <option value="male_pants-1">male_pants-1</option>
              <option value="male_pants-2">male_pants-2</option>
              <option value="male_shoes-1">male_shoes-1</option>
              <option value="male_shoes-2">male_shoes-2</option>
              <option value="male_top-1">male_top-1</option>
              <option value="male_top-2">male_top-2</option>

            </select>
          </td>
        </tr>
  
        <tr class="female">
          <th>Material</th>
          <td>
            <select name="material_female" id="select_material_female" class="bluetext">

              <option value="female_eyes_blue">female_eyes_blue</option>
              <option value="female_eyes_brown">female_eyes_brown</option>
              <option value="female_eyes_green">female_eyes_green</option>
              <option value="female_face-1">female_face-1</option>
              <option value="female_face-2">female_face-2</option>
              <option value="female_hair-1_brown">female_hair-1_brown</option>
              <option value="female_hair-1_red">female_hair-1_red</option>
              <option value="female_hair-1_yellow">female_hair-1_yellow</option>
              <option value="female_hair-2_cyan">female_hair-2_cyan</option>
              <option value="female_hair-2_dark">female_hair-2_dark</option>
              <option value="female_hair-2_pink">female_hair-2_pink</option>

              <option value="female_pants-1_blue">female_pants-1_blue</option>
              <option value="female_pants-1_dark">female_pants-1_dark</option>
              <option value="female_pants-1_green">female_pants-1_green</option>
              <option value="female_pants-2_black">female_pants-2_black</option>
              <option value="female_pants-2_blue">female_pants-2_blue</option>
              <option value="female_pants-2_orange">female_pants-2_orange</option>
              <option value="female_shoes-1_blue">female_shoes-1_blue</option>
              <option value="female_shoes-1_green">female_shoes-1_green</option>
              <option value="female_shoes-1_yellow">female_shoes-1_yellow</option>
              <option value="female_shoes-2_blue">female_shoes-2_blue</option>
              <option value="female_shoes-2_red">female_shoes-2_red</option>
              <option value="female_shoes-2_yellow">female_shoes-2_yellow</option>
              <option value="female_top-1_blue">female_top-1_blue</option>
              <option value="female_top-1_green">female_top-1_green</option>
              <option value="female_top-1_pink">female_top-1_pink</option>
              <option value="female_top-2_green">female_top-2_green</option>
              <option value="female_top-2_orange">female_top-2_orange</option>
              <option value="female_top-2_purple">female_top-2_purple</option>

            </select>
          </td>
        </tr>

        <tr class="male">
          <th>Material</th>
          <td>
            <select name="material_male" id="select_material_male" class="bluetext">

              <option value="male_eyes_blue">male_eyes_blue</option>

              <option value="male_eyes_blue">male_eyes_blue</option>
              <option value="male_eyes_brown">male_eyes_brown</option>
              <option value="male_eyes_green">male_eyes_green</option>
              <option value="male_face-1">male_face-1</option>
              <option value="male_face-2">male_face-2</option>
              <option value="male_hair-1_blond">male_hair-1_blond</option>
              <option value="male_hair-1_brown">male_hair-1_brown</option>
              <option value="male_hair-1_orange">male_hair-1_orange</option>
              <option value="male_hair-2_blond">male_hair-2_blond</option>
              <option value="male_hair-2_brown">male_hair-2_brown</option>
              <option value="male_hair-2_red">male_hair-2_red</option>

              <option value="male_pants-1_blue">male_pants-1_blue</option>
              <option value="male_pants-1_dark">male_pants-1_dark</option>
              <option value="male_pants-1_green">male_pants-1_green</option>
              <option value="male_pants-2_blue">male_pants-2_blue</option>
              <option value="male_pants-2_lillac">male_pants-2_lillac</option>
              <option value="male_pants-2_purple">male_pants-2_purple</option>
              <option value="male_shoes-1_black">male_shoes-1_black</option>
              <option value="male_shoes-1_green">male_shoes-1_green</option>
              <option value="male_shoes-1_red">male_shoes-1_red</option>
              <option value="male_shoes-2_brown">male_shoes-2_brown</option>
              <option value="male_shoes-2_dark">male_shoes-2_dark</option>
              <option value="male_shoes-2_red">male_shoes-2_red</option>
              <option value="male_top-1_blue">male_top-1_blue</option>
              <option value="male_top-1_pink">male_top-1_pink</option>
              <option value="male_top-1_yellow">male_top-1_yellow</option>
              <option value="male_top-2_gray">male_top-2_gray</option>
              <option value="male_top-2_green">male_top-2_green</option>
              <option value="male_top-2_orange">male_top-2_orange</option>

            </select>
          </td>
        </tr>
  
        <tr>
          <td colspan="2" style="text-align:center">
						<input type="button" id="previewBtn" value="Preview Avatar" class="bluetext" />
            &nbsp;
            <input type="button" id="saveConfBtn" value="Save Avatar" class="bluetext" />
          </td>
        </tr>
  
      </table>
    </div>
  </div>


</div>



