<div class="withjs">
<!--[*ui_user_avatar_editor_categorized*]-->
<style type="text/css">
	#dialog_link, #radio_male, #radio_female, #radio_small, #radio_medium, #radio_big {padding: .4em 1em .4em 20px;text-decoration: none;position: relative; font-size:0.7em; color:#FFF;}
	#dialog_link span.ui-icon,  #radio_male span.ui-icon, #radio_female span.ui-icon, #radio_small span.ui-icon, #radio_medium span.ui-icon, #radio_big span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
</style>
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

	var params = {
		disableContextMenu: true,
		backgroundcolor: "1F1F1F",/*2c2c2c*/
		bordercolor: "1F1F1F",
		textcolor: "000000",
		logoimage: "<?php print($this->basepath); ?>bundles/webplayer/images/lilologo.small.png",
	};

	unityObject.embedUnity("unityPlayer", "<?php print($this->basepath); ?>bundles/webplayer/AvatarEditor.unity3d?<?php echo time(); ?>", 380, 456, params);
	
}

function sleep(milliseconds) {
	var start = new Date().getTime();
	for (var i = 0; i < 1e7; i++) {
		if ((new Date().getTime() - start) > milliseconds){
			alert('Loading...');
			break;
		}
	}
}



function OnLiloLoaded() {
	GetUnity().SendMessage("_DressRoom", "ChangePlayerId", "<?php echo $_SESSION['user_id']; ?>");
	
	$("#config_form").show();

	$.post("<?php echo $this->basepath; ?>avatar/user/get_configuration", {}, function(data){
		GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", data);
		
		var config_obj = eval('(' + data + ')');
		
		// hat di-reset dulu
		$("#hat_element").val('');
		$("#hat_material").val('');
		
		for(i = 0; i < config_obj.length; i++){
			var tipe = config_obj[i].tipe.toLowerCase();
			if(tipe == 'gender'){//sleep(2000);
				var gender = config_obj[i].element;
				var gender_ = gender.replace("_base", "");
				$("#select_gender").val(gender_);
				
				$('#current_gender').html(gender_);
			} else if(tipe == 'face'){//sleep(2000);
				// 'tipe':'Face','element':'male_face-1','material':'male_face-1','eye_brows':'brows','eyes':'eyes-2','lip':'lip'
				var element 	= config_obj[i].element;
				var material 	= config_obj[i].material;
				var gender_ = $("#select_gender").val();
				var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
				GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);

				$("#" + tipe + "_element").val(element);
				$("#" + tipe + "_material").val(material);

				// yg diperlukan Hendra:
				// {'tipe':'face','element':'male_face-1','eye_brows':'brows','eyes':'eyes','lip':'lip'},
				var eye_brows = config_obj[i].eye_brows;
				var eyes 			= config_obj[i].eyes;
				var lip 			= config_obj[i].lip;
				// ChangeFacePartEvent("{'tipe':'eyes','element':'eyes'}");
				message = "{'tipe':'eye_brows','element':'"+eye_brows+"'}";
				GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
				message = "{'tipe':'eyes','element':'"+eyes+"'}";
				GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
				message = "{'tipe':'lip','element':'"+lip+"'}";
				GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);

				$("#eye_brows").val(eye_brows);
				$("#eyes").val(eyes);
				$("#lip").val(lip);
			} else if(tipe == 'skin'){//alert('Downloading ' + tipe + '...');//sleep(2000);
				var color = config_obj[i].color;
				color = parseInt(color);
				$("#skin").val(color);
				GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", color);
			} else {
				var element = config_obj[i].element;
				var material = config_obj[i].material;
				$("#" + tipe + "_element").val(element);
				$("#" + tipe + "_material").val(material);
				
				var gender_ = $("#select_gender").val();
				var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
				GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
				
			}
		}
	});

}


function get_session_id(){
	var session_id = "<?php echo $this->session_id; ?>";

	GetUnity().SendMessage("_DressRoom", "GetUserId", session_id);
}

function get_session(){
  return "<?php echo $this->session_id; ?>";
}

-->
</script>


