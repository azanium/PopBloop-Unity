<div class="withjs">

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
/*
	var params = {
		disableContextMenu: true,
		backgroundcolor: "2c2c2c",
		bordercolor: "1F1F1F",
		textcolor: "000000",
		logoimage: "<?php print($this->basepath); ?>bundles/webplayer/images/lilologo.png",
		progressbarimage: "<?php print($this->basepath); ?>bundles/webplayer/images/loading4.png",
		progressframeimage: "<?php print($this->basepath); ?>bundles/webplayer/images/loading4.frame.png"
	};
*/	
	
	var params = {
		disableContextMenu: true,
		backgroundcolor: "1F1F1F",/*2c2c2c*/
		bordercolor: "1F1F1F",
		textcolor: "000000",
/*		logoimage: "<?php print($this->basepath); ?>bundles/webplayer/images/lilologo.png",*/
		logoimage: "<?php print($this->basepath); ?>bundles/webplayer/images/hangout.bg.png",
		/*progressbarimage: "<?php print($this->basepath); ?>bundles/webplayer/images/loading4.png",
		progressframeimage: "<?php print($this->basepath); ?>bundles/webplayer/images/loading4.frame.png"*/
	};
	
	
	unityObject.embedUnity("unityPlayer", "<?php print($this->basepath); ?>bundles/webplayer/Play.unity3d", 940, 400, params);
	
	/*
		backgroundcolor: "FFFFFF",

		progressbarimage: "<?php print($this->basepath); ?>bundles/webplayer/images/progressbar.png",
		progressframeimage: "<?php print($this->basepath); ?>bundles/webplayer/images/progressframe.png"
	
	*/
	
}
function OnGameInit() {
	//$("#config_form").show();

	GetUnity().SendMessage("_Game", "StartGame", ""); //GetUnity().SendMessage("_Game", "StartGame", "FB_City");
}

function shoutPost(text){
  if($.trim(text) != ''){
//			alert(text);
    
    var shout = text;
    var session_id = "<?php echo $this->session_id; ?>";
    var circle = "";
    
    $.post("<?php print($this->basepath); ?>message/user/shout", {shout: shout, session_id: session_id, circle: circle, ingame: 1}, function(data){
      if($.trim(data) == "OK"){
        loadMessages();
        $('#shout').val('');
//					window.location.replace("<?php echo $this->basepath; ?>social");
//					alert(data + " - Shout shouted :)");
      } else {
//					alert(data + " - Shout ga ke shout :( ");
      }
    });

    
  } else {
//			alert('text ga boleh kosong');
  }
  
}

