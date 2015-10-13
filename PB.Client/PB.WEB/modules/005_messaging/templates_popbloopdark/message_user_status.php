<style>
.jquery-slider-pages {
    bottom: 5px;
    height: 20px;
    left: 5px;
    overflow: visible;
    position: absolute;
    right: 48px !important;
}
</style>

<script type="text/javascript" src="<?php print($this->basepath); ?>libraries/js/jquery.slider/jquery.slider.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php print($this->basepath); ?>libraries/js/jquery.slider/jquery.slider.css" media="all" />

<div class="withjs">

<script language="javascript">
	$(document).ready(function(){
														 
		$.post("<?php echo $this->basepath; ?>article/guest/getslides", {}, function(data){
			var all_slides = eval('('+data+')');
//alert(data);
			var html = '';
			
			for(idx = 0; idx < all_slides.length; idx++){
				var _no = all_slides[idx]['no'];
				var _title = all_slides[idx]['title'];
				var _description = all_slides[idx]['description'];
				var _image = all_slides[idx]['image'];
				var _link = all_slides[idx]['link'];
				var _lilo_id = all_slides[idx]['lilo_id'];
				
				html = html + "<div style=\"background:url('"+_image+"') top left no-repeat; width:100%; height:220px;\">";
				html = html + "<a style=\"text-decoration:none;\" href='"+_link+"'>";
				html = html + "<div style='float:left; width: 390px; height:220px;'>&nbsp;</div>";
				html = html + "<div style='float:left; width: 500px; height:220px;'>";
				html = html + "<div style='height:40px; width:100%; font-size:12px; color:#FFF; font-weight:normal;'>&nbsp;</div>";
				html = html + "<div style='height:40px; width:100%; font-size:28px; color:#FFF; font-weight:normal; position:relative; text-align:left;'><span style:\"position:absolute; bottom:0; right:0;\">"+_title+"</span></div>";
				html = html + "<div style='height:100px; width:100%; font-size:12px; color:#FFF; font-weight:normal; text-align: justify;'>"+_description+"</div>";
				html = html + "</div>";
				html = html + "</a>";
				html = html + "</div>";
				
//				html = html + "<tr>";
//				html = html + "<td style=width:10px;>"+_no+"</td>";
//				html = html + "<td style='width:200px;'>"+_title+"</td>";
//				html = html + "<td style='width:250px;'><img src='"+_image+"' style='max-width:250; max-height:100px;' /></td>";
//				html = html + "<td style='width:300px;'><textarea style='min-width:300px; min-height:100px; max-width:300px; max-height:100px; font-size:7px; background:transparent; border:0; color:#666;'>"+_description+"</textarea></td>";
//				html = html + "<td style='width:100px;'>Edit<br />Delete</td>";
//				html = html + "</tr>";
			}
			
//			alert(html);
			$('#slider').html(html);
			
			$('#slider').slider({showControls: false, showProgress: true, hoverPause: true, wait: 5000});
		});

														 

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

					html_ = html_ + "<div style='width:300px; float:left; color:#fff; text-align:justify; text-justify: newspaper;'>"+all_messages.msg_inbox[i]['dm']+"</div>";
					html_ = html_ + "<div style='width:300px; height:5px; float:left;'>&nbsp;</div>";
					html_ = html_ + "<div style='width:300px; height:20px; float:left; font-size:80%;'>"+all_messages.msg_inbox[i]['time_word']+"</div>";
					html_ = html_ + "<div style='width:300px; height:10px; float:left;'>&nbsp;</div>";

				}
				
				$('#msg_content_inbox').html(html_);

			});
			
			
		}
		
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

<div class="container_12">
  <div class="grid_12 pop_news" id="slider" style="text-align:center">

	</div>
  <div class="clear"></div>
</div>

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
  <div class="grid_8">
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