<script language="javascript">
	$(document).ready(function(){

		$('#unityPlayer').click(function(){
			$('#unityPlayer').css('height', '456px');
		});

		$('#dialog_link, ul#icons li').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);

		$('.radio_gender, ul#icons li').hover(
			function() { $(this).addClass('ui-state-hover'); }, 
			function() { $(this).removeClass('ui-state-hover'); }
		);

		$('#tabs').tabs();
		$('#face_part_tabs').tabs();

		$.post("<?php echo $this->basepath; ?>avatar/user/get_gender", {}, function(data){// alert('avatar/user/get_gender: ' + data);
			$('#select_gender').val(data);
			if(data == 'male'){
				$(".male").show();
				$(".female").hide();
			} else {
				$(".female").show();
				$(".male").hide();
			}
		});

		$.post("<?php echo $this->basepath; ?>avatar/user/get_size", {}, function(data){
			var gender = $('#select_gender').val();
			var gender_op = (gender == 'male') ? 'female' : 'male';
			$('#select_size').val(data);
			if(data == 'small' || data == 'thin'){
				$(".big, .fat").hide();
				$(".medium").hide();
				$(".small, .thin").show();
			} else if(data == 'medium'){
				$(".big, .fat").hide();
				$(".small, .thin").hide();
				$(".medium").show();
			} else if(data == 'big' || data == 'fat'){
				$(".small, .thin").hide();
				$(".medium").hide();
				$(".big, .fat").show();
			}
			$("." + gender_op).hide();
		});

		function avatar_config_update_form(avatar_config){	// ga ngeset gender dan size, krn udah di set di fungsi yg akan panggil fungsi ini
//			contoh message = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02','element2':'male_hair_02_bottom','material2':'male_hair_02'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_thin','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";

			$("#hat_element").val('');
			$("#hat_material").val('');

			var message_eval = eval('(' + avatar_config + ')');
			for(i = 0; i < message_eval.length; i++){
				var tipe = message_eval[i].tipe.toLowerCase();

				switch(tipe){
					case 'face':
						$("#face_element").val(message_eval[i].element);
						$("#face_material").val(message_eval[i].material);
						$("#eye_brows").val(message_eval[i].eye_brows);
						$("#eyes").val(message_eval[i].eyes);
						$("#lip").val(message_eval[i].lip);
					break;
					
					case 'hair':
					case 'hat':
					case 'body':
					case 'pants':
					case 'shoes':
						$("#" + tipe + "_element").val(message_eval[i].element);
						$("#" + tipe + "_material").val(message_eval[i].material);//alert(tipe + " - " + message_eval[i].element);
					break;
					
					case 'skin':
						$("#skin").val(message_eval[i].color);
					break;
				}

			}
		}

		$('#radio_small').click(function(){
			var gender = $("#select_gender").val();
			
			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('small');
			
			$('.' + gender).show();
			$('.medium').hide();
			$('.big, .fat').hide();
			$('.small, .thin').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('small')

			//// versi reset dari awal, tanpa baca settingan sebelumnya
			//var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'male_head_broweyes_01','eyes':'male_head_eyes_01','lip':'male_head_lips_01'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_pants_thin','material':'male_pants_2'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_3'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			//var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'female_head_broweyes_01','eyes':'female_head_eyes_01','lip':'female_head_lips_01'},{'tipe':'Hair','element':'female_hair2','material':'female_hair_02_2'},{'tipe':'Body','element':'female_t-shirt_thin','material':'female_t-shirt_03'},{'tipe':'Pants','element':'female_pants_thin','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_4'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			
			// versi baca dari setting sebelumnya
			var body_element = $('#body_element').val();
			body_element = body_element.replace('_fat', '_thin');
			body_element = body_element.replace('_medium', '_thin');
			
			var pants_element = $('#pants_element').val();
			pants_element = pants_element.replace('_fat', '_thin');
			pants_element = pants_element.replace('_medium', '_thin');
			
			// hat, cek dulu apakah ada atau tidak
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();
			
			var message_male = '';
			var message_female = '';
			
			if($.trim(hat_element) == ''){
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			} else {
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			}
			
			
			

			var message = (gender == 'male') ? message_male : message_female;

			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
			
			avatar_config_update_form(message);
			$('#current_size').html('thin');
		});
		
		$('#radio_medium').click(function(){
			var gender = $("#select_gender").val();
			
			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('medium');
			
			$('.' + gender).show();
			$('.small, .thin').hide();
			$('.big, .fat').hide();
			$('.medium').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('medium')

			//// versi reset dari awal
			//var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'male_head_broweyes_01','eyes':'male_head_eyes_01','lip':'male_head_lips_01'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02'},{'tipe':'Body','element':'male_body_medium','material':'male_body'},{'tipe':'Pants','element':'male_pants_medium','material':'male_pants_2'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_3'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			//var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'female_head_broweyes_01','eyes':'female_head_eyes_01','lip':'female_head_lips_01'},{'tipe':'Hair','element':'female_hair2','material':'female_hair_02_2'},{'tipe':'Body','element':'female_t-shirt_medium','material':'female_t-shirt_03'},{'tipe':'Pants','element':'female_pants_medium','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_4'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";


			// versi baca dari setting sebelumnya
			var body_element = $('#body_element').val();
			body_element = body_element.replace('_fat', '_medium');
			body_element = body_element.replace('_thin', '_medium');
			
			var pants_element = $('#pants_element').val();
			pants_element = pants_element.replace('_fat', '_medium');
			pants_element = pants_element.replace('_thin', '_medium');
			
			// hat, cek dulu apakah ada atau tidak
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();
			
			var message_male = '';
			var message_female = '';
			
			if($.trim(hat_element) == ''){
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			} else {
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			}

			var message = (gender == 'male') ? message_male : message_female;
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);

			avatar_config_update_form(message);
			$('#current_size').html('medium');
		});
		
		$('#radio_big').click(function(){
			var gender = $("#select_gender").val();

			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('big');
			
			$('.' + gender).show();
			$('.medium').hide();
			$('.small, .thin').hide();
			$('.big, .fat').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('big')
			
			//// versi reset dari awal
			//var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'male_head_broweyes_01','eyes':'male_head_eyes_01','lip':'male_head_lips_01'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02'},{'tipe':'Body','element':'male_body_fat','material':'male_body'},{'tipe':'Pants','element':'male_pants_fat','material':'male_pants_2'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_3'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			//var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'female_head_broweyes_01','eyes':'female_head_eyes_01','lip':'female_head_lips_01'},{'tipe':'Hair','element':'female_hair2','material':'female_hair_02_2'},{'tipe':'Body','element':'female_t-shirt_fat','material':'female_t-shirt_03'},{'tipe':'Pants','element':'female_pants_fat','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_4'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";


			// versi baca dari setting sebelumnya
			var body_element = $('#body_element').val();
			body_element = body_element.replace('_thin', '_fat');
			body_element = body_element.replace('_medium', '_fat');
			
			var pants_element = $('#pants_element').val();
			pants_element = pants_element.replace('_thin', '_fat');
			pants_element = pants_element.replace('_medium', '_fat');
			

			// hat, cek dulu apakah ada atau tidak
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();
			
			var message_male = '';
			var message_female = '';
			
			if($.trim(hat_element) == ''){
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			} else {
				message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
				message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'"+$('#face_element').val()+"','material':'"+$('#face_material').val()+"','eye_brows':'"+$('#eye_brows').val()+"','eyes':'"+$('#eyes').val()+"','lip':'"+$('#lip').val()+"'},{'tipe':'Hair','element':'"+$('#hair_element').val()+"','material':'"+$('#hair_material').val()+"'},{'tipe':'Hat','element':'"+$('#hat_element').val()+"','material':'"+$('#hat_material').val()+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+$('#body_material').val()+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+$('#pants_material').val()+"'},{'tipe':'Shoes','element':'"+$('#shoes_element').val()+"','material':'"+$('#shoes_material').val()+"'},{'tipe':'Hand','element':'"+$('#hand_element').val()+"','material':'"+$('#hand_material').val()+"'},{'tipe':'Skin','color':'"+$('#skin').val()+"'}]";
			}




			var message = (gender == 'male') ? message_male : message_female;
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);

			avatar_config_update_form(message);
			$('#current_size').html('fat');
		});
		
		$("#radio_female").click(function(){	// tambahkan selector size disini!
			
			var size = $("#select_size").val();
			
			var size_op = (size == 'big' || size == 'fat') ? ".medium, .small, .thin" : ((size == 'medium') ? ".big, .fat, .small, .thin" : ".big, .fat, .medium" ) ;
			//alert(size_op);
			$('.' + size).show();
			$(".female").show();
			$(size_op).hide();
			$(".male").hide();
			
			
			$("#select_gender").val("female");
			
			// hard-coded
			//var message_fat = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'female_head_broweyes_01','eyes':'female_head_eyes_01','lip':'female_head_lips_01'},{'tipe':'Hair','element':'female_hair2','material':'female_hair_02_2'},{'tipe':'Body','element':'female_t-shirt_fat','material':'female_t-shirt_03'},{'tipe':'Pants','element':'female_pants_fat','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_4'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			//var message_medium = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'female_head_broweyes_01','eyes':'female_head_eyes_01','lip':'female_head_lips_01'},{'tipe':'Hair','element':'female_hair2','material':'female_hair_02_2'},{'tipe':'Body','element':'female_t-shirt_medium','material':'female_t-shirt_03'},{'tipe':'Pants','element':'female_pants_medium','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_4'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			//var message_thin = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'female_head_broweyes_01','eyes':'female_head_eyes_01','lip':'female_head_lips_01'},{'tipe':'Hair','element':'female_hair2','material':'female_hair_02_2'},{'tipe':'Body','element':'female_t-shirt_thin','material':'female_t-shirt_03'},{'tipe':'Pants','element':'female_pants_thin','material':'female_pants_01'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01_4'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			
			//	'avatar_config_' . $gender . '_' . $size
			var message_fat = "<?php echo $this->avatar_config_female_big; ?>";
			var message_medium = "<?php echo $this->avatar_config_female_medium; ?>";
			var message_thin = "<?php echo $this->avatar_config_female_small; ?>";

			// revisi: ambil dari 

			var message = message_fat;
			var size_ = 'fat';
			if(size == 'medium'){
				message = message_medium;
				size_ = 'medium';
			} else if(size == 'small'){
				message = message_thin;
				size_ = 'thin';
			}
			
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
			
			updateAvatarConfInput(message);
			
			//$("#select_gender").val("female");
			//$("#face_element").val("female_head");
			//$("#face_material").val("");
			//
			//$("#hand_element").val("female_body_hand");
			//$("#hand_material").val("female_body");
			//
			//$("#hair_element").val("female_hair2");
			//$("#hair_material").val("female_hair_02_2");
			//
			//$("#hat_element").val("");
			//$("#hat_material").val("");
			//
			//$("#body_element").val("female_t-shirt_" + size_);
			//$("#body_material").val("female_t-shirt_03");
			//$("#pants_element").val("female_pants_" + size_);
			//$("#pants_material").val("female_pants_01");
			//$("#shoes_element").val("female_shoes_01");
			//$("#shoes_material").val("female_shoes_01_4");
			//
			//$("#eye_brows").val("female_head_broweyes_01");
			//$("#eyes").val("female_head_eyes_01");
			//$("#lip").val("female_head_lips_01");
			//
			//$("#skin").val("1");
			//
			//$('#current_gender').html('female');
		});
		
		$("#radio_male").click(function(){
			var size = $("#select_size").val();
			
			var size_op = (size == 'big' || size == 'fat') ? ".medium, .small, .thin" : ((size == 'medium') ? ".big, .fat, .small, .thin" : ".big, .fat, .medium" ) ;
			
			//alert(size_op);
			$('.' + size).show();
			$(".male").show();
			$(size_op).hide();
			$(".female").hide();
			$("#select_gender").val("male");

			// message harusnya diperoleh dari last configuration utk gender terpilih

			// {'tipe':'Hat','element':'','material':''}, kalo element == '', ga perlu dimasukkan ke message
			//var message_fat = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'male_head_broweyes_01','eyes':'male_head_eyes_01','lip':'male_head_lips_01'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02_1'},{'tipe':'Body','element':'male_t-shirt_fat','material':'male_t-shirt_1'},{'tipe':'Pants','element':'male_pants_fat','material':'male_pants_2'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_1'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			//var message_medium = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'male_head_broweyes_01','eyes':'male_head_eyes_01','lip':'male_head_lips_01'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02_1'},{'tipe':'Body','element':'male_t-shirt_medium','material':'male_t-shirt_1'},{'tipe':'Pants','element':'male_pants_medium','material':'male_pants_2'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_1'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			//var message_thin = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'male_head_broweyes_01','eyes':'male_head_eyes_01','lip':'male_head_lips_01'},{'tipe':'Hair','element':'male_hair_02','material':'male_hair_02_1'},{'tipe':'Body','element':'male_t-shirt_thin','material':'male_t-shirt_1'},{'tipe':'Pants','element':'male_pants_thin','material':'male_pants_2'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01_1'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";

			//	'avatar_config_' . $gender . '_' . $size
			var message_fat = "<?php echo $this->avatar_config_male_big; ?>";
			var message_medium = "<?php echo $this->avatar_config_male_medium; ?>";
			var message_thin = "<?php echo $this->avatar_config_male_small; ?>";

			
			var message = message_fat;
			var size_ = 'fat';
			if(size == 'medium'){
				message = message_medium;
				size_ = 'medium';
			} else if(size == 'small'){
				message = message_thin;
				size_ = 'thin';
			}
			
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
			
			updateAvatarConfInput(message);
			
			//$("#select_gender").val("male");
			//$("#face_element").val("male_head");
			//$("#face_material").val("");
			//
			//$("#hand_element").val("male_body_hand");
			//$("#hand_material").val("male_body");
			//
			//$("#hair_element").val("male_hair_02");
			//$("#hair_material").val("male_hair_02_1");
			//
			//$("#hat_element").val("");
			//$("#hat_material").val("");
			//
			//$("#body_element").val("male_t-shirt_" + size_);
			//$("#body_material").val("male_t-shirt_1");
			//$("#pants_element").val("male_pants_" + size_);
			//$("#pants_material").val("male_pants_2");
			//$("#shoes_element").val("male_shoes_01");
			//$("#shoes_material").val("male_shoes_01_1");
			//
			//$("#eye_brows").val("male_head_broweyes_01");
			//$("#eyes").val("male_head_eyes_01");
			//$("#lip").val("male_head_lips_01");
			//
			//$("#skin").val("1");
			//
			//$('#current_gender').html('male');
		});

		$('.facePartChange, .avatarChange, .facePartCategoryChange, .avatarCategoryChange').hover(
			function(){
				$(this).addClass('shadow');
				$(this).addClass('light_blue_bg');
			}, 
			function(){
				$(this).removeClass("shadow");
				$(this).removeClass('light_blue_bg');
			}
		);

		$(".facePartChange").live('click', function(){
			$('#saved_notification').hide();
			// ChangeFacePartEvent("{'tipe':'eyes','element':'eyes'}");
			
			// ubah value di hidden input: 
			//					eye_brows
			//					eyes
			//					lip
			var _id = $(this).attr('id');	// tipe__element? no, yg bener: tipe____material
			var _id_split = _id.split('____');
			
			// face_part_eyes -> eyes
			_id_split[0] = _id_split[0].replace('face_part_', '');

			var message = "{'tipe':'"+_id_split[0]+"','element':'"+_id_split[1]+"'}";
			
			GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
			
			if(_id_split[0] == 'eyeBrows'){
				_id_split[0] = 'eye_brows';
			}
			
			$("#" + _id_split[0]).val(_id_split[1]);
			
		});

		$(".avatarChange").live('click', function(){
			$('#saved_notification').hide();
			var _id = $(this).attr('id');	// format id: eyes-male_eyes-male_eyes_blue, tipe-element-material
																		// eyes__male_eyes__male_eyes_green
			var _id_split = _id.split('__');
			var gender = $("#select_gender").val();
			
				var message = "{'gender':'"+gender+"','tipe':'"+_id_split[0]+"','element':'"+_id_split[1]+"','material':'"+_id_split[2]+"'}";
			
			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
			
			$("#" + _id_split[0] + "_element").val(_id_split[1]);
			$("#" + _id_split[0] + "_material").val(_id_split[2]);
			
		});
		
		$('.facePartCategoryChange').live('click', function(){
			$('.loading_div').show();
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			var tipe = _id_split[0];
			var gender = _id_split[1];
			var size = _id_split[2];
			
			var category = $(this).attr('title');
			
			var background_image = $(this).css('background-image');
			
			$.post("<?php echo $this->basepath; ?>avatar/user/avatar_category", {tipe: tipe, gender: gender, category: category, size: size, background_image: background_image}, function(data){
				$('#' + tipe + '_categories').hide();
				$('#' + tipe + '_items').html(data);
				$('#' + tipe + '_items').show();
			});

			$('.loading_div').hide();
			
		});
		
		$('.avatarCategoryChange').live('click', function(){
			$('.loading_div').show();
			var _id = $(this).attr('id');
			var _id_split = _id.split('__');
			var tipe = _id_split[0];
			var gender = _id_split[1];
			var size = _id_split[2];
			
			var category = $(this).attr('title');

			
			$.post("<?php echo $this->basepath; ?>avatar/user/avatar_category", {tipe: tipe, gender: gender, category: category, size: size/*, background_image: background_image*/}, function(data){
				$('#' + tipe + '_categories').hide();
				$('#' + tipe + '_items').html(data);
				$('#' + tipe + '_items').show();
			});

			$('.loading_div').hide();

		});
		

		$("#updateBtn").click(function(){
			var gender = $("#select_gender").val();
			var tipe = $("#select_tipe").val();
			var element = $("#select_element_" + gender).val();
			var material = $("#select_material_" + gender).val();
			
			var message = "{'gender':'"+gender+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
			
			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);

		});
		
		$("#dialog_link").click(function(){	// belum selesai......
			var gender 					= $("#select_gender").val();
			var size 						= $("#select_size").val();
			var tipe 						= $("#select_tipe").val();
			var element 				= $("#select_element_" + gender).val();
			var material 				= $("#select_material_" + gender).val();
			
			var skin						= $("#skin").val();
			
			var hat_element			= $("#hat_element").val();
			var hat_material		= $("#hat_material").val();

			var face_element		= $("#face_element").val();
			var face_material		= $("#face_material").val();
			var hair_element		= $("#hair_element").val();
			var hair_material		= $("#hair_material").val();
			//var hair_element2		= $("#hair_element2").val();
			//var hair_material2	= $("#hair_material2").val();
			var body_element		= $("#body_element").val();
			var body_material		= $("#body_material").val();
			var hand_element		= $("#hand_element").val();
			var hand_material		= $("#hand_material").val();
			var pants_element		= $("#pants_element").val();
			var pants_material	= $("#pants_material").val();
			var shoes_element		= $("#shoes_element").val();
			var shoes_material	= $("#shoes_material").val();
			var eye_brows				= $("#eye_brows").val();
			var eyes						= $("#eyes").val();
			var lip							= $("#lip").val();

			
			var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Hat','element':'"+hat_element+"','material':'"+hat_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";

			if($.trim(hat_element) == ''){
				var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";
			}

			// update the database
			$.post("<?php echo $_SESSION['basepath']; ?>avatar/user/set_configuration", {avatar_conf: message, size: size}, function(data){
				if(data == "1"){
					$('#saved_notification').show();
					var t = setTimeout("$('#saved_notification').hide()", 3000);
				}
			});
		});

		$('.skinChange').live('click', function(){
			var _id = $(this).attr('id');
			var _id_split = _id.split('_');
			skin_idx = parseInt(_id_split[1]);
			GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", skin_idx);
			$('#skin').val(skin_idx);
		});
		
		
		function updateAvatarConfInput(data){
			var config_obj = eval('(' + data + ')');
			
			// hat di-reset dulu
			$("#hat_element").val('');
			$("#hat_material").val('');
			
			for(i = 0; i < config_obj.length; i++){
				var tipe = config_obj[i].tipe.toLowerCase();
				if(tipe == 'gender'){//sleep(2000);
					var gender = config_obj[i].element;
					var gender_ = gender.replace("_base", "");
					$("#select_gender").val(gender_);
					
					$('#current_gender').html(gender_);
				} else if(tipe == 'face'){//sleep(2000);
					// 'tipe':'Face','element':'male_face-1','material':'male_face-1','eye_brows':'brows','eyes':'eyes-2','lip':'lip'
					var element 	= config_obj[i].element;
					var material 	= config_obj[i].material;
					var gender_ = $("#select_gender").val();
//					var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
	
					$("#" + tipe + "_element").val(element);
					$("#" + tipe + "_material").val(material);
	
					// yg diperlukan Hendra:
					// {'tipe':'face','element':'male_face-1','eye_brows':'brows','eyes':'eyes','lip':'lip'},
					var eye_brows = config_obj[i].eye_brows;
					var eyes 			= config_obj[i].eyes;
					var lip 			= config_obj[i].lip;
					// ChangeFacePartEvent("{'tipe':'eyes','element':'eyes'}");
//					message = "{'tipe':'eye_brows','element':'"+eye_brows+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
//					message = "{'tipe':'eyes','element':'"+eyes+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
//					message = "{'tipe':'lip','element':'"+lip+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeFacePartEvent", message);
	
					$("#eye_brows").val(eye_brows);
					$("#eyes").val(eyes);
					$("#lip").val(lip);
				} else if(tipe == 'skin'){//alert('Downloading ' + tipe + '...');//sleep(2000);
					var color = config_obj[i].color;
					color = parseInt(color);
					$("#skin").val(color);
//					GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", color);
				} else {
					var element = config_obj[i].element;
					var material = config_obj[i].material;
					$("#" + tipe + "_element").val(element);
					$("#" + tipe + "_material").val(material);
//					var gender_ = $("#select_gender").val();
//					var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
//					GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
					
				}
			}

		}
		

	});