function loadMessages(){
  $.post("<?php print($this->basepath); ?>message/user/loadmessages", {}, function(data){
//				$('#msg_content_home').append(data + " <br />");
    var all_messages = eval('('+data+')');
//				alert(all_messages.msg_home[0]['user_id']);
    var html_ = '';
    
    for(i = 0; i < all_messages.msg_home.length; i++){
      // alert(all_messages.msg_home[i]['time']);
      html_ = html_ + "<div style='width:40px; height:40px; float:left;'><img src='<?php print($this->basepath); ?>user_generated_data/profile_picture/"+all_messages.msg_home[i]['profile_picture']+"' style='max-width:40px; max-height:40px;' /></div>";
      html_ = html_ + "<div style='width:10px; height:40px; float:left;'>&nbsp;</div>";
      html_ = html_ + "<div style='width:250px; height:40px; float:left;'><div style='width:250px; height:14px; float:left; color:#fff;'>"+all_messages.msg_home[i]['fullname']+"</div><div style='width:250px; height:26px; float:left; overflow: hidden; font-size:80%;'>"+$.trim(all_messages.msg_home[i]['description'])+"</div></div>";

      html_ = html_ + "<div style='width:300px; float:left; color:#fff;'>"+all_messages.msg_home[i]['shout']+"</div>";
      html_ = html_ + "<div style='width:300px; height:5px; float:left;'>&nbsp;</div>";
      html_ = html_ + "<div style='width:300px; height:20px; float:left; font-size:80%;'>"+all_messages.msg_home[i]['time_word']+"</div>";
      html_ = html_ + "<div style='width:300px; height:10px; float:left;'>&nbsp;</div>";

    }
    
    $('#msg_content_home').html(html_);
    
//				$('#msg_content_home').html(all_messages.msg_home);
//				$('#msg_content_me').html(all_messages[msg_me]);
//				$('#msg_content_inbox').html(all_messages[msg_inbox]);

    var html_ = '';
    
    for(i = 0; i < all_messages.msg_me.length; i++){
      // alert(all_messages.msg_home[i]['time']);
      html_ = html_ + "<div style='width:40px; height:40px; float:left;'><img src='<?php print($this->basepath); ?>user_generated_data/profile_picture/"+all_messages.msg_me[i]['profile_picture']+"' style='max-width:40px; max-height:40px;' /></div>";
      html_ = html_ + "<div style='width:10px; height:40px; float:left;'>&nbsp;</div>";
      html_ = html_ + "<div style='width:250px; height:40px; float:left;'><div style='width:250px; height:14px; float:left; color:#fff;'>"+all_messages.msg_me[i]['fullname']+"</div><div style='width:250px; height:26px; float:left; overflow: hidden; font-size:80%;'>"+$.trim(all_messages.msg_me[i]['description'])+"</div></div>";

      html_ = html_ + "<div style='width:300px; float:left; color:#fff;'>"+all_messages.msg_me[i]['shout']+"</div>";
      html_ = html_ + "<div style='width:300px; height:5px; float:left;'>&nbsp;</div>";
      html_ = html_ + "<div style='width:300px; height:20px; float:left; font-size:80%;'>"+all_messages.msg_me[i]['time_word']+"</div>";
      html_ = html_ + "<div style='width:300px; height:10px; float:left;'>&nbsp;</div>";

    }
    
    $('#msg_content_me').html(html_);
    

    var html_ = '';
    
    for(i = 0; i < all_messages.msg_inbox.length; i++){
      // alert(all_messages.msg_home[i]['time']);
      html_ = html_ + "<div style='width:40px; height:40px; float:left;'><img src='<?php print($this->basepath); ?>user_generated_data/profile_picture/"+all_messages.msg_inbox[i]['profile_picture']+"' style='max-width:40px; max-height:40px;' /></div>";
      html_ = html_ + "<div style='width:10px; height:40px; float:left;'>&nbsp;</div>";
      html_ = html_ + "<div style='width:250px; height:40px; float:left;'><div style='width:250px; height:14px; float:left; color:#fff;'>"+all_messages.msg_inbox[i]['fullname']+"</div><div style='width:250px; height:26px; float:left; overflow: hidden; font-size:80%;'>"+$.trim(all_messages.msg_inbox[i]['description'])+"</div></div>";

      html_ = html_ + "<div style='width:300px; float:left; color:#fff;'>"+all_messages.msg_inbox[i]['dm']+"</div>";
      html_ = html_ + "<div style='width:300px; height:5px; float:left;'>&nbsp;</div>";
      html_ = html_ + "<div style='width:300px; height:20px; float:left; font-size:80%;'>"+all_messages.msg_inbox[i]['time_word']+"</div>";
      html_ = html_ + "<div style='width:300px; height:10px; float:left;'>&nbsp;</div>";

    }
    
    $('#msg_content_inbox').html(html_);


  });
  
  
}
    
function get_session_id(){
	var session_id = "<?php echo $this->session_id; ?>";
//	alert(session_id);
	GetUnity().SendMessage("_Game", "GetUserId", session_id);
}

function get_session(){
  return "<?php echo $this->session_id; ?>";
}

-->

$(document).ready(function(){
	$('.friendlist_bottom_content').slideUp();
//	$('.friendlist_bottom_header').live('click', function(){
//		// do something
//	});

	$('#control_toggle').live('click', function(){
		$('#control_div').slideToggle('slow');
		var cur_attr = $('#show_hide_control').attr('src');
		if($.trim(cur_attr) == '<?php print($this->basepath); ?>modules/000_user_interface/images_popbloopdark/icons/howto.show.png'){
			$('#show_hide_control').attr('src', '<?php print($this->basepath); ?>modules/000_user_interface/images_popbloopdark/icons/howto.hide.png');
		} else {
			$('#show_hide_control').attr('src', '<?php print($this->basepath); ?>modules/000_user_interface/images_popbloopdark/icons/howto.show.png');
		}
	});


	
});



</script>

