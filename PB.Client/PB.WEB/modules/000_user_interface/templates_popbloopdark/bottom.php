<div class="container_12 withjs">
  <div class="grid_12">
		<br />
		<br />
	</div>
  <div class="clear"></div>
</div>

<div class="clear"></div>

<div class="container_12 withjs">
	<div class="grid_12">
		<div style="height: 34px; background-color: #333; text-align: center;">
			<div class="bottom_menu" style="text-align: center;">
				<a href="<?php echo $this->basepath; ?>whatis">What Is</a>
				<a>Blog</a>
				<a href="<?php echo $this->basepath; ?>toc">Terms &amp; Condition</a>
				<a>Contact</a>
			</div>
		</div>
	</div>
</div>

<div class="clear"></div>

<div class="container_12 withjs">
  <div class="grid_12">
		<div style="width: 100%; text-align: center; color: #fff; font-size: 11px; margin: 5px;">Copyright &copy; 2012 Popbloop. The content is copyrighted to Popbloop and may not be reproduced on other websites</div>
	</div>
</div>

<div class="container_12 withjs">
  <div class="grid_12">
		<br />
		<br />
		<br />
	</div>
  <div class="clear"></div>
</div>




<?php
if($this->logged_in && $_SESSION['showchat']){
/*
	scroll otomatis setiap update data di .chatwindowcontent
<script language="javascript" src="<?php echo $this->basepath; ?>libraries/js/jquery.smooth.scroll/jquery.smooth-scroll.js"></script>

      $('button.scrollsomething').click(function() {
        $.smoothScroll({
          scrollElement: $('div.scrollme'),	// div yg mau di scroll
          scrollTarget: '#findme'						// id element tujuan scroll
        });
        return false;
      });

	atau baca: http://stackoverflow.com/questions/2291645/jquery-div-autoscroll

*/

?>


<script language="javascript">
	$(document).ready(function(){
		$('.open_profile').live('click', function(){
			var href_ = $(this).attr('href');
//			alert(href_);
			window.location.replace(href_);
			return false;
		});

		$.post("<?php echo $this->basepath; ?>ui/user/getdisplayconfig", {key: 'friendlist_visible'}, function(data){
//			alert(data);
			if(data == 'visible'){
				$('.friendlist_bottom_content').show();
			} else if(data == 'notvisible'){
				$('.friendlist_bottom_content').hide();
			}
		});
		
//		var shown_id = ["4e38df26c1b4ba8c09000001"];
//		for(idx = 0; idx <= shown_id.length; idx++){
//			var _id = shown_id[idx];
//
//			var chatwindowexists = $('#chatwindow' + _id).length > 0;alert(chatwindowexists);
//			if(!chatwindowexists){
//				text = "<div class='chatwindow' id='chatwindow"+ _id +"'>";
//				text += "<div class='chatwindowtitle' id='chatwindowtitle"+ _id +"'><div style='text-align:center; float:left; min-width:180px;'>"+_id+"</div><div class='toggle_item_chat' id='showhidechatwindowcontent"+ _id +"'>[&bull;]</div></div>";
//				text += "<div class='chatwindowcontent transparent_70' id='chatwindowcontent"+ _id +"'></div>";
//				text += "<div class='chatwindowtext transparent_70' id='chatwindowtext"+ _id +"'><textarea class='chattextarea' id='chattextarea"+ _id +"' ></textarea></div>";
//				text += "</div>";
//				
//				$('.chatarea').append(text);
//			} else {
//				$('#chatwindow' + _id).show();
//			}
//		}
		
		$('.friendlist_bottom_header').live('click', function(){
			var friendlist_visible = $('.friendlist_bottom_content').is(":visible") ? 'notvisible' : 'visible';
			
			$('.friendlist_bottom_content').slideToggle('slow');
			
			// ui_user_displayconfig

			$.post("<?php echo $this->basepath; ?>ui/user/displayconfig", {key: 'friendlist_visible', val: friendlist_visible}, function(data){
//				alert(data);
			});
			
		});
		
		
		
		// dapatkan friend list
		// refresh setiap x seconds
		
		function loadFriendList(){
			$.post("<?php echo $this->basepath; ?>friend/user/ws_friendlist", {}, function(data){
				if(data.substr(0,16) == "##ACCESSDENIED##"){
					document.write(data);
					return true;
				}
	//			alert(data);
				/*
				[
					["Management",{"user_id":"4df6e7192cbfd4e6c000fd9b","friend_id":"4e38df26c1b4ba8c09000001","circle_array":["Management"],"approval_time":1318865716,"lilo_id":"4e9c4b3489b38f4c0b000000","email":"qurrota@m-stars.net","username":"qurrota","fullname":"Qurrota Ayun","foto":"default.png","foto_url":"http:\/\/localhost\/lilo3a\/user_generated_data\/profile_picture\/default.png"}],
				 
					["Outer Circle"]
				]
				*/
				//alert(data);
				
				var all_friends = eval('('+data+')');
				var text = '';
				for(idx = 0; idx < all_friends.length; idx++){
					var curr_circle = all_friends[idx][0];
					var curr_circle_no_space = curr_circle.replace(/\s/g, '__SPASI__');
					// alert("curr_circle: " + curr_circle + ", curr_circle_no_space: " + curr_circle_no_space);
					text += "<div class='friendlist_bottom_circle_name' id='div_"+curr_circle_no_space+"'>"+curr_circle+"</div>";
					
					// div list friend di curr_circle
					text += "<div class='friendlist_bottom_content' id='content_"+curr_circle_no_space+"'>";
					
					for(i = 1; i < all_friends[idx].length; i++){
						text += "<div class='friendlist_bottom_item' id='item_"+all_friends[idx][i]['friend_id']+"'>";// alert('woi ' + i + ", " + all_friends[idx][i]['friend_id']);
						
	//					text += "<img src='<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/icon.msg.10x10.png' >";
	//					text += "<img src='<?php echo $this->basepath; ?>modules/000_user_interface/images/icons/icon.chat.10x10.png' >";
						text += "<div style='float:left; width: 120px;'>";
						text += all_friends[idx][i]['fullname'];//alert(all_friends[idx][i]['fullname']);
						text += "</div>";
						text += "<div style='float:left; width: 20px; text-align:center;'><a title='Open "+all_friends[idx][i]['fullname']+"`s profile page' class='open_profile' href='<?php echo $this->basepath; ?>profile/"+all_friends[idx][i]['username']+"'><img style='max-width:10px; max-height:10px;' src='<?php echo $this->basepath; ?>modules/000_user_interface/images_popbloopdark/icons/small.profile.user.png'></a>";
						text += "</div>";
						
						
						text += "</div>";
//					alert("friend id: " + all_friends[idx][i]['friend_id']);
						var af_id = all_friends[idx][i]['friend_id'];//alert('af_id: ' + af_id);
						var af_fullname = all_friends[idx][i]['fullname'];
						$.post("<?php echo $this->basepath; ?>ui/user/getdisplayconfig", {key: 'showchatwindowitem_' + all_friends[idx][i]['friend_id']}, function(data_){ //alert('data: ' + data + ", id: " + af_id);
							if($.trim(data_) == "1" || data_ == 1){
								var chatwindowexists = $('#chatwindowitem_' + af_id).length > 0;
								if(!chatwindowexists){
									text = "<div class='chatwindow shadow' id='chatwindowitem_"+ af_id +"'>";
									text += "<div class='chatwindowtitle' id='chatwindowtitleitem_"+ af_id +"'><div style='text-align:center; float:left; width:170px; height:20px; overflow:hidden;'>"+af_fullname+"</div><div title='Message Archive' class='bottom_message_archive' id='bottommessagearchiveitem_"+ af_id +"'>&nbsp;</div><div title='Hide Chat Window' class='toggle_item_chat' id='showhidechatwindowcontentitem_"+ af_id +"'>&nbsp;</div></div>";
									text += "<div class='chatwindowcontent transparent_70' id='chatwindowcontentitem_"+ af_id +"'></div>";
									text += "<div class='chatwindowtext transparent_70' id='chatwindowtextitem_"+ af_id +"'><textarea class='chattextarea' id='chattextareaitem_"+ af_id +"' maxlength='200'></textarea></div>";
									text += "</div>";
									
									$('.chatarea').append(text);
								} else {
									$('#chatwindowitem_' + af_id).show();
								}
							} else {
//								alert(data_);
							}
						});
						
						$.post("<?php echo $this->basepath; ?>friend/user/chatalert", {}, function(data__){
							var all_alerts = eval('('+data__+')');
							for(i = 0; i < all_alerts.length/2; i++){
								var af_id = all_alerts[i];
								var fullname_idx = i+all_alerts.length/2;
								var af_fullname = all_alerts[fullname_idx];
								var chatwindowexists = $('#chatwindowitem_' + af_id).length > 0;
								if(!chatwindowexists){
									text = "<div class='chatwindow shadow' id='chatwindowitem_"+ af_id +"'>";
									text += "<div class='chatwindowtitle' id='chatwindowtitleitem_"+ af_id +"'><div style='text-align:center; float:left; width:170px; height:20px; overflow:hidden;'>"+af_fullname+"</div><div title='Message Archive' class='bottom_message_archive' id='bottommessagearchiveitem_"+ af_id +"'>&nbsp;</div><div title='Hide Chat Window' class='toggle_item_chat' id='showhidechatwindowcontentitem_"+ af_id +"'>&nbsp;</div></div>";
									text += "<div class='chatwindowcontent transparent_70' id='chatwindowcontentitem_"+ af_id +"'></div>";
									text += "<div class='chatwindowtext transparent_70' id='chatwindowtextitem_"+ af_id +"'><textarea class='chattextarea' id='chattextareaitem_"+ af_id +"' maxlength='200'></textarea></div>";
									text += "</div>";
									
									$('.chatarea').append(text);
								} else {
									$('#chatwindowitem_' + af_id).show();
								}
								
//								alert("alert: " + all_alerts[i]);
							}
						});

	
					}
					
					text += "</div>";
					
				}
				
				$('.friendlist_bottom_content').html(text);
	
			});
		}
		
		// saat halaman load, langsung tampilkan friend list
		loadFriendList();
		
		// fungsi yg merequest ke server secara berkala untuk menjaga apa yg ditampilkan di halaman ini tetap fresh
		function heartBeat(){
			loadFriendList();
		}
		
		setInterval(heartBeat, <?php echo isset($this->heartBeatInterval) ? $this->heartBeatInterval : "60000"; ?>);

		
		$('.friendlist_bottom_circle_name').live('click', function(){
			var _id = $(this).attr('id');
//			alert(_id);
			
			// dari _id, dapatkan id div yg harus di slideToggle
			var circle_name = $.trim(_id.substring(4));
//			alert(circle_name);
			$('#content_' + circle_name).slideToggle('slow');
			
		});
		
		
		$('.friendlist_bottom_item').live('click', function(){
			var friend_fullname = $(this).html();
//			alert("Lets chat with " + $(this).html());

			var _id = $(this).attr('id');
			//alert(_id);	// item_4e1df5f8c1b4bad817000001
			
			var chatwindowexists = $('#chatwindow' + _id).length > 0;
			if(!chatwindowexists){
				text = "<div class='chatwindow shadow' id='chatwindow"+ _id +"'>";
				text += "<div class='chatwindowtitle' id='chatwindowtitle"+ _id +"'><div style='text-align:center; float:left; width:170px; height:20px; overflow:hidden;'>"+friend_fullname+"</div><div title='Message Archive' class='bottom_message_archive' id='bottommessagearchive"+ _id +"'>&nbsp;</div><div title='Hide Chat Window' class='toggle_item_chat' id='showhidechatwindowcontent"+ _id +"'>&nbsp;</div></div>";
				text += "<div class='chatwindowcontent transparent_70' id='chatwindowcontent"+ _id +"'></div>";
				text += "<div class='chatwindowtext transparent_70' id='chatwindowtext"+ _id +"'><textarea class='chattextarea' id='chattextarea"+ _id +"' maxlength='200'></textarea></div>";
				text += "</div>";
				
				$('.chatarea').append(text);
			} else {
				$('#chatwindow' + _id).show();
			}

			$.post("<?php echo $this->basepath; ?>ui/user/displayconfig", {key: 'showchatwindow' + _id, val: '1'}, function(data){
//				alert(data);
			});


		});
		
		$('.bottom_message_archive').live('click', function(){
			var _id = $(this).attr('id');
			
			// alert(_id);	// showhidechatwindowcontentitem_4e1df5f8c1b4bad817000001
			
			var _id_split = _id.split('_');
		});
		
		$('.toggle_item_chat').live('click', function(){
			var _id = $(this).attr('id');
			
//			alert(_id);	// showhidechatwindowcontentitem_4e1df5f8c1b4bad817000001
			
			var _id_split = _id.split('_');
			$('#chatwindowitem_' + _id_split[1]).hide();
			
//			var _id_to_toggle = _id.substring(8)
//			alert(_id_to_toggle);
//			$('#' + _id_to_toggle).slideToggle();
//			alert("key: " + 'showchatwindow' + 'item_' + _id_split[1]);
			$.post("<?php echo $this->basepath; ?>ui/user/displayconfig", {key: 'showchatwindow' + 'item_' + _id_split[1], val: '0'}, function(data){
//				alert(data);
			});

			
		});
		
		$('.chattextarea').live('keypress', function(event){
			var _id = $(this).attr('id');	// chattextareaitem_4e1df5f8c1b4bad817000001
			var _id_split = _id.split('_');
			
			// manipulasi chatwindowcontentitem_4e1df5f8c1b4bad817000001
			
			
			if(event.which == 13){
				var text = $.trim($(this).val());
				if(text != ''){
//					text = "<strong>You: </strong>" + text;
					$.post("<?php echo $this->basepath; ?>friend/user/submitchat", {friend_id:_id_split[1], text:text}, function(data){
//						$('#chatwindowcontentitem_' + _id_split[1]).html('<br>' + data);
					});
				}
				
				$(this).val('');
				event.preventDefault();
				
				
			}
		});
	
		function loadChat(){	// me-reload semua class .chatwindowcontent setiap x.x detik
			$('.chatwindowcontent').each(function(){
				var _id = $(this).attr('id');
				// alert(_id);	// chatwindowcontentitem_4e1df5f8c1b4bad817000001
				var _id_split = _id.split('_');
				// alert(_id_split[1]);
				
				var oldscrollHeight = $(this)[0].scrollHeight;
//				$.post("<?php echo $this->basepath; ?>friend/user/submitchat", {friend_id: _id_split[1]}, function(data){
//					$('#' + _id).html(data);
//				});

				$.ajax({
					url: "<?php echo $this->basepath; ?>friend/user/getchat/" + _id_split[1],
					cache: false,
					success: function(html){
						$('#' + _id).html(html);
						var newscrollHeight = $('#' + _id)[0].scrollHeight;
						if(newscrollHeight > oldscrollHeight){
							$('#' + _id).animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
						}
					}
					
					
				});

				
			});

		}
		
		setInterval(loadChat, 2000);
		
/*		
		function loadLog(){		
			var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
			$.ajax({
				url: "log.html",
				cache: false,
				success: function(html){		
					$("#chatbox").html(html); //Insert chat log into the #chatbox div				
					var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
					if(newscrollHeight > oldscrollHeight){
						$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
					}				
					},
			});
		}
		setInterval (loadLog, 2500);	//Reload file every 2.5 seconds
*/
		
		
	});

</script>




<div class="bottom_toolbar withjs">

<?php
// sementara untuk player dulu, admin belum
	if(isset($_SESSION['user_id'])){
?>

<div class='chatarea withjs'>
	
</div>

<div class='friendlist_bottom transparent_70 shadow withjs'>
	<!--[*
	<div class='friendlist_bottom_header'>
		<div class='friendlist_bottom_title'>
			Friend List
		</div>
	</div>
  *]-->
	
	<div class='friendlist_bottom_content'>
		
	</div>
</div>

<div class="bottom_toolbar_item friendlist_bottom_header withjs" style="float:left; width:138px; height:100%; text-align:center; padding:5px 10px; cursor:pointer; font-weight:bold;">Friend List</div>
<div class="bottom_toolbar_item withjs" style="float:left; width:3px; height:100%; background-image:url(<?php print($this->basepath); ?>tide/popbloop.img/bottom.toolbar.divider.png);"></div>

<?php

/*
<div class="bottom_toolbar">
	<div class="bottom_toolbar_item" style="float:left; width:100px; height:100%; text-align:center"></div>
	<div class="bottom_toolbar_item" style="float:left; width:3px; height:100%; background-image:url(<?php print($this->basepath); ?>tide/popbloop.img/bottom.toolbar.divider.png);"></div>
	<div class="bottom_toolbar_item" style="float:left; width:100px; height:100%; text-align:center"></div>
	<div class="bottom_toolbar_item" style="float:left; width:3px; height:100%; background-image:url(<?php print($this->basepath); ?>tide/popbloop.img/bottom.toolbar.divider.png);"></div>
	<div class="bottom_toolbar_item" style="float:left; width:100px; height:100%; text-align:center"></div>
</div>
*/


	}
?>
	
</div>
<?php
}
?>