</script>


<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_8">
    <div style="height: 30px; border-bottom: solid 2px #333;">
      <div style="float: right; width: 90px; padding: 8px; color: #fff; font-size: 12px; background-color: #333; text-align: center; position: relative; bottom: 0; border: 0; margin-left: 2px;"><a href="<?php echo $this->basepath; ?>profile/<?php echo $this->user_property->username; ?>" style="text-decoration: none; color: #FFF;">Profile</a></div>
      <div style="float: right; width: 90px; padding: 8px; color: #fff; font-size: 12px; background-color: #333; text-align: center; position: relative; bottom: 0; border: 0;"><a href="<?php echo $this->basepath; ?>people" style="text-decoration: none; color: #FFF;">People</a></div>
      <div style="float: right; font-size: 28px; color: #fff; width: 404px;">Avatar Editor</div>
    </div>
	</div>
  <div class="grid_4" style="text-align: center; display: table; height: 30px;">
				<span style="display: table-cell; vertical-align: middle;">Avatar Customize</span>
	</div>
  <div class="clear"></div>
</div>

<div style="height: 20px"></div>


<div class="container_12">
	<div class="grid_5" id="div_webplayer">
		<div class="content" class="centered">
			<div id="unityPlayer">
				<div class="missing">
					<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!">
						<img alt="Unity Web Player. Install now!" src="<?php print($this->basepath); ?>bundles/webplayer/images/getunity.png" width="380" height="456" />
					</a>
				</div>
			</div>
		</div>
  </div>
  
	<div class="grid_7" id="div_asset_chooser">
  	<div id="config_form" style="display:none;">
      <div id="save_conf" style="float:left; height:28px; text-align:right;">
      	<div style="float:left; text-align:left">
        	<a href="#" id="radio_female" class="ui-state-default ui-corner-all radio_gender">
          	<span class="ui-icon ui-icon-bullet"></span>Female
          </a>
        	<a href="#" id="radio_male" class="ui-state-default ui-corner-all radio_gender">
          	<span class="ui-icon ui-icon-bullet"></span>Male
          </a>
          &nbsp;&nbsp;
        	<a href="#" id="radio_small" class="ui-state-default ui-corner-all radio_gender">
          	<span class="ui-icon ui-icon-bullet"></span>Thin
          </a>
        	<a href="#" id="radio_medium" class="ui-state-default ui-corner-all radio_gender">
          	<span class="ui-icon ui-icon-bullet"></span>Medium
          </a>
        	<a href="#" id="radio_big" class="ui-state-default ui-corner-all radio_gender">
          	<span class="ui-icon ui-icon-bullet"></span>Fat
          </a>
          
          <input type="hidden" name="skin" id="skin" value="" />
					
          <input type="hidden" name="select_gender" id="select_gender" value="" />
          <input type="hidden" name="select_size" id="select_size" value="" />
          <input type="hidden" name="face_element" id="face_element" value="" />
          <input type="hidden" name="face_material" id="face_material" value="" />

          <input type="hidden" name="hat_element" id="hat_element" value="" />
          <input type="hidden" name="hat_material" id="hat_material" value="" />

          <input type="hidden" name="hand_element" id="hand_element" value="" />
          <input type="hidden" name="hand_material" id="hand_material" value="" />
					
          <input type="hidden" name="hair_element" id="hair_element" value="" />
          <input type="hidden" name="hair_material" id="hair_material" value="" />
					
          <input type="hidden" name="body_element" id="body_element" value="" />
          <input type="hidden" name="body_material" id="body_material" value="" />
          <input type="hidden" name="hand_element" id="hand_element" value="" />
          <input type="hidden" name="hand_material" id="hand_material" value="" />
          <input type="hidden" name="pants_element" id="pants_element" value="" />
          <input type="hidden" name="pants_material" id="pants_material" value="" />
          <input type="hidden" name="shoes_element" id="shoes_element" value="" />
          <input type="hidden" name="shoes_material" id="shoes_material" value="" />

          <input type="hidden" name="eye_brows" id="eye_brows" value="" />
          <input type="hidden" name="eyes" id="eyes" value="" />
          <input type="hidden" name="lip" id="lip" value="" />
        </div>
      	<div style="float:left; width:100px; text-align:right">
        	<a href="#" id="dialog_link" class="ui-state-default ui-corner-all">
          	<span class="ui-icon ui-icon-disk"></span>Save Avatar
          </a>
        </div>
      	<div id="saved_notification" style="float:left; width:160px; text-align:center; display:none">
        	<font color="#00FF00" size="8px;">&bull;&nbsp;Avatar configuration saved.</font>
        </div>
      </div>
      
      <div class="clear"></div>
      
			<div>
				<?php
				$color_count = 19;
				$color_width = (540 / $color_count) - 2;
				
        for($idx = 1; $idx <= 19; $idx++){
        ?>
        
        <div class="skinChange" id="skinChange_<?php echo $idx; ?>" style="width: <?php echo $color_width; ?>px; height: <?php echo $color_width; ?>px; float: left;
              text-align: center; cursor: pointer; background-position: center top;
              background-repeat: no-repeat; margin: 1px; background-color:#FFF;
              background-image: url('<?php echo $this->basepath; ?>bundles/skintones/skin<?php echo $idx; ?>_icon.jpg');" >
              
            </div>
        
        <?php
        }
        ?>

      </div>

      <div class="clear"></div>
      
      <div id="tabs" style="float:left; width:535px; height:396px">
        <ul>
          <li><a href="#tabs-3">Hair</a></li>
          <li><a href="#tabs-7">Hat</a></li>
          <li><a href="#tabs-2">Face Part</a></li>
          <li><a href="#tabs-6">Top Body</a></li>
          <li><a href="#tabs-4">Pants</a></li>
          <li><a href="#tabs-5">Shoes</a></li>
        </ul>
        
        <div id="tabs-3" style="overflow-y:auto; height:320px;">
          <div id="hair_categories">
						<?php
            $items_array = $this->avatar_array['hair'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>
          </div>
          <div id="hair_items" style="display:none">
          
          </div>

        </div>


        <div id="tabs-7" style="overflow-y:auto; height:320px;">
        	<!--[setiap opsi adalah gabungan element dan material]-->
          <!--[*face*]-->
          <div id="hat_categories">
						<?php
            $items_array = $this->avatar_array['hat'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>
          </div>
          <div id="hat_items" style="display:none">
          
          </div>
          
        </div>


        <div id="tabs-2" style="overflow-y:auto; height:320px;">
					<div id="face_part_tabs" style="float:left; width:100%;">
            <ul>
              <li><a href="#tabs-eye-brows">Eye Brows</a></li>
              <li><a href="#tabs-eyes">Eyes</a></li>
              <li><a href="#tabs-lip">Lip</a></li>
            </ul>
		        <div id="tabs-eye-brows" style="overflow-y:auto; height:260px;">
              <div id="face_part_eye_brows_categories">
								<?php
                $items_array = $this->avatar_array['face_part_eye_brows'];
                foreach($items_array as $item){
                ?>
                <div 
                  id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
                  class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartCategoryChange" 
                  style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
                  <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
                </div>
                <?php
                }
                ?>
              </div>
              <div id="face_part_eye_brows_items" style="display:none">
              
              </div>

            </div>
		        <div id="tabs-eyes" style="overflow-y:auto; height:260px;">
              <div id="face_part_eyes_categories">
								<?php
                $items_array = $this->avatar_array['face_part_eyes'];
                foreach($items_array as $item){
                ?>
                <div 
                  id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
                  class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartCategoryChange" 
                  style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
                  <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
                </div>
                <?php
                }
                ?>
              </div>
              <div id="face_part_eyes_items" style="display:none">
              
              </div>

            </div>
		        <div id="tabs-lip" style="overflow-y:auto; height:260px;">

              <div id="face_part_lip_categories">
								<?php
                $items_array = $this->avatar_array['face_part_lip'];
                foreach($items_array as $item){
                ?>
                <div 
                  id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
                  class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartCategoryChange" 
                  style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
                  <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
                </div>
                <?php
                }
                ?>
              </div>
              <div id="face_part_lip_items" style="display:none">
              
              </div>

            </div>

          </div>
        </div>
        

        <div id="tabs-6" style="overflow-y:auto; height:320px;">
          <div id="body_categories">
						<?php
            $items_array = $this->avatar_array['body'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>
          </div>
          <div id="body_items" style="display:none">
          
          </div>
        </div>
        
        <div id="tabs-4" style="overflow-y:auto; height:320px;">

					<div id="pants_categories">
						<?php
            $items_array = $this->avatar_array['pants'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>
          </div>
          <div id="pants_items" style="display:none">
          
          </div>
        </div>
        <div id="tabs-5" style="overflow-y:auto; height:320px;">
					<div id="shoes_categories">
						<?php
            $items_array = $this->avatar_array['shoes'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . $item['gender'] . '__' . $item['size']; ?>" title="<?php echo $item['category']; ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarCategoryChange" 
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>
          </div>
          <div id="shoes_items" style="display:none">
          
          </div>
        </div>
      </div><!--[end of tabs]-->
		</div>

  </div>

	<?php if($this->is_admin){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			
			$.post("<?php echo $this->basepath; ?>avatar/user/get_size", {}, function(data){
				$('#current_size').html(data);
			});
			
			function set_default_configuration(){
				var gender 					= $("#select_gender").val();
				var size 						= $("#select_size").val();
				var tipe 						= $("#select_tipe").val();
				var element 				= $("#select_element_" + gender).val();
				var material 				= $("#select_material_" + gender).val();
				
				var skin						= $("#skin").val();
				
				var hat_element			= $("#hat_element").val();
				var hat_material		= $("#hat_material").val();
	
				var face_element		= $("#face_element").val();
				var face_material		= $("#face_material").val();
				var hair_element		= $("#hair_element").val();
				var hair_material		= $("#hair_material").val();
				//var hair_element2		= $("#hair_element2").val();
				//var hair_material2	= $("#hair_material2").val();
				var body_element		= $("#body_element").val();
				var body_material		= $("#body_material").val();
				var hand_element		= $("#hand_element").val();
				var hand_material		= $("#hand_material").val();
				var pants_element		= $("#pants_element").val();
				var pants_material	= $("#pants_material").val();
				var shoes_element		= $("#shoes_element").val();
				var shoes_material	= $("#shoes_material").val();
				var eye_brows				= $("#eye_brows").val();
				var eyes						= $("#eyes").val();
				var lip							= $("#lip").val();
				
				var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Hat','element':'"+hat_element+"','material':'"+hat_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";
	
				if($.trim(hat_element) == ''){
					var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";
				}
	
				// update the database
				$.post("<?php echo $_SESSION['basepath']; ?>avatar/admin/set_default_configuration", {gender: gender, avatar_conf: message, size: size, name: 'default ' + gender + ' ' + size}, function(data){
					if(data == "1"){
						// alert('Avatar configuration saved.');
						$('#saved_notification').show();
						var t = setTimeout("$('#saved_notification').hide()", 3000);
					} else {
						alert('Data: ' + data);
					}
				});
			}
			
			$('#set_default_avatar').live('click', function(){
				set_default_configuration();
			});
			
			
			
			$("#preset_dialog").dialog({
				autoOpen: false, 
				minWidth: 800, 
				minHeight: 400,
				modal: true
			});

			
			$('#show_preset').live('click', function(){
				$.post("<?php echo $this->basepath; ?>avatar/admin/preset/getall", {}, function(){
					
				});
				
				$("#preset_dialog").dialog('open');
			});
			
		});
	</script>
	<div class="grid_12">
		<div style="height: 10px;"></div>
		<div>
			<div style="float: left; width: 230px;">
				<a href="#" id="set_default_avatar" class="ui-state-default ui-corner-all">
					&nbsp;&bull;&nbsp;Save as default <span id="current_gender"></span> <span id="current_size"></span> avatar &nbsp;
				</a>
			</div>
			<div style="float: left; width: 150px; text-align: right;">
				<a href="#" id="show_preset" class="ui-state-default ui-corner-all">
					&nbsp;&bull;&nbsp;Save as preset &nbsp;
				</a>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<div class="container_12 pop_20_spacer">
</div>

<?php /* ?>
<div class="container_12">
	<div class="grid_12" style="float:left; height:34px; text-align:left;">
		<?php
		for($idx = 1; $idx <= 19; $idx++){
		?>
		
		<div class="skinChange" id="skinChange_<?php echo $idx; ?>" style="width: 30px; height: 30px; float: left;
					text-align: center; cursor: pointer; background-position: center top;
					background-repeat: no-repeat; margin: 2px; background-color:#FFF;
					background-image: url('<?php echo $this->basepath; ?>bundles/skintones/skin<?php echo $idx; ?>_icon.jpg');" >
          
        </div>
		
		<?php
		}
		?>
  </div>

</div>
<?php */ ?>

<?php if($this->is_admin){ ?>
<div style="width: auto; min-height: 400px; height: auto; min-width:800px; " class="ui-dialog-content ui-widget-content" id="preset_dialog" 
	title="Avatar Preset">
</div>
<?php } ?>

</div><!--[ end withjs ]-->