<!--[new]-->
<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_12 pop_webplayer">
			<div id="unityPlayer" style="width: 100%; text-align: center;">
				<div class="missing" style="width: 100%; text-align: center; height: 400px; position: relative; top: 20px;">
					<a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!"><img alt="Unity Web Player. Install now!" src="<?php print($this->basepath); ?>bundles/webplayer/images/getunity.wide.png" width="940" height="400" />
						<!--img alt="Unity Web Player. Install now!" src="http://webplayer.unity3d.com/installation/getunity.png" width="193" height="63" /-->
						<!--img alt="Unity Web Player. Install now!" src="<?php print($this->basepath); ?>bundles/webplayer/images/getunity.wide.png" width="900" height="400" /-->
					</a>
				</div>
			</div>
	</div>
  <div class="clear"></div>
</div>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12">
  <div class="grid_12" style="border: solid 1px #1f1f1f;">
	  <div class="grid_12 alpha" id="control_toggle" style="text-align:center; height:16px; cursor:pointer;" title="Control Guide">
  	  <img id="show_hide_control" src="<?php print($this->basepath); ?>modules/000_user_interface/images_popbloopdark/icons/howto.hide.png" />
    </div>
    <div class="clear"></div>
  	<div class="grid_12 alpha" id="control_div" style="text-align:left; padding-bottom: 10px;">
		  <img src="<?php print($this->basepath); ?>images/controls.play.jpg" />
    </div>
    <div class="clear"></div>
  </div>
</div>



<script language="javascript">
	$(document).ready(function(){
		$('#btn_shout').live('click', function(){
			var shout = $('#shout').val();// alert("Your shout: " + shout);
			var session_id = $('#session_id').val();
			var circle = $('#circle').val();
			
			$.post("<?php print($this->basepath); ?>message/user/shout", {shout: shout, session_id: session_id, circle: circle}, function(data){
				if($.trim(data) == "OK"){
					loadMessages();
					$('#shout').val('');
//					window.location.replace("<?php echo $this->basepath; ?>social");
//					alert(data + " - Shout shouted :)");
				} else {
//					alert(data + " - Shout ga ke shout :( ");
				}
			});
		});
		
		

		
		// saat halaman load, langsung tampilkan friend list
		loadMessages();
		
		// fungsi yg merequest ke server secara berkala untuk menjaga apa yg ditampilkan di halaman ini tetap fresh
		function heartBeat(){
			loadMessages();
		}
		
		setInterval(heartBeat, <?php echo isset($this->heartBeatInterval) ? $this->heartBeatInterval : "60000"; ?>);

		
		
	});
</script>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12" style="display: none;">
  <div class="grid_2">&nbsp;
  </div>
  
  <!--[*
  <div class="grid_6">
    <textarea name="shout" id="shout" class="msg_shout_area" maxlength="500"></textarea>
	</div>
  <div class="grid_2" style="display:table-cell; vertical-align:middle">
    <input style="width:100%; height:30px;" type="button" id="btn_shout" name="btn_shout" value="Shout!" />
  </div>
  *]-->
  <div class="grid_8" style="padding-top: 10px;">
    <div style="width:520px; float:left;">
	    <input type="hidden" name="circle" id="circle" value="" />
      <input type="text" name="shout" id="shout" class="round_left" maxlength="500" style="width:580px; height:36px; text-align:center; background-color:#666; color:#fff; border:0; font-family:inherit; font-weight:normal; font-size:12px;" />
    </div>
    <div id="btn_shout" style="width:100px; float:left; text-align:center; height:38px; cursor:pointer; background:url(<?php print($this->basepath); ?>/tide/popbloop.img/shout.png) left no-repeat #666;" class="round_right">&nbsp;
    </div>
  </div>
  
  <div class="grid_2">
  </div>
  <div class="clear"></div>
</div>

<div class="container_12 pop_20_spacer">
</div>

<div class="container_12" style="display: none;">
  <div class="grid_4">
  	<div class="msg_header">Home</div>
  	<div class="msg_content" id="msg_content_home"></div>
  </div>
  <div class="grid_4">
  	<div class="msg_header">Me</div>
  	<div class="msg_content" id="msg_content_me"></div>
  </div>
  <div class="grid_4">
  	<div class="msg_header">Inbox</div>
  	<div class="msg_content" id="msg_content_inbox"></div>
  </div>
  <div class="clear"></div>
</div>


</div><!--[ end withjs ]-->