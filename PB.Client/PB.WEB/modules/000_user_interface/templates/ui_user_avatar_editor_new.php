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
		backgroundcolor: "FFFFFF",
		bordercolor: "00CCFF",
		textcolor: "00CCFF",
		logoimage: "<?php print($this->basepath); ?>bundles/webplayer/images/lilologo.small.png",
		progressbarimage: "<?php print($this->basepath); ?>bundles/webplayer/images/progressbar.small.png",
		progressframeimage: "<?php print($this->basepath); ?>bundles/webplayer/images/progressframe.small.png"
	};

	unityObject.embedUnity("unityPlayer", "<?php print($this->basepath); ?>bundles/webplayer/AvatarEditor.unity3d?<?php echo time(); ?>", 400, 480, params);
	
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
		// buggy: blm bisa nerima {'tipe':'Skin','color':13}
		
		var config_obj = eval('(' + data + ')');
		for(i = 0; i < config_obj.length; i++){
			var tipe = config_obj[i].tipe.toLowerCase();
			if(tipe == 'gender'){//sleep(2000);
				var gender = config_obj[i].element;
//				alert(gender);
				var gender_ = gender.replace("_base", "");
				$("#select_gender").val(gender_);
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

//			} else if(tipe == 'hair'){//sleep(2000);
//				var element = config_obj[i].element;
//				var material = config_obj[i].material;
//				$("#" + tipe + "_element").val(element);
//				$("#" + tipe + "_material").val(material);
//				
//				var element2 = config_obj[i].element2;
//				var material2 = config_obj[i].material2;
//				$("#" + tipe + "_element2").val(element2);
//				$("#" + tipe + "_material2").val(material2);
//				
//				var gender_ = $("#select_gender").val();
//				var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"','element2':'"+element2+"','material2':'"+material2+"'}";
////				var message = "{'gender':'"+gender_+"','tipe':'"+tipe+"','element':'"+element+"','material':'"+material+"'}";
////				alert(message);
//				GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
			} else if(tipe == 'skin'){//alert('Downloading ' + tipe + '...');//sleep(2000);
				var color = config_obj[i].color;
				color = parseInt(color);
				$("#skin").val(color);
				GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", color);
			} else {//alert('Downloading ' + tipe + '...');//sleep(2000);
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
-->
</script>


<script language="javascript">
	$(document).ready(function(){

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

		$.post("<?php echo $this->basepath; ?>avatar/user/get_gender", {}, function(data){
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
			if(data == 'small'){
				$(".big").hide();
				$(".medium").hide();
				$(".small").show();
			} else if(data == 'medium'){
				$(".big").hide();
				$(".small").hide();
				$(".medium").show();
			} else if(data == 'big'){
				$(".small").hide();
				$(".medium").hide();
				$(".big").show();
			}
			$("." + gender_op).hide();
		});

		function avatar_config_update_form(avatar_config){	// ga ngeset gender dan size, krn udah di set di fungsi yg akan panggil fungsi ini
			//alert(avatar_config);
//			contoh message = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_thin','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";

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
					
//					case 'hair':
//						$("#hair_element").val(message_eval[i].element);
//						$("#hair_material").val(message_eval[i].material);
//						$("#hair_element2").val(message_eval[i].element2);
//						$("#hair_material2").val(message_eval[i].material2);
//					break;

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
			$('.big').hide();
			$('.small').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('small')
//			var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_thin','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
//			var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1','element2':'female_hair1_bottom','material2':'female_hair1'},{'tipe':'Body','element':'female_body_thin','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_thin','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_thin','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1'},{'tipe':'Body','element':'female_body_thin','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_thin','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";

			var message = (gender == 'male') ? message_male : message_female;

			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
			
			avatar_config_update_form(message);
			
		});
		
		$('#radio_medium').click(function(){
			var gender = $("#select_gender").val();
			
			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('medium');
			
			$('.' + gender).show();
			$('.small').hide();
			$('.big').hide();
			$('.medium').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('medium')
//			var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},{'tipe':'Body','element':'male_body_medium','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_medium','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
//			var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1','element2':'female_hair1_bottom','material2':'female_hair1'},{'tipe':'Body','element':'female_body_medium','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_medium','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1'},{'tipe':'Body','element':'male_body_medium','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_medium','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1'},{'tipe':'Body','element':'female_body_medium','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_medium','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";

			var message = (gender == 'male') ? message_male : message_female;
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);

			avatar_config_update_form(message);
		});
		
		$('#radio_big').click(function(){
			var gender = $("#select_gender").val();

			var gender_op = (gender == 'male') ? 'female' : 'male';
			
			$("#select_size").val('big');
			
			$('.' + gender).show();
			$('.medium').hide();
			$('.small').hide();
			$('.big').show();
			$('.' + gender_op).hide();
			
			// load avatar sesuai gender dan size ('big')
//			var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},{'tipe':'Body','element':'male_body_fat','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_fat','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
//			var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1','element2':'female_hair1_bottom','material2':'female_hair1'},{'tipe':'Body','element':'female_body_fat','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_fat','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			var message_male = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1'},{'tipe':'Body','element':'male_body_fat','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_fat','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			var message_female = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1'},{'tipe':'Body','element':'female_body_fat','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_fat','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";

			var message = (gender == 'male') ? message_male : message_female;
			
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);

			avatar_config_update_form(message);
		});
		
		$("#radio_female").click(function(){	// tambahkan selector size disini!
			
			// buggy
			// SAMPE SENEEE....
			
			var size = $("#select_size").val();
			
			var size_op = (size == 'big') ? ".medium, .small" : ((size == 'medium') ? ".big, .small" : ".big, .medium" ) ;
			//alert(size_op);
			$('.' + size).show();
			$(".female").show();
			$(size_op).hide();
			$(".male").hide();

			
			
			$("#select_gender").val("female");
			
			// message harusnya diperoleh dari last configuration utk gender terpilih
//			var message_fat = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1','element2':'female_hair1_bottom','material2':'female_hair1'},{'tipe':'Body','element':'female_body_fat','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_fat','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
//			var message_medium = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1','element2':'female_hair1_bottom','material2':'female_hair1'},{'tipe':'Body','element':'female_body_medium','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_medium','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
//			var message_thin = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1','element2':'female_hair1_bottom','material2':'female_hair1'},{'tipe':'Body','element':'female_body_thin','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_thin','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			// {'tipe':'Hat','element':'','material':''} kalo element == '', ga perlu dimasukin ke message
			var message_fat = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1'},{'tipe':'Body','element':'female_body_fat','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_fat','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			var message_medium = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1'},{'tipe':'Body','element':'female_body_medium','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_medium','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			var message_thin = "[{'tipe':'gender','element':'female_base'},{'tipe':'Face','element':'female_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'female_hair1_top','material':'female_hair1'},{'tipe':'Body','element':'female_body_thin','material':'female_body'},{'tipe':'Pants','element':'female_short_pants_thin','material':'female_short_pants'},{'tipe':'Shoes','element':'female_shoes_01','material':'female_shoes_01'}, {'tipe':'Hand','element':'female_body_hand','material':'female_body'},{'tipe':'Skin','color':'1'}]";
			
			var message = message_fat;
			if(size == 'medium'){
				message = message_medium;
			} else if(size == 'small'){
				message = message_thin;
			}
			
			
//			alert(message);
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
//			GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", 1);
			
			$("#select_gender").val("female");
			$("#face_element").val("female_head");
			$("#face_material").val("");
			
			$("#hand_element").val("female_body_hand");
			$("#hand_material").val("female_body");
			
			$("#hair_element").val("female_hair1_top");
			$("#hair_material").val("female_hair1");
//			$("#hair_element2").val("female_hair1_bottom");
//			$("#hair_material2").val("female_hair1");

			$("#hat_element").val("");
			$("#hat_material").val("");

			$("#body_element").val("female_body_fat");
			$("#body_material").val("female_body");
			$("#pants_element").val("female_short_pants_fat");
			$("#pants_material").val("female_short_pants");
			$("#shoes_element").val("female_shoes_01");
			$("#shoes_material").val("female_shoes_01");

			$("#eye_brows").val("brows");
			$("#eyes").val("eyes");
			$("#lip").val("lip");

			$("#skin").val("1");
			
		});
		
		$("#radio_male").click(function(){	// hat_element dan hat_material belum ditentukan, blm ada assetnya

			var size = $("#select_size").val();
			
			$(".male").show();
			$(".female").hide();
			$("#select_gender").val("male");

			// message harusnya diperoleh dari last configuration utk gender terpilih
//			var message = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_face-1','material':'male_face-1','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair-2','material':'male_hair-2_blond'},{'tipe':'Body','element':'male_top-2','material':'male_top-2_green'},{'tipe':'Pants','element':'male_pants-1','material':'male_pants-1_green'},{'tipe':'Shoes','element':'male_shoes-2','material':'male_shoes-2_red'}]";
//			var message_fat = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},{'tipe':'Body','element':'male_body_fat','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_fat','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
//			var message_medium = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},{'tipe':'Body','element':'male_body_medium','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_medium','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
//			var message_thin = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1','element2':'male_hair_1_bottom','material2':'male_hair_1'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_thin','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";

			// {'tipe':'Hat','element':'','material':''}, kalo element == '', ga perlu dimasukkan ke message
			var message_fat = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1'},{'tipe':'Body','element':'male_body_fat','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_fat','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			var message_medium = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1'},{'tipe':'Body','element':'male_body_medium','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_medium','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			var message_thin = "[{'tipe':'gender','element':'male_base'},{'tipe':'Face','element':'male_head','material':'','eye_brows':'brows','eyes':'eyes','lip':'lip'},{'tipe':'Hair','element':'male_hair_1_top','material':'male_hair_1'},{'tipe':'Body','element':'male_body_thin','material':'male_body'},{'tipe':'Pants','element':'male_short_pant_thin','material':'male_short_pant'},{'tipe':'Shoes','element':'male_shoes_01','material':'male_shoes_01'},{'tipe':'Hand','element':'male_body_hand','material':'male_body'},{'tipe':'Skin','color':'1'}]";
			
			var message = message_fat;
			if(size == 'medium'){
				message = message_medium;
			} else if(size == 'small'){
				message = message_thin;
			}

			
//			alert(message);
			GetUnity().SendMessage("_DressRoom", "ChangeCharacterEvent", message);
//			GetUnity().SendMessage("_DressRoom", "ChangeSkinColor", 1);
			
			$("#select_gender").val("male");
			$("#face_element").val("male_head");
			$("#face_material").val("");
			
			$("#hand_element").val("male_body_hand");
			$("#hand_material").val("male_body");
			
			$("#hair_element").val("male_hair_1_top");
			$("#hair_material").val("male_hair_1");
			
//			$("#hair_element2").val("male_hair_1_bottom");
//			$("#hair_material2").val("male_hair_1");

			$("#hat_element").val("");
			$("#hat_material").val("");

			$("#body_element").val("male_body_fat");
			$("#body_material").val("male_body");
			$("#pants_element").val("male_short_pant_fat");
			$("#pants_material").val("male_short_pant");
			$("#shoes_element").val("male_shoes_01");
			$("#shoes_material").val("male_shoes_01");

			$("#eye_brows").val("brows");
			$("#eyes").val("eyes");
			$("#lip").val("lip");
			
			$("#skin").val("1");
			
		});

		$('.facePartChange, .avatarChange').hover(
			function(){
				$(this).addClass('shadow');
				$(this).addClass('light_blue_bg');
			}, 
			function(){
				$(this).removeClass("shadow");
				$(this).removeClass('light_blue_bg');
			}
		);

		$(".facePartChange").click(function(){
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
alert(message);
			$("#" + _id_split[0]).val(_id_split[1]);

		});

		$(".avatarChange").click(function(){
			var _id = $(this).attr('id');	// format id: eyes-male_eyes-male_eyes_blue, tipe-element-material
																		// eyes__male_eyes__male_eyes_green
			var _id_split = _id.split('__');
//			alert(_id);
			var gender = $("#select_gender").val();
			
//			if(_id_split[0] == 'hair'){
//				// __XSPACEX__
//				var _id_split_x = _id.split('__XSPACEX__');
//				var _id_split = _id_split_x[0].split('__');
//				var _id_split_2 = _id_split_x[1].split('__');
//				var message = "{'gender':'"+gender+"','tipe':'"+_id_split[0]+"','element':'"+_id_split[1]+"','material':'"+_id_split[2]+"','element2':'"+_id_split_2[0]+"','material2':'"+_id_split_2[1]+"'}";
//				
//				$("#" + _id_split[0] + "_element2").val(_id_split_2[0]);
//				$("#" + _id_split[0] + "_material2").val(_id_split_2[1]);
//
//				
//			} else {
				var message = "{'gender':'"+gender+"','tipe':'"+_id_split[0]+"','element':'"+_id_split[1]+"','material':'"+_id_split[2]+"'}";
//			}
			
alert(message);
			GetUnity().SendMessage("_DressRoom", "ChangeElementEvent", message);
			
			$("#" + _id_split[0] + "_element").val(_id_split[1]);
			$("#" + _id_split[0] + "_material").val(_id_split[2]);
			
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

			
//			var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"','element2':'"+hair_element2+"','material2':'"+hair_material2+"'},{'tipe':'Hat','element':'"+hat_element+"','material':'"+hat_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";
			var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Hat','element':'"+hat_element+"','material':'"+hat_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";

			if($.trim(hat_element) == ''){
				var message = "[{'tipe':'gender','element':'"+ gender +"_base'},{'tipe':'Face','element':'"+face_element+"','material':'"+face_material+"','eye_brows':'"+eye_brows+"','eyes':'"+eyes+"','lip':'"+lip+"'},{'tipe':'Hair','element':'"+hair_element+"','material':'"+hair_material+"'},{'tipe':'Body','element':'"+body_element+"','material':'"+body_material+"'},{'tipe':'Pants','element':'"+pants_element+"','material':'"+pants_material+"'},{'tipe':'Shoes','element':'"+shoes_element+"','material':'"+shoes_material+"'},{'tipe':'Hand','element':'"+hand_element+"','material':'"+hand_material+"'}, {'tipe':'Skin','color':'"+skin+"'}]";
			}

			// update the database
			$.post("<?php echo $_SESSION['basepath']; ?>avatar/user/set_configuration", {avatar_conf: message, size: size}, function(data){
				if(data == "1"){
					alert('Avatar configuration saved.');
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

	});

</script>

<div class="centered shadow transparent_70" style="width:960px;">

	<div style="float:left; width:960px; height:28px;">

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
	<div style="float:left; width:520px; text-align:center" id="div_asset_chooser">



  	<div id="config_form" style="display:none;" style="width:520px">
      <div id="save_conf" style="float:left; width:520px; height:28px; text-align:right;">
      	<div style="float:left; width:404px; text-align:left">
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
          <!--input type="hidden" name="hair_element2" id="hair_element2" value="" /-->
          <!--input type="hidden" name="hair_material2" id="hair_material2" value="" /-->
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
      </div>
      <div id="tabs" style="float:left; width:500px; height:440px">
        <ul>
          <li><a href="#tabs-1">Head</a></li>
          <li><a href="#tabs-7">Hat</a></li>
<!--[          <li><a href="#tabs-2">Face</a></li>	]-->
          <li><a href="#tabs-2">Face Part</a></li>
          <li><a href="#tabs-3">Hair</a></li>
          <li><a href="#tabs-0">Hand</a></li>
          <li><a href="#tabs-4">Pants</a></li>
          <li><a href="#tabs-5">Shoes</a></li>
          <li><a href="#tabs-6">Top Body</a></li>
        </ul>
        <div id="tabs-1" style="overflow-y:auto; height:320px;">
        	<!--[setiap opsi adalah gabungan element dan material]-->
          <!--[*face*]-->
        <?php
				$items_array = $this->avatar_array['face'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
          
        </div>

        <div id="tabs-7" style="overflow-y:auto; height:320px;">
        	<!--[setiap opsi adalah gabungan element dan material]-->
          <!--[*face*]-->
        <?php
				$items_array = $this->avatar_array['hat'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>
          
        </div>


        <div id="tabs-2" style="overflow-y:auto; height:320px;">
					<div id="face_part_tabs" style="float:left; width:100%;">
            <ul>
              <li><a href="#tabs-eye-brows">Eye Brows</a></li>
              <li><a href="#tabs-eyes">Eyes</a></li>
              <li><a href="#tabs-lip">Lip</a></li>
            </ul>
		        <div id="tabs-eye-brows" style="overflow-y:auto; height:260px;">
							<?php
              $items_array = $this->avatar_array['face_part_eye_brows'];
              foreach($items_array as $item){
              ?>
              <div 
                id="<?php echo /*$item['tipe']*/'eyeBrows' . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
                class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartChange" 
                style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
                <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
              </div>
              <?php
              }
              ?>

            </div>
		        <div id="tabs-eyes" style="overflow-y:auto; height:260px;">
							<?php
              $items_array = $this->avatar_array['face_part_eyes'];
              foreach($items_array as $item){
              ?>
              <div 
                id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
                class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartChange" 
                style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
                <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
              </div>
              <?php
              }
              ?>

            </div>
		        <div id="tabs-lip" style="overflow-y:auto; height:260px;">

						<?php
            $items_array = $this->avatar_array['face_part_lip'];
            foreach($items_array as $item){
            ?>
            <div 
              id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
              class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> facePartChange" 
              style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
              <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
            </div>
            <?php
            }
            ?>

            </div>

          </div>
        </div>
        
        
        <div id="tabs-3" style="overflow-y:auto; height:320px;">
        <?php
				$items_array = $this->avatar_array['hair'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); //echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']) . '__XSPACEX__' .  str_ireplace('.unity3d', '', $item['element2']) . '__' . str_ireplace('.unity3d', '', $item['material2']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>

        </div>
        
        <div id="tabs-0" style="overflow-y:auto; height:320px;">
        <?php
				$items_array = (array)$this->avatar_array['hand'];
        foreach($items_array as $item){
				?>
        <div 
          id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
          class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
          style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
          <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
        </div>
        <?php
				}
				?>

        </div>

        
        <div id="tabs-4" style="overflow-y:auto; height:320px;">

					<?php
          $items_array = $this->avatar_array['pants'];
          foreach($items_array as $item){
          ?>
          <div 
            id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
            class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
            style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
            <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
          </div>
          <?php
          }
          ?>
        </div>
        <div id="tabs-5" style="overflow-y:auto; height:320px;">

					<?php
          $items_array = $this->avatar_array['shoes'];
          foreach($items_array as $item){
          ?>
          <div 
            id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
            class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
            style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
            <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
          </div>
          <?php
          }
          ?>
          

        </div>
        <div id="tabs-6" style="overflow-y:auto; height:320px;">
					<?php
          $items_array = $this->avatar_array['body'];
          foreach($items_array as $item){
          ?>
          <div 
            id="<?php echo $item['tipe'] . '__' . str_ireplace('.unity3d', '', $item['element']) . '__' . str_ireplace('.unity3d', '', $item['material']); ?>"
            class="<?php echo $item['gender']; ?> <?php echo $item['size']; ?> avatarChange" 
            style="width:100px; height:130px; float:left; text-align:center; cursor:pointer; background-position:top; background-repeat:no-repeat; background-image:url('<?php echo $this->preview_dir . $item['preview_image']; ?>')">
            <div style="position:relative; top:100px;"><?php echo $item['name']; ?></div>
          </div>
          <?php
          }
          ?>

        </div>
      </div>

		</div>

  </div>

	<div style="float:left; max-width:960px; height:34px; text-align:left; overflow:auto; margin-top:15px; margin-left:18px; background-color:#FFF;">
		<?php
		for($idx = 1; $idx <= 19; $idx++){
		?>
		
		<div class="skinChange" id="skinChange_<?php echo $idx; ?>" style="width: 30px; height: 30px; float: left;
					text-align: center; cursor: pointer; background-position: center top;
					background-repeat: no-repeat; margin: 2px;
					background-image: url('<?php echo $this->basepath; ?>bundles/skintones/skin<?php echo $idx; ?>_icon.jpg');" >
          
        </div>
		
		<?php
		}
		?>
  </div>

</div>



